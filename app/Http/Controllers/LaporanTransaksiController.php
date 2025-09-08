<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LaporanTransaksiController extends Controller
{
    public function index()
    {
        return view('laporan.index');
    }

    public function load(Request $request)
    {
        try {
            $withdraws = DB::table('transactions as w')
                ->join('courses as u', 'w.course_id', '=', 'u.id')
                ->join('users as s', 'w.user_id', '=', 's.id')
                ->select([
                    'w.id',
                    'u.title',
                    's.name',
                    'w.amount',
                    'w.status',
                    'w.payment_channel',
                    'w.created_at'
                ])
                ->orderBy('w.created_at', 'desc');
            if ($request->status) {
                $withdraws->where('w.status', $request->status);
            }
            return DataTables::of($withdraws)
                ->addIndexColumn()
                ->editColumn('amount', function ($row) {
                    return 'Rp ' . number_format($row->amount, 0, ',', '.');
                })
                ->editColumn('status', function ($row) {
                    $class = match ($row->status) {
                        'PAID' => 'success',
                        'PENDING' => 'warning',
                        'FAILED' => 'danger',
                        default => 'warning',
                    };
                    return '<span class="badge bg-' . $class . '">' . ucfirst($row->status) . '</span>';
                })
                ->rawColumns(['status'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Error loading withdraw data: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
