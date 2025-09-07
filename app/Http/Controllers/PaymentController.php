<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

// Import Xendit classes correctly
use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;
use Xendit\Invoice\CreateInvoiceRequest;
use Xendit\Invoice\InvoiceItem;
use Xendit\Invoice\CustomerObject;

class PaymentController extends Controller
{
    public function createPayment($encryptedCourseId)
    {
        try {
            $courseId = Crypt::decryptString($encryptedCourseId);
            $course = DB::table('courses')->where('id', $courseId)->first();
            $user = Auth::user();
            $referralCode = request()->query('referral_code');


            // Cek apakah user sudah memiliki akses ke course ini
            $hasAccess = DB::table('enrollments')
                ->where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->exists();

            if ($hasAccess) {
                return redirect()->route('course.show', $encryptedCourseId)
                    ->with('info', 'Anda sudah memiliki akses ke kursus ini.');
            }

            // Jika course gratis, langsung berikan akses
            if ($course->is_free) {
                return $this->grantFreeAccess($user->id, $course->id, $encryptedCourseId);
            }

            $existingTransaction = DB::table('transactions')
                ->where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->where('status', 'PENDING')
                ->where('expired_at', '>', now())
                ->first();

            if ($existingTransaction) {
                // Jika ada transaksi pending, redirect ke invoice URL yang sama
                $invoiceData = json_decode($existingTransaction->xendit_data, true);
                return redirect($invoiceData['invoice_url'])
                    ->with('info', 'Anda memiliki pembayaran yang masih pending. Lanjutkan pembayaran.');
            }

            // Configure Xendit with API Key
            Configuration::setXenditKey(env('XENDIT_API_KEY'));

            // Generate external ID yang unik
            $externalId = 'course-' . $course->id . '-' . $user->id . '-' . time();

            // Hitung expired date (24 jam dari sekarang)
            $expiredAt = Carbon::now()->addHours(24);

            // Create invoice items
            $invoiceItem = new InvoiceItem([
                'name' => $course->title,
                'quantity' => 1,
                'price' => (float)$course->price,
                'category' => $course->nama_kategori ?? 'Course'
            ]);

            // Create customer object
            $customer = new CustomerObject([
                'given_names' => $user->name,
                'email' => $user->email,
            ]);

            // Create invoice request
            $createInvoiceRequest = new CreateInvoiceRequest([
                'external_id' => $externalId,
                'payer_email' => $user->email,
                'description' => 'Pembayaran Kursus: ' . $course->title,
                'amount' => (float)$course->price,
                'invoice_duration' => 86400, // 24 jam dalam detik
                'success_redirect_url' => route('payment.success', $encryptedCourseId),
                'failure_redirect_url' => route('payment.failed', $encryptedCourseId),
                'currency' => 'IDR',
                'items' => [$invoiceItem],
                'customer' => $customer
            ]);

            // Create invoice using the API
            $apiInstance = new InvoiceApi();
            $invoice = $apiInstance->createInvoice($createInvoiceRequest);

            // Simpan data transaksi ke database
            DB::table('transactions')->insert([
                'external_id' => $externalId,
                'invoice_id' => $invoice['id'],
                'user_id' => $user->id,
                'course_id' => $course->id,
                'referral_code' => $referralCode,
                'amount' => $course->price,
                'status' => 'PENDING',
                'expired_at' => $expiredAt,
                'xendit_data' => json_encode($invoice),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Log::info('Invoice created', [
                'external_id' => $externalId,
                'invoice_id' => $invoice['id'],
                'user_id' => $user->id,
                'course_id' => $course->id,
                'amount' => $course->price
            ]);

            return redirect($invoice['invoice_url']);
        } catch (\Exception $e) {
            Log::error('Payment creation failed', [
                'error' => $e->getMessage(),
                'course_id' => $courseId ?? null,
                'user_id' => Auth::id()
            ]);

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat membuat pembayaran. Silakan coba lagi.');
        }
    }

    private function grantFreeAccess($userId, $courseId, $encryptedCourseId)
    {
        try {
            // Berikan akses gratis
            DB::table('enrollments')->insert([
                'user_id' => $userId,
                'course_id' => $courseId,
                'enrolled_at' => now()
            ]);

            // Simpan transaksi gratis untuk tracking
            DB::table('transactions')->insert([
                'external_id' => 'free-' . $courseId . '-' . $userId . '-' . time(),
                'invoice_id' => 'FREE-' . time(),
                'user_id' => $userId,
                'course_id' => $courseId,
                'amount' => 0,
                'status' => 'PAID',
                'payment_method' => 'FREE',
                'paid_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->route('course.show', $encryptedCourseId)
                ->with('success', 'Selamat! Anda berhasil mendapatkan akses gratis ke kursus ini.');
        } catch (\Exception $e) {
            Log::error('Free access grant failed', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
                'course_id' => $courseId
            ]);

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memberikan akses. Silakan coba lagi.');
        }
    }

    public function callback(Request $request)
    {
        try {
            // Verifikasi callback token dari Xendit
            $callbackToken = $request->header('x-callback-token');
            if ($callbackToken !== env('XENDIT_CALLBACK_TOKEN')) {
                Log::warning('Invalid callback token', ['token' => $callbackToken]);
                return response()->json(['status' => 'invalid token'], 400);
            }

            $data = $request->all();
            Log::info('Xendit callback received', $data);

            $externalId = $data['external_id'] ?? null;
            $invoiceId = $data['id'] ?? null;
            $status = $data['status'] ?? null;

            if (!$externalId || !$invoiceId) {
                Log::error('Missing external_id or invoice_id in callback');
                return response()->json(['status' => 'missing data'], 400);
            }

            // Ambil data transaksi dari database
            $transaction = DB::table('transactions')
                ->where('external_id', $externalId)
                ->where('invoice_id', $invoiceId)
                ->first();

            if (!$transaction) {
                Log::error('Transaction not found', ['external_id' => $externalId]);
                return response()->json(['status' => 'transaction not found'], 404);
            }

            // Update status transaksi
            $updateData = [
                'status' => strtoupper($status),
                'xendit_data' => json_encode($data),
                'updated_at' => now(),
            ];

            if ($status === 'PAID') {
                $updateData['paid_at'] = Carbon::parse($data['paid_at'] ?? now());
                $updateData['payment_method'] = $data['payment_method'] ?? null;
                $updateData['payment_channel'] = $data['payment_channel'] ?? null;
            }

            DB::table('transactions')
                ->where('id', $transaction->id)
                ->update($updateData);

            // Jika pembayaran berhasil, berikan akses ke course
            if ($status === 'PAID') {
                $this->grantCourseAccess($transaction->user_id, $transaction->course_id, $transaction->status);

                Log::info('Payment successful and access granted', [
                    'transaction_id' => $transaction->id,
                    'user_id' => $transaction->user_id,
                    'course_id' => $transaction->course_id
                ]);
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Callback processing failed', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return response()->json(['status' => 'error'], 500);
        }
    }

    private function grantCourseAccess($userId, $courseId, $status)
    {
        try {
            $enrollment = DB::table('enrollments')
                ->where('user_id', $userId)
                ->where('course_id', $courseId)
                ->first();

            if ($enrollment) {
                // Update payment_status jika sudah ada
                DB::table('enrollments')
                    ->where('id', $enrollment->id)
                    ->update([
                        'payment_status' => $status,
                        'enrolled_at' => now(),
                    ]);
            } else {
                // Insert baru jika belum ada
                DB::table('enrollments')->insert([
                    'user_id' => $userId,
                    'course_id' => $courseId,
                    'payment_status' => $status,
                    'enrolled_at' => now(),
                ]);
            }

            $transaction = DB::table('transactions')
                ->where('user_id', $userId)
                ->where('course_id', $courseId)
                ->latest()
                ->first();

            if ($transaction && !empty($transaction->referral_code)) {
                $referralUser = DB::table('users')
                    ->where('referral_code', $transaction->referral_code)
                    ->first();

                if ($referralUser && $referralUser->id != $userId) {
                    // Ambil persentase komisi dari tabel komisi
                    $commissionRate = DB::table('komisi')->value('persentase') ?? 10; // default 10%

                    $commissionAmount = ($commissionRate / 100) * $transaction->amount;

                    DB::table('users')
                        ->where('id', $referralUser->id)
                        ->increment('balance', $commissionAmount);
                    DB::table('referral_commissions')->insert([
                        'user_id' => $referralUser->id,
                        'referred_user_id' => $userId,
                        'transaction_id' => $transaction->id,
                        'course_id' => $courseId,
                        'amount' => $commissionAmount,
                        'percentage' => $commissionRate,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to grant course access', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
                'course_id' => $courseId
            ]);
        }
    }


    public function success($encryptedCourseId)
    {
        return redirect()->route('course.show', $encryptedCourseId)
            ->with('success', 'Pembayaran berhasil! Anda sekarang memiliki akses ke kursus ini.');
    }

    public function failed($encryptedCourseId)
    {
        return redirect()->route('course.show', $encryptedCourseId)
            ->with('error', 'Pembayaran gagal atau dibatalkan. Silakan coba lagi.');
    }
}
