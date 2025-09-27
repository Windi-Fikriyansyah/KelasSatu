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

class LatihanController extends Controller
{
    public function index()
    {
        return view('latihan.index');
    }

    public function load(Request $request)
    {
        try {
            $kursus = DB::table('course_modules')
                ->leftJoin('courses', 'course_modules.course_id', '=', 'courses.id')
                ->select([
                    'course_modules.id',
                    'course_modules.course_id',
                    'course_modules.title',
                    'courses.thumbnail',
                    'courses.title as title_course',
                    'courses.status',
                    'course_modules.order',
                    'course_modules.created_at',
                    'course_modules.updated_at'
                ])
                ->where('courses.status', 'active')
                ->orderBy('course_modules.created_at', 'desc');

            return DataTables::of($kursus)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $encryptedId = Crypt::encrypt($row->id);

                    $buttons = '<div class="btn-group" role="group">';
                    $buttons .= '<a href="' . route('latihan.quiz', $encryptedId) . '" class="btn btn-sm btn-warning me-1" title="Buat Soal">';
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
            return redirect()->route('latihan.index')->with('error', 'ID tidak valid');
        }

        // Mengambil data course berdasarkan ID
        $course = DB::table('course_modules')->where('id', $decryptedId)->first();

        if (!$course) {
            return redirect()->route('latihan.index')->with('error', 'Materi tidak ditemukan');
        }

        // Mengambil soal yang terkait dengan course ini
        $soal = DB::table('quiz')
            ->where('module_id', $decryptedId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('latihan.detail_soal', compact('course', 'soal'));
    }

    public function load_quiz(Request $request, $courseId)
    {
        try {
            $decryptedId = Crypt::decrypt($courseId);

            $soal = DB::table('quiz')
                ->select([
                    'quiz.id',
                    'quiz.title',
                    'quiz.course_id',
                    'quiz.quiz_type',
                    'quiz.created_at',
                    'quiz.updated_at'
                ])
                ->where('quiz_type', 'latihan')
                ->where('quiz.module_id', $decryptedId)
                ->orderBy('quiz.created_at', 'desc');

            return DataTables::of($soal)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $encryptedId = Crypt::encrypt($row->id);

                    $buttons = '<div class="btn-group" role="group">';
                    $buttons .= '<a href="' . route('latihan.edit', $encryptedId) . '" class="btn btn-sm btn-warning me-1" title="Edit">';
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



    public function uploadckeditor(Request $request)
    {
        if ($request->hasFile('upload')) {
            $originName = $request->file('upload')->getClientOriginalName();
            $fileName = pathinfo($originName, PATHINFO_FILENAME);
            $extension = $request->file('upload')->getClientOriginalExtension();
            $fileName = $fileName . '_' . time() . '.' . $extension;

            $request->file('upload')->move(public_path('uploads/images'), $fileName);

            $url = asset('uploads/images/' . $fileName);

            return response()->json([
                'fileName' => $fileName,
                'uploaded' => 1,
                'url' => $url
            ]);
        }
    }

    public function createSoal($courseId)
    {
        try {
            $decryptedId = Crypt::decrypt($courseId);
            $course = DB::table('course_modules')->where('id', $decryptedId)->first();

            if (!$course) {
                return redirect()->route('latihan.index')->with('error', 'Materi tidak ditemukan');
            }

            return view('latihan.create', compact('course'));
        } catch (\Exception $e) {
            return redirect()->route('latihan.index')->with('error', 'ID tidak valid');
        }
    }

    public function edit($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);

            // Mengambil data quiz
            $soal = DB::table('quiz')->where('id', $decryptedId)->first();

            if (!$soal) {
                return redirect()->route('latihan.index')->with('error', 'Quiz tidak ditemukan');
            }

            // Mengambil course
            $course = DB::table('course_modules')->where('id', $soal->module_id)->first();

            if (!$course) {
                return redirect()->route('latihan.index')->with('error', 'Materi tidak ditemukan');
            }

            // Mengambil semua questions dengan field yang lengkap
            $questions = DB::table('quiz_questions')
                ->where('quiz_id', $decryptedId)
                ->get([
                    'id',
                    'question',
                    'question_type',
                    'option_a',
                    'option_b',
                    'option_c',
                    'option_d',
                    'option_e',
                    'correct_answer',
                    'correct_answers', // untuk PGK MCMA
                    'statements', // untuk PGK Kategori
                    'custom_labels', // untuk PGK Kategori
                    'pembahasan'
                ]);

            // Transform data untuk konsistensi dengan frontend
            $questions = $questions->map(function ($question) {
                $question->explanation = $question->pembahasan;

                // Parse JSON fields jika ada
                if ($question->correct_answers) {
                    $question->correct_answers = is_string($question->correct_answers)
                        ? json_decode($question->correct_answers, true)
                        : $question->correct_answers;
                }

                if ($question->statements) {
                    $question->statements = is_string($question->statements)
                        ? json_decode($question->statements, true)
                        : $question->statements;
                }

                if ($question->custom_labels) {
                    $question->custom_labels = is_string($question->custom_labels)
                        ? json_decode($question->custom_labels, true)
                        : $question->custom_labels;
                }

                // Set default question_type jika null
                if (!$question->question_type) {
                    $question->question_type = 'multiple_choice';
                }

                return $question;
            });

            // Menambahkan questions ke objek soal
            $soal->questions = $questions;

            return view('latihan.create', compact('course', 'soal'));
        } catch (\Exception $e) {
            return redirect()->route('latihan.index')->with('error', 'ID tidak valid');
        }
    }
    private function cleanJsonStatements($statements)
    {
        if (!$statements) return $statements;

        Log::info('Starting JSON cleaning:', ['original_length' => strlen($statements)]);

        // Pendekatan baru: fix dulu, baru decode
        $fixed = $this->fixJsonString($statements);

        // Coba decode hasil fix
        $decoded = json_decode($fixed, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            Log::info('JSON successfully decoded after initial fix');

            // Clean HTML dalam setiap value
            foreach ($decoded as $key => $value) {
                if (is_string($value)) {
                    // Hapus style attributes dengan hati-hati
                    $originalValue = $value;

                    // Method 1: Hapus seluruh style attribute
                    $value = preg_replace('/\s+style\s*=\s*"[^"]*"/i', '', $value);

                    // Method 2: Bersihkan space berlebihan
                    $value = preg_replace('/\s+/', ' ', $value);
                    $value = trim($value);

                    $decoded[$key] = $value;

                    Log::info("Cleaned value for key '$key':", [
                        'original' => substr($originalValue, 0, 100) . '...',
                        'cleaned' => substr($value, 0, 100) . '...'
                    ]);
                }
            }
            return json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        // Jika masih gagal, coba manual extraction yang lebih robust
        Log::warning('JSON decode failed after fix, trying manual extraction');

        // Pattern yang lebih spesifik untuk data kita
        $pattern = '/"([a-c])"\s*:\s*"(.*?)(?=",\s*"[a-c]"|\s*})/s';

        if (preg_match_all($pattern, $statements, $matches, PREG_SET_ORDER)) {
            $decoded = [];

            foreach ($matches as $match) {
                $key = $match[1];
                $value = $match[2];

                // Unescape JSON escape sequences
                $value = str_replace(['\\"', '\\\\'], ['"', '\\'], $value);

                // Remove style attributes safely
                $value = preg_replace('/\s+style\s*=\s*"[^"]*"/i', '', $value);

                // Clean up whitespace
                $value = preg_replace('/\s+/', ' ', $value);
                $value = trim($value);

                $decoded[$key] = $value;

                Log::info("Manual extraction for key '$key':", ['value' => substr($value, 0, 100) . '...']);
            }

            if (count($decoded) >= 3) { // Expect at least keys a, b, c
                Log::info('Manual extraction successful');
                return json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
        }

        // Final fallback - return as-is but log error
        Log::error('All attempts to clean JSON failed, returning original');
        return $statements;
    }

    // Tambahkan method untuk fix JSON dengan pendekatan berbeda
    private function fixJsonString($jsonString)
    {
        if (!$jsonString) return $jsonString;

        // Pendekatan konservatif: hanya hapus style attributes yang bermasalah
        // tanpa mengubah struktur JSON

        $fixed = $jsonString;

        // 1. Ganti escaped quotes di dalam style attributes
        $fixed = preg_replace('/style\\\\?=\\\\?"[^"]*font-family:\\\\?"Times New Roman\\\\?"[^"]*\\\\?"/i', '', $fixed);

        // 2. Hapus style attributes yang mengandung escaped quotes
        $fixed = preg_replace('/style\\\\?=\\\\?"[^"]*\\\\?"[^"]*"/i', '', $fixed);

        // 3. Clean up extra spaces yang mungkin tertinggal
        $fixed = preg_replace('/\s+/', ' ', $fixed);
        $fixed = preg_replace('/\s*>\s*/', '>', $fixed);
        $fixed = preg_replace('/\s*<\s*/', '<', $fixed);

        return $fixed;
    }

    // Tambahkan method untuk validasi custom JSON
    private function isValidJson($value)
    {
        if (!is_string($value)) {
            return false;
        }

        json_decode($value);
        return json_last_error() === JSON_ERROR_NONE;
    }



    public function store(Request $request)
    {

        if ($request->has('questions')) {
            $questions = $request->questions;
            foreach ($questions as $index => $question) {
                if (isset($question['question_type']) && $question['question_type'] === 'pgk_kategori' && isset($question['statements'])) {
                    $originalStatements = $question['statements'];
                    $cleanStatements = $this->cleanJsonStatements($question['statements']);
                    $questions[$index]['statements'] = $cleanStatements;

                    // Debug: Log cleaning process
                    Log::info("Question $index cleaning:", [
                        'original' => $originalStatements,
                        'cleaned' => $cleanStatements,
                        'is_valid_json' => $this->isValidJson($cleanStatements)
                    ]);
                }
            }
            $request->merge(['questions' => $questions]);
        }

        // Debug: Log cleaned data
        Log::info('Cleaned request data:', $request->all());

        // Validasi dasar
        $rules = [
            'course_id' => 'required|exists:course_modules,id',
            'title' => 'required|string|max:255',
            'questions' => 'required|array|min:1',
            'questions.*.question_type' => 'required|in:multiple_choice,pgk_kategori,pgk_mcma',
            'questions.*.question' => 'required|string',
            'questions.*.explanation' => 'nullable|string',
            'quiz_type' => 'required|in:latihan,tryout',
        ];

        // Tambahkan rules untuk setiap question type
        foreach ($request->questions as $index => $question) {
            $questionType = $question['question_type'];

            if ($questionType === 'multiple_choice') {
                $rules["questions.$index.option_a"] = 'required|string';
                $rules["questions.$index.option_b"] = 'required|string';
                $rules["questions.$index.option_c"] = 'nullable|string';
                $rules["questions.$index.option_d"] = 'nullable|string';
                $rules["questions.$index.option_e"] = 'nullable|string';
                $rules["questions.$index.correct_answer"] = 'required|in:A,B,C,D,E';
            } elseif ($questionType === 'pgk_kategori') {
                $rules["questions.$index.statements"] = 'required|string';
                $rules["questions.$index.correct_answers"] = 'required|string';
                $rules["questions.$index.custom_labels"] = 'required|string';
            } elseif ($questionType === 'pgk_mcma') {
                $rules["questions.$index.option_a"] = 'required|string';
                $rules["questions.$index.option_b"] = 'required|string';
                $rules["questions.$index.option_c"] = 'nullable|string';
                $rules["questions.$index.option_d"] = 'nullable|string';
                $rules["questions.$index.option_e"] = 'nullable|string';
                $rules["questions.$index.correct_answers"] = 'required|string';
            }
        }

        $validator = Validator::make($request->all(), $rules);

        // Custom JSON validation
        $validator->after(function ($validator) use ($request) {
            foreach ($request->questions as $index => $question) {
                $questionType = $question['question_type'];

                if ($questionType === 'pgk_kategori') {
                    // Validasi statements
                    if (isset($question['statements']) && !$this->isValidJson($question['statements'])) {
                        $validator->errors()->add("questions.$index.statements", 'Field statements harus berupa JSON yang valid.');
                    }

                    // Validasi correct_answers
                    if (isset($question['correct_answers']) && !$this->isValidJson($question['correct_answers'])) {
                        $validator->errors()->add("questions.$index.correct_answers", 'Field correct_answers harus berupa JSON yang valid.');
                    }

                    // Validasi custom_labels
                    if (isset($question['custom_labels']) && !$this->isValidJson($question['custom_labels'])) {
                        $validator->errors()->add("questions.$index.custom_labels", 'Field custom_labels harus berupa JSON yang valid.');
                    }
                } elseif ($questionType === 'pgk_mcma') {
                    // Validasi correct_answers untuk pgk_mcma
                    if (isset($question['correct_answers']) && !$this->isValidJson($question['correct_answers'])) {
                        $validator->errors()->add("questions.$index.correct_answers", 'Field correct_answers harus berupa JSON yang valid.');
                    }
                }
            }
        });

        if ($validator->fails()) {
            Log::error('Validation failed:', $validator->errors()->toArray());
            return redirect()->back()->withErrors($validator)->withInput();
        }


        DB::beginTransaction();

        try {
            $courseId = DB::table('course_modules')
                ->where('id', $request->course_id)
                ->value('course_id');
            $quizType = $request->quiz_type;

            // Simpan data Quiz
            $quizId = DB::table('quiz')->insertGetId([
                'module_id' => $request->course_id,
                'course_id' => $courseId,
                'title' => $request->title,
                'quiz_type' => $request->quiz_type,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Simpan pertanyaan
            foreach ($request->questions as $q) {
                $questionData = [
                    'quiz_id' => $quizId,
                    'question_type' => $q['question_type'],
                    'question' => $q['question'],
                    'pembahasan' => $q['explanation'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // Handle data berdasarkan tipe soal
                if ($q['question_type'] === 'multiple_choice') {
                    $questionData['option_a'] = $q['option_a'];
                    $questionData['option_b'] = $q['option_b'];
                    $questionData['option_c'] = $q['option_c'] ?? null;
                    $questionData['option_d'] = $q['option_d'] ?? null;
                    $questionData['option_e'] = $q['option_e'] ?? null;
                    $questionData['correct_answer'] = $q['correct_answer'];
                } elseif ($q['question_type'] === 'pgk_kategori') {
                    $cleanStatements = $this->cleanJsonStatements($q['statements']);
                    $questionData['statements'] = $cleanStatements;

                    $questionData['correct_answers'] = $q['correct_answers'];
                    $questionData['custom_labels'] = $q['custom_labels'];

                    // Kosongkan field yang tidak digunakan
                    $questionData['option_a'] = null;
                    $questionData['option_b'] = null;
                    $questionData['option_c'] = null;
                    $questionData['option_d'] = null;
                    $questionData['option_e'] = null;
                    $questionData['correct_answer'] = null;
                } elseif ($q['question_type'] === 'pgk_mcma') {
                    $questionData['option_a'] = $q['option_a'];
                    $questionData['option_b'] = $q['option_b'];
                    $questionData['option_c'] = $q['option_c'] ?? null;
                    $questionData['option_d'] = $q['option_d'] ?? null;
                    $questionData['option_e'] = $q['option_e'] ?? null;
                    $questionData['correct_answers'] = $q['correct_answers'];

                    // Kosongkan field yang tidak digunakan
                    $questionData['correct_answer'] = null;
                }

                DB::table('quiz_questions')->insert($questionData);
            }

            DB::table('quiz_drafts')
                ->where('user_id', Auth::id())
                ->where('module_id', $request->course_id)
                ->where('quiz_type', $quizType)
                ->delete();
            // TAMBAHKAN INI - Commit transaksi
            DB::commit();

            return redirect()->route('latihan.quiz', Crypt::encrypt($request->course_id))
                ->with('success', 'Quiz dan semua soal berhasil disimpan.');
        } catch (\Exception $e) {
            // TAMBAHKAN INI - Rollback transaksi jika error
            DB::rollBack();
            Log::error('Error menyimpan quiz: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menyimpan quiz: ' . $e->getMessage())->withInput();
        }
    }

    // Method update soal
    public function update(Request $request)
    {

        if ($request->has('questions')) {
            $questions = $request->questions;
            foreach ($questions as $index => $question) {
                if (isset($question['question_type']) && $question['question_type'] === 'pgk_kategori' && isset($question['statements'])) {
                    $originalStatements = $question['statements'];
                    $cleanStatements = $this->cleanJsonStatements($question['statements']);
                    $questions[$index]['statements'] = $cleanStatements;

                    // Debug: Log cleaning process
                    Log::info("Question $index cleaning:", [
                        'original' => $originalStatements,
                        'cleaned' => $cleanStatements,
                        'is_valid_json' => $this->isValidJson($cleanStatements)
                    ]);
                }
            }
            $request->merge(['questions' => $questions]);
        }

        // Debug: Log cleaned data
        Log::info('Cleaned request data:', $request->all());

        // Validasi dasar
        $rules = [
            'course_id' => 'required|exists:course_modules,id',
            'title' => 'required|string|max:255',
            'questions' => 'required|array|min:1',
            'questions.*.question_type' => 'required|in:multiple_choice,pgk_kategori,pgk_mcma',
            'questions.*.question' => 'required|string',
            'questions.*.explanation' => 'nullable|string',
            'quiz_type' => 'required|in:latihan,tryout',
        ];

        // Tambahkan rules untuk setiap question type
        foreach ($request->questions as $index => $question) {
            $questionType = $question['question_type'];

            if ($questionType === 'multiple_choice') {
                $rules["questions.$index.option_a"] = 'required|string';
                $rules["questions.$index.option_b"] = 'required|string';
                $rules["questions.$index.option_c"] = 'nullable|string';
                $rules["questions.$index.option_d"] = 'nullable|string';
                $rules["questions.$index.option_e"] = 'nullable|string';
                $rules["questions.$index.correct_answer"] = 'required|in:A,B,C,D,E';
            } elseif ($questionType === 'pgk_kategori') {
                $rules["questions.$index.statements"] = 'required|string';
                $rules["questions.$index.correct_answers"] = 'required|string';
                $rules["questions.$index.custom_labels"] = 'required|string';
            } elseif ($questionType === 'pgk_mcma') {
                $rules["questions.$index.option_a"] = 'required|string';
                $rules["questions.$index.option_b"] = 'required|string';
                $rules["questions.$index.option_c"] = 'nullable|string';
                $rules["questions.$index.option_d"] = 'nullable|string';
                $rules["questions.$index.option_e"] = 'nullable|string';
                $rules["questions.$index.correct_answers"] = 'required|string';
            }
        }

        $validator = Validator::make($request->all(), $rules);

        // Custom JSON validation
        $validator->after(function ($validator) use ($request) {
            foreach ($request->questions as $index => $question) {
                $questionType = $question['question_type'];

                if ($questionType === 'pgk_kategori') {
                    // Validasi statements
                    if (isset($question['statements']) && !$this->isValidJson($question['statements'])) {
                        $validator->errors()->add("questions.$index.statements", 'Field statements harus berupa JSON yang valid.');
                    }

                    // Validasi correct_answers
                    if (isset($question['correct_answers']) && !$this->isValidJson($question['correct_answers'])) {
                        $validator->errors()->add("questions.$index.correct_answers", 'Field correct_answers harus berupa JSON yang valid.');
                    }

                    // Validasi custom_labels
                    if (isset($question['custom_labels']) && !$this->isValidJson($question['custom_labels'])) {
                        $validator->errors()->add("questions.$index.custom_labels", 'Field custom_labels harus berupa JSON yang valid.');
                    }
                } elseif ($questionType === 'pgk_mcma') {
                    // Validasi correct_answers untuk pgk_mcma
                    if (isset($question['correct_answers']) && !$this->isValidJson($question['correct_answers'])) {
                        $validator->errors()->add("questions.$index.correct_answers", 'Field correct_answers harus berupa JSON yang valid.');
                    }
                }
            }
        });

        if ($validator->fails()) {
            Log::error('Validation failed:', $validator->errors()->toArray());
            return redirect()->back()->withErrors($validator)->withInput();
        }


        DB::beginTransaction();
        try {
            $courseId = DB::table('course_modules')
                ->where('id', $request->course_id)
                ->value('course_id');



            // Update data quiz
            DB::table('quiz')->where('id', $request->quiz_id)->update([
                'module_id' => $request->course_id,
                'course_id' => $courseId,
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

            // Hapus pertanyaan yang tidak ada dalam request
            if (!empty($requestQuestionIds)) {
                DB::table('quiz_questions')
                    ->where('quiz_id', $request->quiz_id)
                    ->whereNotIn('id', $requestQuestionIds)
                    ->delete();
            } else {
                DB::table('quiz_questions')
                    ->where('quiz_id', $request->quiz_id)
                    ->delete();
            }

            // Update atau insert pertanyaan
            foreach ($request->questions as $q) {
                $questionData = [
                    'question_type' => $q['question_type'],
                    'question' => $q['question'],
                    'pembahasan' => $q['explanation'] ?? null,
                    'updated_at' => now(),
                ];

                // Handle data berdasarkan tipe soal
                if ($q['question_type'] === 'multiple_choice') {
                    $questionData['option_a'] = $q['option_a'];
                    $questionData['option_b'] = $q['option_b'];
                    $questionData['option_c'] = $q['option_c'] ?? null;
                    $questionData['option_d'] = $q['option_d'] ?? null;
                    $questionData['option_e'] = $q['option_e'] ?? null;
                    $questionData['correct_answer'] = $q['correct_answer'];

                    // Kosongkan field yang tidak digunakan
                    $questionData['statements'] = null;
                    $questionData['correct_answers'] = null;
                    $questionData['custom_labels'] = null;
                } elseif ($q['question_type'] === 'pgk_kategori') {
                    $cleanStatements = $this->cleanJsonStatements($q['statements']);

                    $questionData['statements'] = $cleanStatements;
                    $questionData['correct_answers'] = $q['correct_answers'];
                    $questionData['custom_labels'] = $q['custom_labels'];

                    // Kosongkan field yang tidak digunakan
                    $questionData['option_a'] = null;
                    $questionData['option_b'] = null;
                    $questionData['option_c'] = null;
                    $questionData['option_d'] = null;
                    $questionData['option_e'] = null;
                    $questionData['correct_answer'] = null;
                } elseif ($q['question_type'] === 'pgk_mcma') {
                    $questionData['option_a'] = $q['option_a'];
                    $questionData['option_b'] = $q['option_b'];
                    $questionData['option_c'] = $q['option_c'] ?? null;
                    $questionData['option_d'] = $q['option_d'] ?? null;
                    $questionData['option_e'] = $q['option_e'] ?? null;
                    $questionData['correct_answers'] = $q['correct_answers'];

                    // Kosongkan field yang tidak digunakan
                    $questionData['correct_answer'] = null;
                    $questionData['statements'] = null;
                    $questionData['custom_labels'] = null;
                }

                if (!empty($q['id'])) {
                    // Update pertanyaan existing
                    DB::table('quiz_questions')->where('id', $q['id'])->update($questionData);
                } else {
                    // Insert pertanyaan baru
                    $questionData['quiz_id'] = $request->quiz_id;
                    $questionData['created_at'] = now();
                    DB::table('quiz_questions')->insert($questionData);
                }
            }

            $quizType = $request->quiz_type;

            // Hapus draft setelah berhasil update
            DB::table('quiz_drafts')
                ->where('user_id', Auth::id())
                ->where('module_id', $request->course_id)
                ->where('quiz_type', $quizType)
                ->delete();

            DB::commit();

            return redirect()->route('latihan.quiz', Crypt::encrypt($request->course_id))
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
            $quizType = $request->input('quiz_type', 'latihan');
            $now = Carbon::now();

            $data = [
                'title' => $request->input('title'),
                'questions_data' => json_encode($request->input('questions', [])), // Sesuai nama kolom
                'form_data' => json_encode($request->input('form_data', [])),
                'last_saved_at' => $now,
                'updated_at' => $now,
            ];

            // Cek apakah draft sudah ada
            $draft = DB::table('quiz_drafts')
                ->where('user_id', $userId)
                ->where('module_id', $courseId)
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
                $data['module_id'] = $courseId;
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
            \Log::error('Error saving draft: ' . $e->getMessage());
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
            $quizType = $request->input('quiz_type', 'latihan');

            $draft = DB::table('quiz_drafts')
                ->where('user_id', $userId)
                ->where('module_id', $courseId)
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
                    'questions' => json_decode($draft->questions_data) ?? [], // Sesuai nama kolom
                    'form_data' => json_decode($draft->form_data) ?? [],
                    'saved_at' => Carbon::parse($draft->last_saved_at)->format('d M Y H:i')
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error loading draft: ' . $e->getMessage());
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
            $quizType = $request->input('quiz_type', 'latihan');

            DB::table('quiz_drafts')
                ->where('user_id', $userId)
                ->where('module_id', $courseId)
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
            $quizType = $request->input('quiz_type', 'latihan');

            $draft = DB::table('quiz_drafts')
                ->where('user_id', $userId)
                ->where('module_id', $courseId)
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
