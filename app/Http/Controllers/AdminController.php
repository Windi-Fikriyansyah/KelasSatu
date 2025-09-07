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

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.index');
    }

    public function load(Request $request)
    {
        try {
            $currentUserId = auth()->id();

            // Mengambil data admin dengan role guru atau siswa
            $users = User::whereIn('role', ['admin', 'owner'])
                ->select([
                    'id',
                    'name',
                    'no_hp',
                    'email',
                    'role',
                    'created_at'
                ])
                ->orderBy('created_at', 'desc');

            return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('action', function ($row) use ($currentUserId) {
                    $encryptedId = Crypt::encrypt($row->id);
                    $editUrl = route('admin.edit', $encryptedId);
                    $deleteUrl = route('admin.destroy', $row->id);

                    $buttons = '<div class="btn-group" role="group">';
                    $buttons .= '<a href="' . $editUrl . '" class="btn btn-sm btn-info me-1" title="Edit"><i class="bi bi-pencil-square"></i></a>';
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


    public function create()
    {
        return view('admin.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|string|email|max:255|unique:users',
            'no_hp'     => 'nullable|string|max:20',
            'password'  => 'required|string|min:8|confirmed',
            'role'      => 'required|in:admin,owner'
        ]);

        try {
            User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'no_hp'    => $request->no_hp,
                'password' => Hash::make($request->password),
                'role'     => $request->role,
            ]);

            return redirect()->route('admin.index')->with('success', 'Admin berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan admin: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            $user = User::findOrFail($decryptedId);
            return view('admin.create', compact('user'));
        } catch (\Exception $e) {
            return redirect()->route('admin.index')->with('error', 'Admin tidak ditemukan');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|string|email|max:255|unique:users,email,' . $id,
            'no_hp'     => 'nullable|string|max:20',
            'password'  => 'nullable|string|min:8|confirmed',
            'role'      => 'required|in:admin,owner'
        ]);

        try {
            $user = User::findOrFail($id);

            $data = [
                'name'  => $request->name,
                'email' => $request->email,
                'no_hp' => $request->no_hp,
                'role'  => $request->role,
            ];

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $user->update($data);

            return redirect()->route('admin.index')->with('success', 'Admin berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui admin: ' . $e->getMessage());
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
