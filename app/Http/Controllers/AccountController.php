<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function index()
    {
        return view('pengaturan.index');
    }

    public function mutasi()
    {
        $userId = Auth::id();

        // Ambil hanya data penarikan saldo
        $withdrawals = DB::table('withdrawals')
            ->join('bank', 'withdrawals.bank_id', '=', 'bank.id')
            ->select(
                'withdrawals.id',
                'withdrawals.amount',
                'withdrawals.created_at',
                DB::raw("'Penarikan Saldo' as type"),
                DB::raw("'out' as transaction_type"),
                DB::raw("CONCAT('Penarikan ke ', bank.nama_bank, ' (', bank.no_rekening, ')') as description"),
                'withdrawals.status'
            )
            ->where('withdrawals.user_id', $userId)
            ->orderBy('withdrawals.created_at', 'desc')
            ->get();

        // Hitung statistik khusus penarikan
        $totalIn = 0;
        $totalOut = $withdrawals->sum('amount');
        $currentBalance = Auth::user()->balance;

        return view('pengaturan.mutasi', [
            'totalIn' => $totalIn,
            'totalOut' => $totalOut,
            'currentBalance' => $currentBalance,
        ]);
    }

    public function load(Request $request)
    {
        $userId = Auth::id();

        $query = DB::table('withdrawals')
            ->join('bank', 'withdrawals.bank_id', '=', 'bank.id')
            ->where('withdrawals.user_id', $userId)
            ->select(
                'withdrawals.id',
                'withdrawals.amount',
                'withdrawals.created_at',
                DB::raw("CONCAT('Penarikan ke ', bank.nama_bank, ' (', bank.no_rekening, ')') as description"),
                'withdrawals.status'
            );

        // Filter status
        if ($request->status) {
            $query->where('withdrawals.status', $request->status);
        }

        // Filter tanggal
        if ($request->startDate) {
            $query->whereDate('withdrawals.created_at', '>=', $request->startDate);
        }
        if ($request->endDate) {
            $query->whereDate('withdrawals.created_at', '<=', $request->endDate);
        }

        return DataTables::of($query)
            ->filterColumn('description', function ($query, $keyword) {
                $sql = "CONCAT('Penarikan ke ', bank.nama_bank, ' (', bank.no_rekening, ')') like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            })
            ->addColumn('date_formatted', function ($row) {
                return \Carbon\Carbon::parse($row->created_at)->format('d M Y H:i');
            })
            ->addColumn('amount_formatted', function ($row) {
                return '- Rp ' . number_format($row->amount, 0, ',', '.');
            })
            ->addColumn('status_badge', function ($row) {
                $color = match ($row->status) {
                    'completed' => 'green',
                    'pending'   => 'yellow',
                    'failed'    => 'red',
                    default     => 'gray',
                };
                return '<span class="px-2 py-1 text-xs font-medium rounded-full bg-' . $color . '-100 text-' . $color . '-800">'
                    . ucfirst($row->status) . '</span>';
            })
            ->rawColumns(['status_badge'])
            ->make(true);
    }


    public function withdrawal()
    {
        $userId = Auth::id();

        $userBanks = DB::table('bank')
            ->where('id_user', $userId)
            ->get();

        // Ambil riwayat penarikan
        $recentWithdrawals = DB::table('withdrawals')
            ->join('bank', 'withdrawals.bank_id', '=', 'bank.id')
            ->select(
                'withdrawals.*',
                'bank.nama_bank',
                'bank.no_rekening',
                'bank.nama_pemilik'
            )
            ->where('withdrawals.user_id', Auth::id())
            ->orderBy('withdrawals.created_at', 'desc')
            ->get();


        return view('pengaturan.penarikan', compact('userBanks', 'recentWithdrawals'));
    }

    public function withdrawalProcess(Request $request)
    {
        $userId = Auth::id();

        $request->validate([
            'bank_id' => 'required|exists:bank,id',
            'amount'  => 'required|integer|min:250000',
        ]);

        // Ambil saldo user
        $user = DB::table('users')->where('id', $userId)->first();

        if ($request->amount > $user->balance) {
            return back()->withErrors(['amount' => 'Jumlah melebihi saldo tersedia.']);
        }

        // Kurangi saldo
        DB::table('users')->where('id', $userId)->update([
            'balance' => $user->balance - $request->amount,
        ]);

        // Simpan request penarikan
        DB::table('withdrawals')->insert([
            'user_id'       => $userId,
            'bank_id'       => $request->bank_id,
            'amount'        => $request->amount,
            'status'        => 'pending',
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        return redirect()->route('account.index')->with('success', 'Penarikan berhasil diajukan.');
    }

    public function bank()
    {
        $userId = Auth::id();
        $banks = DB::table('bank')->where('id_user', $userId)->get(); // ambil data bank
        return view('pengaturan.atur_bank', compact('banks'));
    }
    public function bankJson()
    {
        $userId = Auth::id();
        $banks = DB::table('bank')->where('id_user', $userId)->get();
        return response()->json($banks);
    }

    public function saveBank(Request $request)
    {
        $userId = Auth::id();

        try {
            $request->validate([
                'nama_bank' => 'required|string|max:255',
                'no_rekening' => 'required|string|max:50',
                'nama_pemilik' => 'required|string|max:255',
            ]);

            $data = [
                'id_user' => $userId,
                'nama_bank' => $request->nama_bank,
                'no_rekening' => $request->no_rekening,
                'nama_pemilik' => $request->nama_pemilik,
                'updated_at' => now(),
            ];

            if ($request->bank_id) {
                DB::table('bank')->where('id', $request->bank_id)->where('id_user', $userId)->update($data);
                $message = 'Rekening bank berhasil diperbarui!';
            } else {
                $data['created_at'] = now();
                DB::table('bank')->insert($data);
                $message = 'Rekening bank berhasil ditambahkan!';
            }

            return response()->json(['success' => true, 'message' => $message]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
            ], 500);
        }
    }




    public function deleteBank(Request $request)
    {
        $userId = Auth::id();

        DB::table('bank')->where('id', $request->id)->where('id_user', $userId)->delete();

        return response()->json(['success' => true, 'message' => 'Rekening bank berhasil dihapus!']);
    }
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $messages = [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.string' => 'Nama lengkap harus berupa teks.',
            'name.max' => 'Nama lengkap maksimal 255 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah digunakan.',
            'no_hp.string' => 'Nomor telepon harus berupa teks.',
            'no_hp.max' => 'Nomor telepon maksimal 20 karakter.',
        ];

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'no_hp' => 'nullable|string|max:20',
        ], $messages);
        $user->update($request->only('name', 'email', 'no_hp'));

        return redirect()->back()->with('success', 'Profil berhasil diperbarui!');
    }

    public function updatePassword(Request $request)
    {
        $messages = [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.confirmed' => 'Konfirmasi password tidak sesuai.',
        ];

        $request->validate([
            'current_password' => ['required'],
            'new_password' => ['required', 'confirmed', Password::defaults()],
        ], $messages);
        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->back()->with('success', 'Password berhasil diperbarui!');
    }
}
