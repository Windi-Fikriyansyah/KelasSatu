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
use Illuminate\Support\Facades\Storage;

class KategoriController extends Controller
{
    public function index()
    {
        return view('kategori.index');
    }

    public function load(Request $request)
    {
        try {
            $kategori = DB::table('kategori')
                ->select([
                    'id',
                    'nama_kategori',
                ]);

            return DataTables::of($kategori)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $encryptedId = Crypt::encrypt($row->id);
                    $deleteUrl = route('kategori.destroy', $row->id);

                    $buttons = '<div class="btn-group" role="group">';
                    $buttons .= '<a href="' . route('kategori.edit', $row->id) . '" class="btn btn-sm btn-warning me-1" title="Edit"><i class="bi bi-pencil"></i></a>';
                    $buttons .= '<button class="btn btn-sm btn-danger delete-btn" title="Hapus" data-id="' . $row->id . '" data-url="' . $deleteUrl . '"><i class="bi bi-trash"></i></button>';
                    $buttons .= '</div>';

                    return $buttons;
                })
                ->rawColumns(['action'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Error loading kategori data: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            // Hapus data kategori langsung dari database
            $deleted = DB::table('kategori')->where('id', $id)->delete();

            if ($deleted) {
                return response()->json([
                    'success' => true,
                    'message' => 'kategori berhasil dihapus'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'kategori tidak ditemukan'
                ], 404);
            }
        } catch (\Exception $e) {
            Log::error('Error deleting kategori: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }


    public function create()
    {
        return view('kategori.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
        ]);

        try {

            DB::table('kategori')->insert([
                'nama_kategori' => $request->nama_kategori,
            ]);

            return redirect()->route('kategori.index')
                ->with('success', 'kategori berhasil dibuat');
        } catch (\Exception $e) {
            Log::error('Error creating course: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $course = DB::table('kategori')->where('id', $id)->first();
        return view('kategori.create', compact('course'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
        ]);

        try {
            $course = DB::table('kategori')->where('id', $id)->first();

            if (!$course) {
                return back()->with('error', 'kategori tidak ditemukan');
            }



            $dataUpdate = [
                'nama_kategori' => $request->nama_kategori,
            ];



            // Update course using Query Builder
            DB::table('kategori')->where('id', $id)->update($dataUpdate);

            return redirect()->route('kategori.index')
                ->with('success', 'kategori berhasil diperbarui');
        } catch (\Exception $e) {
            Log::error('Error updating course: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
