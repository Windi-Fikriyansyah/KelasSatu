<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WithdrawController extends Controller
{
    public function index()
    {
        return view('withdraw.index');
    }

    public function load(Request $request)
    {
        try {
            $withdraws = DB::table('withdrawals as w')
                ->join('users as u', 'w.user_id', '=', 'u.id')
                ->join('bank as b', 'w.bank_id', '=', 'b.id')
                ->select([
                    'w.id',
                    'u.name as user_name',
                    'b.nama_bank as bank_name',
                    'b.nama_pemilik',
                    'b.no_rekening',
                    'w.amount',
                    'w.status',
                    'w.notes',
                    'w.created_at'
                ])
                ->orderBy('w.created_at', 'desc');

            return DataTables::of($withdraws)
                ->addIndexColumn()
                ->editColumn('amount', function ($row) {
                    return 'Rp ' . number_format($row->amount, 0, ',', '.');
                })
                ->editColumn('status', function ($row) {
                    $class = match ($row->status) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'warning',
                    };
                    return '<span class="badge bg-' . $class . '">' . ucfirst($row->status) . '</span>';
                })
                ->editColumn('notes', function ($row) {
                    return $row->notes ? $row->notes : '-';
                })
                ->filterColumn('user_name', function ($query, $keyword) {
                    $query->whereRaw("LOWER(u.name) like ?", ["%" . strtolower($keyword) . "%"]);
                })
                ->filterColumn('bank_name', function ($query, $keyword) {
                    $query->whereRaw("LOWER(b.nama_bank) like ?", ["%" . strtolower($keyword) . "%"]);
                })
                ->addColumn('action', function ($row) {
                    $approveUrl = route('withdraw.approve', $row->id);
                    $rejectUrl = route('withdraw.reject', $row->id);

                    $buttons = '<div class="btn-group" role="group">';
                    if ($row->status == 'pending') {
                        $buttons .= '<button class="btn btn-sm btn-success approve-btn" data-id="' . $row->id . '" data-url="' . $approveUrl . '"><i class="bi bi-check-circle"></i> Approve</button>';
                        $buttons .= '<button class="btn btn-sm btn-danger reject-btn" data-id="' . $row->id . '" data-url="' . $rejectUrl . '"><i class="bi bi-x-circle"></i> Reject</button>';
                    }
                    $buttons .= '</div>';
                    return $buttons;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Error loading withdraw data: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function approve($id)
    {
        DB::table('withdrawals')->where('id', $id)->update([
            'status' => 'approved',
            'updated_at' => now()
        ]);

        return response()->json(['success' => true, 'message' => 'Withdraw berhasil di-approve']);
    }

    public function reject($id)
    {
        DB::table('withdrawals')->where('id', $id)->update([
            'status' => 'rejected',
            'updated_at' => now()
        ]);

        return response()->json(['success' => true, 'message' => 'Withdraw ditolak']);
    }
}
