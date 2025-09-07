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

class ModuleController extends Controller
{
    public function index()
    {
        return view('module.index');
    }

    public function load(Request $request)
    {
        try {
            // Join dengan tabel courses untuk mendapatkan judul kursus
            $kursus = DB::table('course_modules as cm')
                ->join('courses as c', 'cm.course_id', '=', 'c.id')
                ->select([
                    'cm.id',
                    'c.title as title_kursus',
                    'cm.title',
                    'cm.order',
                    'cm.created_at',
                    'cm.updated_at'
                ])
                ->orderBy('cm.created_at', 'desc');

            return DataTables::of($kursus)
                ->addIndexColumn()
                ->filterColumn('title_kursus', function ($query, $keyword) {
                    $query->whereRaw("LOWER(c.title) like ?", ["%" . strtolower($keyword) . "%"]);
                })
                ->addColumn('action', function ($row) {
                    $encryptedId = Crypt::encrypt($row->id);
                    $deleteUrl = route('module.destroy', $row->id);

                    $buttons = '<div class="btn-group" role="group">';
                    $buttons .= '<a href="' . route('module.edit', $encryptedId) . '" class="btn btn-sm btn-warning me-1" title="Edit"><i class="bi bi-pencil"></i></a>';
                    $buttons .= '<button class="btn btn-sm btn-danger delete-btn" title="Hapus" data-id="' . $row->id . '" data-url="' . $deleteUrl . '"><i class="bi bi-trash"></i></button>';
                    $buttons .= '</div>';

                    return $buttons;
                })
                ->rawColumns(['action'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Error loading kursus data: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }


    public function destroy($id)
    {
        try {
            // Hapus data kursus langsung dari database
            $deleted = DB::table('course_modules')->where('id', $id)->delete();

            if ($deleted) {
                return response()->json([
                    'success' => true,
                    'message' => 'Kursus berhasil dihapus'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Kursus tidak ditemukan'
                ], 404);
            }
        } catch (\Exception $e) {
            Log::error('Error deleting kursus: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }


    public function create()
    {
        $courses = DB::table('courses')->get();
        return view('module.create', compact('courses'));
    }
    public function store(Request $request)
    {
        $messages = [
            'course_id.required' => 'Kursus harus dipilih.',
            'course_id.exists' => 'Kursus yang dipilih tidak valid.',
            'title.required' => 'Judul module wajib diisi.',
            'title.max' => 'Judul module maksimal :max karakter.',
            'description.required' => 'Deskripsi module wajib diisi.',
            'order.required' => 'Urutan module wajib diisi.',
            'order.integer' => 'Urutan module harus berupa angka.',
            'order.min' => 'Urutan module tidak boleh kurang dari :min.'
        ];

        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'order' => 'required|integer|min:0'
        ], $messages);
        try {
            // Insert module menggunakan Query Builder
            DB::table('course_modules')->insert([
                'course_id' => $request->course_id,
                'title' => $request->title,
                'description' => $request->description,
                'order' => $request->order,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return redirect()->route('module.index')
                ->with('success', 'Module berhasil dibuat');
        } catch (\Exception $e) {
            Log::error('Error creating module: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }


    public function edit($id)
    {

        try {
            $decryptedId = Crypt::decrypt($id);
        } catch (\Exception $e) {
            return redirect()->route('module.index')->with('error', 'ID tidak valid');
        }

        $module = DB::table('course_modules')->where('id', $decryptedId)->first();

        if (!$module) {
            return redirect()->route('module.index')->with('error', 'Materi tidak ditemukan');
        }

        $courses = DB::table('courses')->get();
        return view('module.create', compact('module', 'courses'));
    }

    public function update(Request $request, $id)
    {
        $messages = [
            'course_id.required' => 'Kursus harus dipilih.',
            'course_id.exists' => 'Kursus yang dipilih tidak valid.',
            'title.required' => 'Judul module wajib diisi.',
            'title.max' => 'Judul module maksimal :max karakter.',
            'description.required' => 'Deskripsi module wajib diisi.',
            'order.required' => 'Urutan module wajib diisi.',
            'order.integer' => 'Urutan module harus berupa angka.',
            'order.min' => 'Urutan module tidak boleh kurang dari :min.'
        ];

        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'order' => 'required|integer|min:0'
        ], $messages);

        try {
            $module = DB::table('course_modules')->where('id', $id)->first();

            if (!$module) {
                return back()->with('error', 'Module tidak ditemukan');
            }

            // Update module menggunakan Query Builder
            DB::table('course_modules')->where('id', $id)->update([
                'course_id' => $request->course_id,
                'title' => $request->title,
                'description' => $request->description,
                'order' => $request->order,
                'updated_at' => now()
            ]);

            return redirect()->route('module.index')
                ->with('success', 'Module berhasil diperbarui');
        } catch (\Exception $e) {
            Log::error('Error updating module: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
