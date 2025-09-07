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
            $users = User::whereIn('role', ['guru', 'siswa'])
                ->select([
                    'id',
                    'name',
                    'no_hp',
                    'email',
                    'kabupaten',
                    'kota',
                    'instansi',
                    'created_at'
                ])
                ->orderBy('created_at', 'desc');

            return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('action', function ($row) use ($currentUserId) {
                    $encryptedId = Crypt::encrypt($row->id);
                    $deleteUrl = route('pengguna.destroy', $row->id);

                    $buttons = '<div class="btn-group" role="group">';

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
}
