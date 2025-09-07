<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function dashboardOwner(Request $request)
    {
        // Hitung jumlah siswa
        $jumlahSiswa = DB::table('users')->where('role', 'siswa')->count();

        // Hitung jumlah guru
        $jumlahGuru = DB::table('users')->where('role', 'guru')->count();

        // Hitung jumlah transaksi dengan status paid
        $jumlahTransaksiPaid = DB::table('transactions')->where('status', 'PAID')->count();

        // Hitung total amount (misal fieldnya 'amount')
        $totalAmount = DB::table('transactions')->where('status', 'PAID')->sum('amount');

        return view('dashboardOwner', compact(
            'jumlahSiswa',
            'jumlahGuru',
            'jumlahTransaksiPaid',
            'totalAmount'
        ));
    }
    public function dashboardUser(Request $request)
    {
        $courses = DB::table('courses')
            ->join('kategori', 'courses.id_kategori', '=', 'kategori.id')
            ->select('courses.id', 'courses.title', 'kategori.id as kategori_id', 'courses.description', 'courses.thumbnail', 'courses.price', 'courses.features', 'kategori.nama_kategori')
            ->orderBy('courses.created_at', 'desc')
            ->get();
        $kategori = DB::table('kategori')->get();

        return view('dashboardUser', compact('courses', 'kategori'));
    }
}
