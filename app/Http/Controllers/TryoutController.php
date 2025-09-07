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
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TryoutController extends Controller
{
    public function index()
    {
        return view('tryout.index');
    }

    public function load(Request $request)
    {
        try {
            $kursus = DB::table('courses')
                ->leftJoin('kategori', 'courses.id_kategori', '=', 'kategori.id')
                ->select([
                    'courses.id',
                    'courses.title',
                    'courses.description',
                    'courses.thumbnail',
                    'courses.access_type',
                    'courses.is_free',
                    'courses.status',
                    'kategori.nama_kategori',
                    'courses.created_at',
                    'courses.updated_at'
                ])
                ->where('courses.status', 'active')
                ->orderBy('courses.created_at', 'desc');

            return DataTables::of($kursus)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $encryptedId = Crypt::encrypt($row->id);

                    $buttons = '<div class="btn-group" role="group">';
                    $buttons .= '<a href="' . route('tryout.quiz', $encryptedId) . '" class="btn btn-sm btn-warning me-1" title="Buat Soal">';
                    $buttons .= '<i class="bi bi-pencil"></i> Bikin Soal';
                    $buttons .= '</a>';
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

    public function quiz($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
        } catch (\Exception $e) {
            return redirect()->route('tryout.index')->with('error', 'ID tidak valid');
        }

        // Mengambil data course berdasarkan ID
        $course = DB::table('courses')->where('id', $decryptedId)->first();

        if (!$course) {
            return redirect()->route('tryout.index')->with('error', 'Materi tidak ditemukan');
        }

        // Mengambil soal yang terkait dengan course ini
        $soal = DB::table('quiz')
            ->where('course_id', $decryptedId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('tryout.detail_soal', compact('course', 'soal'));
    }

    public function load_quiz(Request $request, $courseId)
    {
        try {
            $decryptedId = Crypt::decrypt($courseId);

            $soal = DB::table('quiz')
                ->select([
                    'quiz.id',
                    'quiz.title',
                    'quiz.durasi',
                    'quiz.course_id',
                    'quiz.quiz_type',
                    'quiz.created_at',
                    'quiz.updated_at'
                ])
                ->where('quiz.course_id', $decryptedId)
                ->orderBy('quiz.created_at', 'desc');

            return DataTables::of($soal)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $encryptedId = Crypt::encrypt($row->id);

                    $buttons = '<div class="btn-group" role="group">';
                    $buttons .= '<a href="' . route('tryout.edit', $encryptedId) . '" class="btn btn-sm btn-warning me-1" title="Edit">';
                    $buttons .= '<i class="bi bi-pencil"></i>';
                    $buttons .= '</a>';
                    $buttons .= '<button class="btn btn-sm btn-danger delete-btn" data-id="' . $encryptedId . '" title="Hapus">';
                    $buttons .= '<i class="bi bi-trash"></i>';
                    $buttons .= '</button>';
                    $buttons .= '</div>';

                    return $buttons;
                })
                ->rawColumns(['action'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Error loading quiz data: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }


    public function createSoal($courseId)
    {
        try {
            $decryptedId = Crypt::decrypt($courseId);
            $course = DB::table('courses')->where('id', $decryptedId)->first();

            if (!$course) {
                return redirect()->route('tryout.index')->with('error', 'Materi tidak ditemukan');
            }

            return view('tryout.create', compact('course'));
        } catch (\Exception $e) {
            return redirect()->route('tryout.index')->with('error', 'ID tidak valid');
        }
    }

    public function edit($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);

            // Mengambil data quiz
            $soal = DB::table('quiz')->where('id', $decryptedId)->first();

            if (!$soal) {
                return redirect()->route('tryout.index')->with('error', 'Quiz tidak ditemukan');
            }

            // Mengambil course
            $course = DB::table('courses')->where('id', $soal->course_id)->first();

            if (!$course) {
                return redirect()->route('tryout.index')->with('error', 'course tidak ditemukan');
            }

            $questions = DB::table('quiz_questions')
                ->where('quiz_id', $decryptedId)
                ->get([
                    'id',
                    'question',
                    'option_a',
                    'option_b',
                    'option_c',
                    'option_d',
                    'option_e',
                    'correct_answer',
                    'pembahasan' // Gunakan nama field asli dari database
                ]);

            // Transform data untuk konsistensi dengan frontend
            $questions = $questions->map(function ($question) {
                $question->explanation = $question->pembahasan; // Map pembahasan ke explanation
                return $question;
            });

            // Menambahkan questions ke objek soal
            $soal->questions = $questions;

            return view('tryout.edit', compact('course', 'soal'));
        } catch (\Exception $e) {
            return redirect()->route('tryout.index')->with('error', 'ID tidak valid');
        }
    }

    public function store(Request $request)
    {
        // Validasi
        $validator = Validator::make($request->all(), [
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'durasi' => 'required|integer|min:0',
            'questions' => 'required|array|min:1',
            'questions.*.question' => 'required|string',
            'questions.*.option_a' => 'required|string|max:255',
            'questions.*.option_b' => 'required|string|max:255',
            'questions.*.option_c' => 'nullable|string|max:255',
            'questions.*.option_d' => 'nullable|string|max:255',
            'questions.*.option_e' => 'nullable|string|max:255',
            'questions.*.correct_answer' => 'required|in:A,B,C,D,E',
            'quiz_type' => 'required|in:latihan,tryout',
            'questions.*.explanation' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();

        try {
            $courseId = $request->course_id;
            $quizType = $request->quiz_type;

            // Simpan data Quiz
            $quizId = DB::table('quiz')->insertGetId([
                'course_id' => $request->course_id,
                'durasi' => $request->durasi,
                'title' => $request->title,
                'quiz_type' => $request->quiz_type,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Simpan pertanyaan
            foreach ($request->questions as $q) {
                DB::table('quiz_questions')->insert([
                    'quiz_id' => $quizId,
                    'question' => $q['question'],
                    'option_a' => $q['option_a'],
                    'option_b' => $q['option_b'],
                    'option_c' => $q['option_c'] ?? null,
                    'option_d' => $q['option_d'] ?? null,
                    'option_e' => $q['option_e'] ?? null,
                    'correct_answer' => $q['correct_answer'],
                    'pembahasan' => $q['explanation'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('quiz_drafts')
                ->where('user_id', Auth::id())
                ->where('course_id', $courseId)
                ->where('quiz_type', $quizType)
                ->delete();
            // TAMBAHKAN INI - Commit transaksi
            DB::commit();

            return redirect()->route('tryout.quiz', Crypt::encrypt($request->course_id))
                ->with('success', 'Quiz dan semua soal berhasil disimpan.');
        } catch (\Exception $e) {
            // TAMBAHKAN INI - Rollback transaksi jika error
            DB::rollBack();
            Log::error('Error menyimpan quiz: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menyimpan quiz: ' . $e->getMessage())->withInput();
        }
    }

    // Method update soal
    // Tambahkan method ini di controller untuk menangani penghapusan pertanyaan yang tidak ada lagi
    public function update(Request $request)
    {


        // Validasi utama
        $validator = Validator::make($request->all(), [
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'durasi' => 'required|integer|min:0',
            'questions' => 'required|array|min:1',
            'questions.*.question' => 'required|string',
            'questions.*.option_a' => 'required|string|max:255',
            'questions.*.option_b' => 'required|string|max:255',
            'questions.*.option_c' => 'nullable|string|max:255',
            'questions.*.option_d' => 'nullable|string|max:255',
            'questions.*.option_e' => 'nullable|string|max:255',
            'questions.*.correct_answer' => 'required|in:A,B,C,D,E',
            'quiz_type' => 'required|in:latihan,tryout',
            'questions.*.explanation' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            // Update data quiz
            DB::table('quiz')->where('id', $request->quiz_id)->update([
                'course_id' => $request->course_id,
                'durasi' => $request->durasi,
                'title' => $request->title,
                'quiz_type' => $request->quiz_type,
                'updated_at' => now(),
            ]);

            // Kumpulkan ID pertanyaan yang ada di request
            $requestQuestionIds = [];
            foreach ($request->questions as $q) {
                if (!empty($q['id'])) {
                    $requestQuestionIds[] = $q['id'];
                }
            }

            // Hapus pertanyaan yang tidak ada dalam request (yang dihapus user)
            if (!empty($requestQuestionIds)) {
                DB::table('quiz_questions')
                    ->where('quiz_id', $request->quiz_id)
                    ->whereNotIn('id', $requestQuestionIds)
                    ->delete();
            } else {
                // Jika tidak ada ID sama sekali, hapus semua pertanyaan lama
                DB::table('quiz_questions')
                    ->where('quiz_id', $request->quiz_id)
                    ->delete();
            }

            // Update atau insert pertanyaan
            foreach ($request->questions as $q) {
                if (!empty($q['id'])) {
                    // Update pertanyaan existing
                    DB::table('quiz_questions')->where('id', $q['id'])->update([
                        'question' => $q['question'],
                        'option_a' => $q['option_a'],
                        'option_b' => $q['option_b'],
                        'option_c' => $q['option_c'] ?? null,
                        'option_d' => $q['option_d'] ?? null,
                        'option_e' => $q['option_e'] ?? null,
                        'correct_answer' => $q['correct_answer'],
                        'pembahasan' => $q['explanation'] ?? null,
                        'updated_at' => now(),
                    ]);
                } else {
                    // Insert pertanyaan baru
                    DB::table('quiz_questions')->insert([
                        'quiz_id' => $request->quiz_id,
                        'question' => $q['question'],
                        'option_a' => $q['option_a'],
                        'option_b' => $q['option_b'],
                        'option_c' => $q['option_c'] ?? null,
                        'option_d' => $q['option_d'] ?? null,
                        'option_e' => $q['option_e'] ?? null,
                        'correct_answer' => $q['correct_answer'],
                        'pembahasan' => $q['explanation'] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            $courseId = $request->course_id;
            $quizType = $request->quiz_type;

            DB::table('quiz_drafts')
                ->where('user_id', Auth::id())
                ->where('course_id', $courseId)
                ->where('quiz_type', $quizType)
                ->delete();

            DB::commit();

            return redirect()->route('tryout.quiz', Crypt::encrypt($request->course_id))
                ->with('success', 'Quiz dan semua soal berhasil diupdate.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating quiz: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal update quiz: ' . $e->getMessage())->withInput();
        }
    }


    public function saveDraft(Request $request)
    {
        try {
            $userId = Auth::id();
            $courseId = $request->input('course_id');
            $quizType = $request->input('quiz_type', 'tryout');
            $now = Carbon::now();

            $data = [
                'title' => $request->input('title'),
                'durasi' => $request->input('durasi'),
                'questions_data' => json_encode($request->input('questions', [])),
                'form_data' => json_encode($request->input('form_data', [])),
                'last_saved_at' => $now,
                'updated_at' => $now,
            ];

            // Cek apakah draft sudah ada
            $draft = DB::table('quiz_drafts')
                ->where('user_id', $userId)
                ->where('course_id', $courseId)
                ->where('quiz_type', $quizType)
                ->first();

            if ($draft) {
                // Update draft
                DB::table('quiz_drafts')
                    ->where('id', $draft->id)
                    ->update($data);

                $draftId = $draft->id;
            } else {
                // Insert draft baru
                $data['user_id'] = $userId;
                $data['course_id'] = $courseId;
                $data['quiz_type'] = $quizType;
                $data['created_at'] = $now;

                $draftId = DB::table('quiz_drafts')->insertGetId($data);
            }

            return response()->json([
                'success' => true,
                'message' => 'Draft berhasil disimpan',
                'draft_id' => $draftId,
                'saved_at' => $now->format('d M Y H:i')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan draft: ' . $e->getMessage()
            ], 500);
        }
    }

    public function loadDraft(Request $request)
    {
        try {
            $userId = Auth::id();
            $courseId = $request->input('course_id');
            $quizType = $request->input('quiz_type', 'tryout');

            $draft = DB::table('quiz_drafts')
                ->where('user_id', $userId)
                ->where('course_id', $courseId)
                ->where('quiz_type', $quizType)
                ->first();

            if (!$draft) {
                return response()->json([
                    'success' => false,
                    'message' => 'Draft tidak ditemukan'
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'title' => $draft->title,
                    'durasi' => $draft->durasi,
                    'questions' => json_decode($draft->questions_data) ?? [],
                    'form_data' => json_decode($draft->form_data) ?? [],
                    'saved_at' => Carbon::parse($draft->last_saved_at)->format('d M Y H:i')
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat draft: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteDraft(Request $request)
    {
        try {
            $userId = Auth::id();
            $courseId = $request->input('course_id');
            $quizType = $request->input('quiz_type', 'tryout');

            DB::table('quiz_drafts')
                ->where('user_id', $userId)
                ->where('course_id', $courseId)
                ->where('quiz_type', $quizType)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Draft berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus draft: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkDraft(Request $request)
    {
        try {
            $userId = Auth::id();
            $courseId = $request->input('course_id');
            $quizType = $request->input('quiz_type', 'tryout');

            $draft = DB::table('quiz_drafts')
                ->where('user_id', $userId)
                ->where('course_id', $courseId)
                ->where('quiz_type', $quizType)
                ->first();

            return response()->json([
                'success' => true,
                'has_draft' => $draft ? true : false,
                'saved_at' => $draft ? Carbon::parse($draft->last_saved_at)->format('d M Y H:i') : null
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error checking draft: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);

            // Cek quiz
            $quiz = DB::table('quiz')->where('id', $decryptedId)->first();
            if (!$quiz) {
                return response()->json([
                    'success' => false,
                    'message' => 'Quiz tidak ditemukan'
                ], 404);
            }

            DB::beginTransaction();

            // Hapus pertanyaan terkait
            DB::table('quiz_questions')->where('quiz_id', $decryptedId)->delete();

            // Hapus quiz
            DB::table('quiz')->where('id', $decryptedId)->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Quiz berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting quiz: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus quiz: ' . $e->getMessage()
            ], 500);
        }
    }
}
