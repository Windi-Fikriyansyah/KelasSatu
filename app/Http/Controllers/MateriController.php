<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Handler\AbstractHandler;
use Pion\Laravel\ChunkUpload\Handler\ContentRangeUploadHandler;
use Illuminate\Http\UploadedFile;

class MateriController extends Controller
{
    public function index()
    {
        return view('materi.index');
    }

    public function load(Request $request)
    {
        try {
            // Join dengan tabel courses untuk mendapatkan judul kursus
            $kursus = DB::table('contents as cm')
                ->join('courses as c', 'cm.course_id', '=', 'c.id')
                ->select([
                    'cm.id',
                    'c.title as title_course',
                    'cm.title',
                    'cm.type',
                    'cm.created_at',
                    'cm.updated_at'
                ])
                ->orderBy('cm.created_at', 'desc');

            return DataTables::of($kursus)
                ->addIndexColumn()
                ->filterColumn('title_course', function ($query, $keyword) {
                    $query->whereRaw("LOWER(c.title) like ?", ["%" . strtolower($keyword) . "%"]);
                })
                ->addColumn('action', function ($row) {
                    $deleteUrl = route('materi.destroy', $row->id);

                    // Encrypt ID untuk link edit
                    $encryptedId = Crypt::encrypt($row->id);

                    $buttons = '<div class="btn-group" role="group">';
                    $buttons .= '<a href="' . route('materi.edit', $encryptedId) . '" class="btn btn-sm btn-warning me-1" title="Edit"><i class="bi bi-pencil"></i></a>';
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

    public function create()
    {
        // Ambil semua data courses
        $courses = DB::table('courses')->get();

        // Ambil semua data modules dengan relasi ke courses
        $kursuss = DB::table('courses')
            ->select('courses.*')
            ->get();

        return view('materi.create', compact('courses', 'kursuss'));
    }

    public function edit($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
        } catch (\Exception $e) {
            return redirect()->route('materi.index')->with('error', 'ID tidak valid');
        }

        $materi = DB::table('contents')->where('id', $decryptedId)->first();

        if (!$materi) {
            return redirect()->route('materi.index')->with('error', 'Materi tidak ditemukan');
        }

        $kursuss = DB::table('courses')->get();

        return view('materi.create', compact('kursuss', 'materi'));
    }

    /**
     * Handle chunk upload untuk video
     */
    public function uploadVideoChunk(Request $request)
    {
        try {
            Log::info('Video chunk upload started', [
                'request_files' => array_keys($request->allFiles()),
                'request_data' => $request->except(['_token']),
                'content_type' => $request->header('Content-Type')
            ]);

            // Validasi file
            $request->validate([
                'video_file' => 'required|file|mimes:mp4,avi,mov,wmv,mkv|max:2097152', // 2GB = 2097152 KB
            ]);

            // Periksa apakah ada file yang diupload
            if (!$request->hasFile('video_file')) {
                Log::error('No video file found in request');
                return response()->json([
                    'status' => false,
                    'message' => 'No video file received'
                ], 400);
            }

            // Untuk upload biasa tanpa chunk (FilePond bisa handle file kecil tanpa chunk)
            $file = $request->file('video_file');

            // Generate unique filename
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $fileName = $originalName . '_' . time() . '_' . uniqid() . '.' . $extension;

            // Store file
            $path = $file->storeAs('videos', $fileName, 'public');

            // Verify file was stored
            if (!Storage::disk('public')->exists($path)) {
                throw new \Exception('File was not stored successfully');
            }

            Log::info('Video upload successful', ['path' => $path]);

            // Return success response
            return response()->json([
                'status' => true,
                'message' => 'Video uploaded successfully',
                'path' => $path,
                'url' => Storage::url($path),
                'filename' => $fileName
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Video upload validation error: ' . json_encode($e->errors()));
            return response()->json([
                'status' => false,
                'message' => 'Validation failed: ' . implode(', ', $e->validator->errors()->all())
            ], 422);
        } catch (\Exception $e) {
            Log::error('Video chunk upload error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete uploaded video file (when user removes file from FilePond)
     */
    public function deleteVideoChunk(Request $request)
    {
        try {
            $filePath = trim($request->getContent());

            Log::info('Attempting to delete video file', ['path' => $filePath]);

            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
                Log::info('Video file deleted successfully', ['path' => $filePath]);
                return response()->json(['status' => true, 'message' => 'File deleted successfully']);
            }

            Log::warning('File not found for deletion', ['path' => $filePath]);
            return response()->json(['status' => false, 'message' => 'File not found']);
        } catch (\Exception $e) {
            Log::error('Delete video chunk error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Delete failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'type' => 'required|in:pdf,video',
            'title' => 'required|string|max:255',
            'description' => 'nullable',
            'pdf_file' => 'nullable|string',
            'video_file' => 'nullable|string'
        ]);

        try {
            // Get course_id from module
            $module = DB::table('course_modules')->where('id', $request->module_id)->first();

            $filePath = null;
            $videoPath = null;

            // Handle file upload based on type
            if ($request->type === 'pdf' && $request->pdf_file) {
                $filePath = $request->pdf_file;
            } elseif ($request->type === 'video' && $request->video_file) {
                $videoPath = $request->video_file;
            }

            DB::table('contents')->insert([
                'course_id' => $request->course_id,
                'type' => $request->type,
                'title' => $request->title,
                'description' => $request->description,
                'video' => $videoPath,
                'file_pdf' => $filePath,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->route('materi.index')->with('success', 'Materi berhasil disimpan!');
        } catch (\Exception $e) {
            Log::error('Store materi error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'type' => 'required|in:pdf,video',
            'title' => 'required|string|max:255',
            'description' => 'nullable',
            'pdf_file' => 'nullable|string',
            'video_file' => 'nullable|string'
        ]);

        try {
            $materi = DB::table('contents')->where('id', $id)->first();
            if (!$materi) {
                return redirect()->back()->with('error', 'Materi tidak ditemukan');
            }

            // Get course_id from module
            $module = DB::table('course_modules')->where('id', $request->module_id)->first();

            $filePath = $materi->file_pdf;
            $videoPath = $materi->video;

            // Handle file update based on type
            if ($request->type === 'pdf') {
                if ($request->pdf_file) {
                    // Delete old PDF file if exists and different
                    if ($filePath && $filePath !== $request->pdf_file && Storage::disk('public')->exists($filePath)) {
                        Storage::disk('public')->delete($filePath);
                    }
                    $filePath = $request->pdf_file;
                }
                // Clear video if switching from video to pdf
                if ($videoPath && Storage::disk('public')->exists($videoPath)) {
                    Storage::disk('public')->delete($videoPath);
                }
                $videoPath = null;
            } elseif ($request->type === 'video') {
                if ($request->video_file) {
                    // Delete old video file if exists and different
                    if ($videoPath && $videoPath !== $request->video_file && Storage::disk('public')->exists($videoPath)) {
                        Storage::disk('public')->delete($videoPath);
                    }
                    $videoPath = $request->video_file;
                }
                // Clear PDF if switching from pdf to video
                if ($filePath && Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                }
                $filePath = null;
            }

            DB::table('contents')->where('id', $id)->update([
                'course_id' => $request->course_id,
                'type' => $request->type,
                'title' => $request->title,
                'description' => $request->description,
                'video' => $videoPath,
                'file_pdf' => $filePath,
                'updated_at' => now(),
            ]);

            return redirect()->route('materi.index')->with('success', 'Materi berhasil diupdate!');
        } catch (\Exception $e) {
            Log::error('Update materi error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            // Get materi data first to delete files
            $materi = DB::table('contents')->where('id', $id)->first();

            if ($materi) {
                // Delete associated files
                if ($materi->file_pdf && Storage::disk('public')->exists($materi->file_pdf)) {
                    Storage::disk('public')->delete($materi->file_pdf);
                }

                if ($materi->video && Storage::disk('public')->exists($materi->video)) {
                    Storage::disk('public')->delete($materi->video);
                }

                // Delete database record
                $deleted = DB::table('contents')->where('id', $id)->delete();

                if ($deleted) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Materi berhasil dihapus'
                    ]);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Materi tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error deleting materi: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function uploadPdfChunk(Request $request)
    {
        try {
            Log::info('PDF chunk upload started', [
                'request_files' => array_keys($request->allFiles()),
                'request_data' => $request->except(['_token']),
                'content_type' => $request->header('Content-Type')
            ]);

            // Validasi file
            $request->validate([
                'pdf_file' => 'required|file|mimes:pdf|mimetypes:application/pdf|max:102400',
            ]);
            // Periksa apakah ada file yang diupload
            if (!$request->hasFile('pdf_file')) {
                Log::error('No PDF file found in request');
                return response()->json([
                    'status' => false,
                    'message' => 'No PDF file received'
                ], 400);
            }

            // Untuk upload biasa tanpa chunk
            $file = $request->file('pdf_file');

            // Generate unique filename
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $fileName = $originalName . '_' . time() . '_' . uniqid() . '.' . $extension;

            // Store file
            $path = $file->storeAs('pdfs', $fileName, 'public');

            // Verify file was stored
            if (!Storage::disk('public')->exists($path)) {
                throw new \Exception('File was not stored successfully');
            }

            Log::info('PDF upload successful', ['path' => $path]);

            // Return success response
            return response()->json([
                'status' => true,
                'message' => 'PDF uploaded successfully',
                'path' => $path,
                'url' => Storage::url($path),
                'filename' => $fileName
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('PDF upload validation error: ' . json_encode($e->errors()));
            return response()->json([
                'status' => false,
                'message' => 'Validation failed: ' . implode(', ', $e->validator->errors()->all())
            ], 422);
        } catch (\Exception $e) {
            Log::error('PDF chunk upload error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deletePdfChunk(Request $request)
    {
        try {
            $filePath = trim($request->getContent());

            Log::info('Attempting to delete PDF file', ['path' => $filePath]);

            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
                Log::info('PDF file deleted successfully', ['path' => $filePath]);
                return response()->json(['status' => true, 'message' => 'File deleted successfully']);
            }

            Log::warning('File not found for deletion', ['path' => $filePath]);
            return response()->json(['status' => false, 'message' => 'File not found']);
        } catch (\Exception $e) {
            Log::error('Delete PDF chunk error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Delete failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
