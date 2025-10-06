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
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    public function index()
    {
        return view('user.index');
    }

    public function load(Request $request)
    {
        try {
            $currentUserId = auth()->id();

            // Mengambil data pengguna dengan role guru atau siswa
            $users = User::where('role', 'user')
                ->select([
                    'id',
                    'name',
                    'no_hp',
                    'email',
                    'kabupaten',
                    'provinsi',
                    'instansi',
                    'status',
                    'created_at'
                ])
                ->orderBy('created_at', 'desc');

            return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('action', function ($row) use ($currentUserId) {
                    $encryptedId = Crypt::encrypt($row->id);
                    $deleteUrl = route('pengguna.destroy', $row->id);

                    $isActive = $row->status == 1;

                    $checked = $isActive ? 'checked' : '';
                    $label = $isActive ? 'Aktif' : 'Nonaktif';
                    $labelClass = $isActive ? 'text-success' : 'text-danger';

                    $buttons = '
        <div class="d-flex align-items-center gap-2">
            <div class="toggle-container d-flex align-items-center gap-1">
                <div class="form-check form-switch m-0">
                    <input type="checkbox" class="form-check-input toggle-status"
                           data-id="' . $row->id . '" ' . $checked . '>
                </div>
                <span class="status-label ' . $labelClass . '">' . $label . '</span>
            </div>';
                    if ($row->id != $currentUserId) {
                        $buttons .= '<button class="btn btn-sm btn-danger delete-btn" title="Hapus" data-id="' . $row->id . '" data-url="' . $deleteUrl . '"><i class="bi bi-trash"></i></button>';
                    }
                    $buttons .= '</div>';

                    return $buttons;
                })
                ->rawColumns(['action'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Error loading user data: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }


    public function toggleStatus(Request $request)
    {
        try {
            $user = User::findOrFail($request->id);
            $user->status = $request->status;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Status pengguna berhasil diperbarui.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating status: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Gagal memperbarui status pengguna.'
            ], 500);
        }
    }


    public function destroy($id)
    {
        try {
            $users = DB::table('users')->where('id', $id)->first();

            if (!$users) {
                return response()->json([
                    'error' => true,
                    'message' => 'users tidak ditemukan'
                ], 404);
            }

            DB::table('users')->where('id', $id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'users berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting users: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
    public function export()
    {
        return Excel::download(new UsersExport, 'pengguna.xlsx');
    }
}
