<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

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
            $course   = DB::table('courses')->where('id', $courseId)->first();
            $user     = Auth::user();
            $referralCode = request()->query('referral_code');

            // Cek apakah sudah punya akses
            $hasAccess = DB::table('enrollments')
                ->where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->exists();


            $unpaidTrx = DB::table('transactions')
                ->where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->whereIn('status', ['UNPAID', 'PENDING'])
                ->orderBy('created_at', 'desc')
                ->first();

            if ($unpaidTrx) {
                return redirect()->route('history.index')
                    ->with('toast', [
                        'type' => 'info',
                        'message' => 'Anda masih memiliki transaksi yang belum dibayar. Silakan selesaikan pembayaran di history.'
                    ]);
            }

            if ($hasAccess) {
                return redirect()->route('course.show', $encryptedCourseId)
                    ->with('info', 'Anda sudah memiliki akses ke kursus ini.');
            }

            if ($course->is_free) {
                return $this->grantFreeAccess($user->id, $course->id, $encryptedCourseId);
            }

            // --- Ambil daftar payment channel dari Tripay ---
            $apiKey = config('services.tripay.api_key');
            $url    = config('services.tripay.sandbox')
                ? 'https://tripay.co.id/api/merchant/payment-channel'
                : 'https://tripay.co.id/api/merchant/payment-channel';

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey
            ])->get($url);

            if (!$response->successful()) {
                throw new \Exception('Gagal mengambil channel pembayaran Tripay');
            }

            $channels = $response->json('data');


            return view('kursus.choose-channel', [
                'course' => $course,
                'channels' => $channels,
                'encryptedCourseId' => $encryptedCourseId,
                'referralCode' => $referralCode,
            ]);
        } catch (\Exception $e) {
            Log::error('Tripay Payment error', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return redirect()->back()->with('error', 'Terjadi kesalahan, silakan coba lagi.');
        }
    }


    public function processPayment(Request $request, $encryptedCourseId)
    {
        try {
            $courseId = Crypt::decryptString($encryptedCourseId);
            $course   = DB::table('courses')->where('id', $courseId)->first();
            $user     = Auth::user();

            if (!$course) {
                return redirect()->back()->with('error', 'Kursus tidak ditemukan.');
            }

            $unpaidTrx = DB::table('transactions')
                ->where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->whereIn('status', ['UNPAID', 'PENDING'])
                ->orderBy('created_at', 'desc')
                ->first();

            if ($unpaidTrx) {
                return redirect()->route('history.index')
                    ->with('toast', [
                        'type' => 'info',
                        'message' => 'Anda masih memiliki transaksi yang belum dibayar. Silakan selesaikan pembayaran di history.'
                    ]);
            }

            // Ambil channel yang dipilih user
            $channel = $request->input('channel');
            $referralCode = $request->input('referral_code');

            // Data transaksi
            $merchantRef = 'INV-' . $course->id . '-' . $user->id . '-' . time();
            $amount = (int) $course->price;

            $merchantCode = config('services.tripay.merchant_code');
            $privateKey   = config('services.tripay.private_key');
            $apiKey       = config('services.tripay.api_key');

            // Signature wajib
            $signature = hash_hmac('sha256', $merchantCode . $merchantRef . $amount, $privateKey);

            // Payload sesuai dokumentasi
            $payload = [
                'method'        => $channel,
                'merchant_ref'  => $merchantRef,
                'amount'        => $amount,
                'customer_name' => $user->name,
                'customer_email' => $user->email,
                'customer_phone' => $user->phone ?? '081234567890',
                'order_items'   => [
                    [
                        'sku'       => 'COURSE-' . $course->id,
                        'name'      => $course->title,
                        'price'     => $amount,
                        'quantity'  => 1,
                        'subtotal'  => $amount,
                    ]
                ],
                'callback_url'  => route('payment.callback'),
                'return_url'    =>  route('payment.redirect', $encryptedCourseId),
                'expired_time'  => now()->addDay()->timestamp,
                'signature'     => $signature,
            ];

            $url = config('services.tripay.urlcreatetripay')
                ? 'https://tripay.co.id/api/transaction/create'
                : 'https://tripay.co.id/api/transaction/create';

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey
            ])->post($url, $payload);

            if (!$response->successful()) {
                Log::error('Tripay transaction failed', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return redirect()->back()->with('error', 'Gagal membuat transaksi pembayaran.');
            }

            $result = $response->json();

            if (!isset($result['success']) || !$result['success']) {
                return redirect()->back()->with('error', $result['message'] ?? 'Gagal membuat transaksi.');
            }

            $data = $result['data'];

            $invoiceId = $data['reference'] ?? $merchantRef;
            // Simpan transaksi ke DB
            DB::table('transactions')->insert([
                'external_id'     => $merchantRef,
                'invoice_id'      => $invoiceId,
                'user_id'         => $user->id,
                'course_id'       => $course->id,
                'referral_code'   => $referralCode,
                'amount'          => $amount,
                'status'          => $data['status'],
                'expired_at'      => Carbon::createFromTimestamp($data['expired_time']),
                'payment_method'  => $data['payment_method'],
                'payment_channel' => $channel,
                'tripay_data'     => json_encode($data),
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
            Log::info('Transaction created', [
                'merchant_ref' => $merchantRef,
                'invoice_id' => $invoiceId,
                'user_id' => $user->id
            ]);

            return redirect($data['checkout_url']); // arahkan ke halaman pembayaran Tripay

        } catch (\Exception $e) {
            Log::error('processTransaction error', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'Terjadi kesalahan, silakan coba lagi.');
        }
    }

    // Tambahkan method ini di PaymentController

    public function redirectAfterPayment(Request $request, $encryptedCourseId)
    {
        try {
            Log::info('RedirectAfterPayment called with parameters:', [
                'all_query_params' => $request->query->all(),
                'encryptedCourseId' => $encryptedCourseId,
                'full_url' => $request->fullUrl()
            ]);
            $reference = $request->query('tripay_reference');

            if (!$reference) {
                return redirect()->route('course.show', $encryptedCourseId)
                    ->with('error', 'Parameter referensi pembayaran tidak ditemukan.');
            }

            $trx = DB::table('transactions')->where('invoice_id', $reference)->first();

            if (!$trx) {
                // Coba mencari dengan external_id sebagai fallback
                $trx = DB::table('transactions')->where('external_id', $reference)->first();

                if (!$trx) {
                    Log::warning('Transaksi tidak ditemukan', ['reference' => $reference]);
                    return redirect()->route('course.show', $encryptedCourseId)
                        ->with('error', 'Transaksi tidak ditemukan. Silakan hubungi admin dengan kode referensi: ' . $reference);
                }
            }

            // Periksa apakah user yang mengakses adalah pemilik transaksi
            if ($trx->user_id !== Auth::id()) {
                return redirect()->route('course.show', $encryptedCourseId)
                    ->with('error', 'Anda tidak memiliki akses ke transaksi ini.');
            }

            if ($trx->status === 'PAID') {
                return $this->success($encryptedCourseId);
            } elseif (in_array($trx->status, ['UNPAID', 'PENDING'])) {
                return redirect()->route('course.show', $encryptedCourseId)
                    ->with('info', 'Pembayaran Anda masih menunggu konfirmasi. Silakan selesaikan pembayaran.');
            } else {
                return $this->failed($encryptedCourseId);
            }
        } catch (\Exception $e) {
            Log::error('Redirect after payment error', [
                'error' => $e->getMessage(),
                'reference' => $request->query('reference')
            ]);

            return redirect()->route('course.show', $encryptedCourseId)
                ->with('error', 'Terjadi kesalahan sistem. Silakan hubungi admin.');
        }
    }

    public function callback(Request $request)
    {
        try {
            $privateKey = config('services.tripay.private_key');

            // Ambil raw body JSON (harus asli)
            $json = $request->getContent();

            // Buat signature sendiri
            $signature = hash_hmac('sha256', $json, $privateKey);

            // Cocokkan dengan header dari Tripay
            if ($request->header('X-Callback-Signature') !== $signature) {
                Log::warning('Invalid callback signature', [
                    'received' => $request->header('X-Callback-Signature'),
                    'expected' => $signature,
                    'body' => $json,
                ]);
                return response()->json(['success' => false, 'message' => 'Invalid signature'], 403);
            }

            $data = json_decode($json, true);

            // Validasi event
            if ($request->header('X-Callback-Event') !== 'payment_status') {
                return response()->json(['success' => false, 'message' => 'Invalid event'], 400);
            }

            // Ambil data transaksi dari DB berdasarkan reference
            $trx = DB::table('transactions')->where('invoice_id', $data['reference'])->first();

            if (!$trx) {
                Log::error('Callback transaksi tidak ditemukan', ['reference' => $data['reference']]);
                return response()->json(['success' => false], 404);
            }

            // Update status transaksi sesuai dari Tripay
            DB::table('transactions')
                ->where('id', $trx->id)
                ->update([
                    'status' => $data['status'],
                    'paid_at' => isset($data['paid_at']) ? Carbon::createFromTimestamp($data['paid_at']) : null,
                    'updated_at' => now(),
                ]);

            // Bisa juga update progress user (misalnya beri akses kursus kalau PAID)
            if ($data['status'] === 'PAID') {

                $this->grantCourseAccess($trx->user_id, $trx->course_id, 'PAID');
            }

            // Tripay hanya menganggap sukses kalau balas {"success": true}
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Callback error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Server error'], 500);
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
        $courseId = Crypt::decryptString($encryptedCourseId);
        $trx = DB::table('transactions')
            ->where('course_id', $courseId)
            ->where('user_id', Auth::id())
            ->latest()
            ->first();

        if ($trx && $trx->status === 'PAID') {
            return redirect()->route('course.show', $encryptedCourseId)
                ->with('success', 'Pembayaran berhasil! Anda sekarang memiliki akses ke kursus ini.');
        }

        return redirect()->route('course.show', $encryptedCourseId)
            ->with('error', 'Status pembayaran belum dikonfirmasi. Silakan tunggu atau hubungi admin.');
    }


    public function failed($encryptedCourseId)
    {
        return redirect()->route('course.show', $encryptedCourseId)
            ->with('error', 'Pembayaran gagal atau sudah kedaluwarsa. Silakan coba lagi.');
    }
}
