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
use Illuminate\Support\Facades\Auth;

class KursusController extends Controller
{
    public function index()
    {
        return view('kursus.index');
    }

    public function load(Request $request)
    {
        try {
            $kursus = DB::table('courses')
                ->leftJoin('kategori', 'courses.id_kategori', '=', 'kategori.id') // menambahkan join dengan kategori
                ->select([
                    'courses.id',
                    'courses.title',
                    'courses.description',
                    'courses.thumbnail',
                    'courses.price',
                    'courses.access_type',
                    'courses.is_free',
                    'courses.status',
                    'kategori.nama_kategori', // menampilkan nama kategori
                    'courses.created_at',
                    'courses.updated_at'
                ])
                ->orderBy('courses.created_at', 'desc');

            return DataTables::of($kursus)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $encryptedId = Crypt::encrypt($row->id);
                    $deleteUrl = route('kursus.destroy', $row->id);

                    $buttons = '<div class="btn-group" role="group">';
                    $buttons .= '<a href="' . route('kursus.edit', $encryptedId) . '" class="btn btn-sm btn-warning me-1" title="Edit"><i class="bi bi-pencil"></i></a>';
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
            $deleted = DB::table('courses')->where('id', $id)->delete();

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
        $kategoris = DB::table('kategori')->get();
        return view('kursus.create', compact('kategoris'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'id_kategori' => 'required|string',
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'price' => 'required|numeric|min:0',
            'is_free' => 'required|boolean',
            'status' => 'required|in:active,inactive',
            'access_type' => 'required|in:lifetime,subscription',
            'subscription_duration' => 'nullable|integer|min:1',
            'features' => 'nullable|array',
            'features.*' => 'nullable|string|max:255',
        ]);

        try {
            // Jika gratis, set harga ke 0
            $price = $request->is_free ? 0 : $request->price;

            // Handle thumbnail upload
            $thumbnailPath = null;
            if ($request->hasFile('thumbnail')) {
                $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
            }

            // Insert course using Query Builder
            DB::table('courses')->insert([
                'title' => $request->title,
                'description' => $request->description,
                'id_kategori' => $request->id_kategori,
                'thumbnail' => $thumbnailPath,
                'price' => $price,
                'is_free' => $request->is_free,
                'access_type' => $request->access_type,
                'subscription_duration' => $request->access_type === 'subscription' ? $request->subscription_duration : null,
                'status' => $request->status,
                'features' => $request->features ? json_encode($request->features) : null,
                'created_by' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return redirect()->route('kursus.index')
                ->with('success', 'Kursus berhasil dibuat');
        } catch (\Exception $e) {
            Log::error('Error creating course: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
        } catch (\Exception $e) {
            return redirect()->route('kursus.index')->with('error', 'ID tidak valid');
        }
        $course = DB::table('courses')->where('id', $decryptedId)->first();
        if (!$course) {
            return redirect()->route('kursus.index')->with('error', 'Materi tidak ditemukan');
        }

        $kategoris = DB::table('kategori')->get();

        return view('kursus.create', compact('course', 'kategoris'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'id_kategori' => 'required|string',
            'description' => 'required|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'price' => 'required|numeric|min:0',
            'is_free' => 'required|boolean',
            'status' => 'required|in:active,inactive',
            'access_type' => 'required|in:lifetime,subscription',
            'subscription_duration' => 'nullable|integer|min:1',
            'features' => 'nullable|array',
            'features.*' => 'nullable|string|max:255',
        ]);

        try {
            $course = DB::table('courses')->where('id', $id)->first();

            if (!$course) {
                return back()->with('error', 'Kursus tidak ditemukan');
            }

            // Jika gratis, set harga ke 0
            $price = $request->is_free ? 0 : $request->price;

            $dataUpdate = [
                'title' => $request->title,
                'id_kategori' => $request->id_kategori,
                'description' => $request->description,
                'price' => $price,
                'is_free' => $request->is_free,
                'access_type' => $request->access_type,
                'subscription_duration' => $request->access_type === 'subscription' ? $request->subscription_duration : null,
                'status' => $request->status,
                'features' => $request->features ? json_encode($request->features) : null,
                'updated_at' => now()
            ];

            // Handle thumbnail upload
            if ($request->hasFile('thumbnail')) {
                // Delete old thumbnail
                if ($course->thumbnail && Storage::disk('public')->exists($course->thumbnail)) {
                    Storage::disk('public')->delete($course->thumbnail);
                }

                $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
                $dataUpdate['thumbnail'] = $thumbnailPath;
            }

            // Update course using Query Builder
            DB::table('courses')->where('id', $id)->update($dataUpdate);

            return redirect()->route('kursus.index')
                ->with('success', 'Kursus berhasil diperbarui');
        } catch (\Exception $e) {
            Log::error('Error updating course: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function kursus()
    {
        // Ambil semua data course dari tabel 'courses'
        $courses = DB::table('courses')
            ->join('kategori', 'courses.id_kategori', '=', 'kategori.id')
            ->select('courses.id', 'courses.title', 'courses.description', 'courses.thumbnail', 'courses.price', 'courses.features', 'kategori.nama_kategori')
            ->limit(6)
            ->get();

        // Kirim data ke view (misalnya ke halaman landing page kamu)
        return view('welcome', compact('courses'));
    }
    public function course()
    {
        // Ambil semua data course dari tabel 'courses'
        $courses = DB::table('courses')
            ->join('kategori', 'courses.id_kategori', '=', 'kategori.id')
            ->select('courses.id', 'courses.title', 'kategori.id as kategori_id', 'courses.description', 'courses.thumbnail', 'courses.price', 'courses.features', 'kategori.nama_kategori')
            ->orderBy('courses.created_at', 'desc')
            ->get();
        $kategori = DB::table('kategori')->get();

        return view('kursus', compact('courses', 'kategori'));
    }
    public function detail($slug)
    {
        // Ambil ID dari slug (angka di belakang)
        $id = (int) substr(strrchr($slug, '-'), 1);

        $course = DB::table('courses')
            ->join('kategori', 'courses.id_kategori', '=', 'kategori.id')
            ->select('courses.*', 'kategori.nama_kategori')
            ->where('courses.id', $id)
            ->first();

        if (!$course) {
            abort(404);
        }

        return view('kursus.detail', compact('course'));
    }

    public function checkout($id)
    {
        $course = DB::table('courses')->where('id', $id)->first();
        return view('kursus.checkout', compact('course'));
    }

    public function pay(Request $request, $id)
    {
        // Contoh simpan transaksi
        DB::table('transactions')->insert([
            'course_id' => $id,
            'user_id' => auth()->id(),
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('course')->with('success', 'Pembayaran berhasil, silakan cek riwayat transaksi!');
    }

    public function show($encryptedCourseId)
    {
        try {
            $courseId = Crypt::decryptString($encryptedCourseId);

            // Ambil data course dari database tanpa model
            $course = DB::table('courses')->where('id', $courseId)->first();

            if (!$course) {
                return redirect()->back()->with('error', 'Kursus tidak ditemukan.');
            }

            return view('kursus.show', compact('course'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
