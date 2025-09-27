<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class KelasSayaController extends Controller
{
    public function index()
    { // Ambil semua data course dari tabel 'courses'
        $courses = DB::table('enrollments')
            ->join('courses', 'enrollments.course_id', '=', 'courses.id')
            ->join('kategori', 'courses.id_kategori', '=', 'kategori.id')
            ->select('courses.id', 'courses.title', 'courses.description', 'courses.thumbnail', 'courses.price', 'courses.features', 'enrollments.user_id', 'enrollments.payment_status', 'kategori.nama_kategori')
            ->where('enrollments.user_id', Auth::id())
            ->get();

        // Kirim data ke view (misalnya ke halaman landing page kamu)
        return view('kelas_saya.index', compact('courses'));
    }

    public function akses($slug)
    {
        $id = substr($slug, strrpos($slug, '-') + 1);

        $enrollment = DB::table('enrollments')
            ->where('user_id', Auth::id())
            ->where('course_id', $id)
            ->first();

        if (!$enrollment) {
            return redirect()->route('kelas.index')->with('error', 'Anda tidak memiliki akses ke kelas ini.');
        }

        $course = DB::table('courses')
            ->join('kategori', 'courses.id_kategori', '=', 'kategori.id')
            ->select('courses.*', 'kategori.nama_kategori')
            ->where('courses.id', $id)
            ->first();

        // Ambil modul sesuai mapel
        if ($course->mapel === 'wajib') {
            $modules = DB::table('course_modules')
                ->where('course_id', $id)
                ->orderBy('order', 'asc')
                ->get();
        } else {
            $modules = DB::table('module_pilihan as mp')
                ->join('course_modules as cm', 'mp.module_id', '=', 'cm.id')
                ->where('mp.user_id', Auth::id())
                ->where('mp.course_id', $id)
                ->orderBy('cm.order', 'asc')
                ->select('cm.*')
                ->get();
        }

        // Ambil semua data untuk semua modul
        $materis = DB::table('contents')
            ->whereIn('module_id', $modules->pluck('id'))
            ->where('type', 'pdf')
            ->orderBy('order', 'asc')
            ->get();

        $videoContents = DB::table('contents')
            ->whereIn('module_id', $modules->pluck('id'))
            ->where('type', 'video')
            ->orderBy('order', 'asc')
            ->get();

        $latihan = DB::table('quiz as q')
            ->leftJoin('quiz_questions as qq', 'q.id', '=', 'qq.quiz_id')
            ->select('q.*', DB::raw('COUNT(qq.id) as jumlah_soal'))
            ->whereIn('q.module_id', $modules->pluck('id'))
            ->where('q.quiz_type', 'latihan')
            ->groupBy('q.id')
            ->get();

        $tryout = DB::table('quiz as q')
            ->leftJoin('quiz_questions as qq', 'q.id', '=', 'qq.quiz_id')
            ->select('q.*', DB::raw('COUNT(qq.id) as jumlah_soal'))
            ->whereIn('q.module_id', $modules->pluck('id'))
            ->where('q.quiz_type', 'tryout')
            ->groupBy('q.id')
            ->get();

        return view('kelas_saya.materi', compact('course', 'modules', 'materis', 'videoContents', 'latihan', 'tryout'));
    }




    public function pdfView($moduleId)
    {
        $module = DB::table('contents')->where('id', $moduleId)->first();

        if (!$module || !$module->file_pdf) {
            abort(404, 'Materi PDF tidak ditemukan.');
        }

        return view('kelas_saya.pdf_view', compact('module'));
    }

    public function latihan($quizId)
    {
        $quizId = base64_decode($quizId);

        $quiz = DB::table('quiz')
            ->join('courses', 'quiz.course_id', '=', 'courses.id')
            ->join('enrollments', function ($join) {
                $join->on('courses.id', '=', 'enrollments.course_id')
                    ->where('enrollments.user_id', Auth::id());
            })
            ->select('quiz.*', 'courses.title as course_title', 'courses.mapel')
            ->where('quiz.id', $quizId)
            ->where('quiz.quiz_type', 'latihan')
            ->first();

        if (!$quiz) {
            return redirect()->route('kelas.index')
                ->with('error', 'Anda tidak memiliki akses ke latihan ini.');
        }

        $questions = DB::table('quiz_questions as qq')
            ->select(
                'qq.id as question_id',
                'qq.question',
                'qq.question_type',
                'qq.option_a',
                'qq.option_b',
                'qq.option_c',
                'qq.option_d',
                'qq.option_e',
                'qq.correct_answer',
                'qq.correct_answers',
                'qq.statements',
                'qq.custom_labels'
            )
            ->where('qq.quiz_id', $quizId)
            ->orderBy('qq.id')
            ->get();

        $questionRanges = [];
        if ($quiz->mapel === 'wajib') {
            $currentNumber = 1;
            $typeOrder = []; // Untuk menyimpan urutan kemunculan tipe

            // Kelompokkan soal berdasarkan urutan kemunculan
            foreach ($questions as $question) {
                if (!in_array($question->question_type, $typeOrder)) {
                    $typeOrder[] = $question->question_type;
                }
            }

            // Buat range berdasarkan urutan kemunculan tipe
            foreach ($typeOrder as $type) {
                $questionNumbers = [];
                $currentNumber = 1;

                foreach ($questions as $question) {
                    if ($question->question_type === $type) {
                        $questionNumbers[] = $currentNumber;
                    }
                    $currentNumber++;
                }

                if (!empty($questionNumbers)) {
                    $min = min($questionNumbers);
                    $max = max($questionNumbers);
                    $questionRanges[$type] = [
                        'range' => $min === $max ? "$min" : "$min-$max",
                        'questions' => $questionNumbers,
                        'order' => array_search($type, $typeOrder) // Tambahkan order
                    ];
                }
            }
        }

        foreach ($questions as $index => $question) {
            // Beri nomor urut yang sequential
            $question->display_number = $index + 1;

            if ($question->question_type === 'multiple_choice') {
                $question->formatted_options = [
                    'A' => $question->option_a,
                    'B' => $question->option_b,
                    'C' => $question->option_c,
                    'D' => $question->option_d,
                    'E' => $question->option_e,
                ];
            } elseif ($question->question_type === 'pgk_kategori') {
                $question->statements = json_decode($question->statements, true);
                $question->custom_labels = json_decode($question->custom_labels, true);
                $question->correct_answers = json_decode($question->correct_answers, true);
            } elseif ($question->question_type === 'pgk_mcma') {
                $question->formatted_options = [
                    'A' => $question->option_a,
                    'B' => $question->option_b,
                    'C' => $question->option_c,
                    'D' => $question->option_d,
                    'E' => $question->option_e,
                ];
                $question->correct_answers = json_decode($question->correct_answers, true);
            }
        }

        return view('kelas_saya.latihan', compact('quiz', 'questions', 'questionRanges'));
    }


    public function tryout($quizId)
    {

        $quizId = base64_decode($quizId);

        $quiz = DB::table('quiz')
            ->join('courses', 'quiz.course_id', '=', 'courses.id')
            ->join('enrollments', function ($join) {
                $join->on('courses.id', '=', 'enrollments.course_id')
                    ->where('enrollments.user_id', Auth::id());
            })
            ->select('quiz.*', 'courses.title as course_title', 'courses.mapel')
            ->where('quiz.id', $quizId)
            ->where('quiz.quiz_type', 'tryout')
            ->first();

        if (!$quiz) {
            return redirect()->route('kelas.index')
                ->with('error', 'Anda tidak memiliki akses ke tryout ini.');
        }

        $quizDuration = $quiz->durasi * 60;
        $questions = DB::table('quiz_questions as qq')
            ->select(
                'qq.id as question_id',
                'qq.question',
                'qq.question_type',
                'qq.option_a',
                'qq.option_b',
                'qq.option_c',
                'qq.option_d',
                'qq.option_e',
                'qq.correct_answer',
                'qq.correct_answers',
                'qq.statements',
                'qq.custom_labels'
            )
            ->where('qq.quiz_id', $quizId)
            ->orderBy('qq.id')
            ->get();

        $questionRanges = [];
        if ($quiz->mapel === 'wajib') {
            $currentNumber = 1;
            $typeOrder = []; // Untuk menyimpan urutan kemunculan tipe

            // Kelompokkan soal berdasarkan urutan kemunculan
            foreach ($questions as $question) {
                if (!in_array($question->question_type, $typeOrder)) {
                    $typeOrder[] = $question->question_type;
                }
            }

            // Buat range berdasarkan urutan kemunculan tipe
            foreach ($typeOrder as $type) {
                $questionNumbers = [];
                $currentNumber = 1;

                foreach ($questions as $question) {
                    if ($question->question_type === $type) {
                        $questionNumbers[] = $currentNumber;
                    }
                    $currentNumber++;
                }

                if (!empty($questionNumbers)) {
                    $min = min($questionNumbers);
                    $max = max($questionNumbers);
                    $questionRanges[$type] = [
                        'range' => $min === $max ? "$min" : "$min-$max",
                        'questions' => $questionNumbers,
                        'order' => array_search($type, $typeOrder) // Tambahkan order
                    ];
                }
            }
        }

        foreach ($questions as $index => $question) {
            // Beri nomor urut yang sequential
            $question->display_number = $index + 1;

            if ($question->question_type === 'multiple_choice') {
                $question->formatted_options = [
                    'A' => $question->option_a,
                    'B' => $question->option_b,
                    'C' => $question->option_c,
                    'D' => $question->option_d,
                    'E' => $question->option_e,
                ];
            } elseif ($question->question_type === 'pgk_kategori') {
                $question->statements = json_decode($question->statements, true);
                $question->custom_labels = json_decode($question->custom_labels, true);
                $question->correct_answers = json_decode($question->correct_answers, true);
            } elseif ($question->question_type === 'pgk_mcma') {
                $question->formatted_options = [
                    'A' => $question->option_a,
                    'B' => $question->option_b,
                    'C' => $question->option_c,
                    'D' => $question->option_d,
                    'E' => $question->option_e,
                ];
                $question->correct_answers = json_decode($question->correct_answers, true);
            }
        }

        return view('kelas_saya.tryout', compact('quiz', 'questions', 'questionRanges', 'quizDuration'));
    }




    public function submitLatihan(Request $request, $quizId)
    {
        $userId = Auth::id();
        $answers = $request->input('answers', []);
        $duration = $request->input('duration');

        $quiz = DB::table('quiz')->where('id', $quizId)->first();

        // Ambil semua field yang diperlukan untuk berbagai tipe soal
        $questions = DB::table('quiz_questions')
            ->select(
                'id',
                'question',
                'question_type',
                'option_a',
                'option_b',
                'option_c',
                'option_d',
                'option_e',
                'correct_answer',
                'correct_answers',
                'statements',
                'custom_labels',
                'pembahasan'
            )
            ->where('quiz_id', $quizId)
            ->get();

        $totalQuestions = $questions->count();

        // Ganti nama counter supaya tidak bentrok dengan array correct_answers
        $correctCount = 0;
        $results = [];

        foreach ($questions as $question) {
            $userAnswer = $answers[$question->id] ?? null;
            $isCorrect = false;

            if ($question->question_type === 'multiple_choice') {
                $isCorrect = $userAnswer === $question->correct_answer;

                $results[] = [
                    'question_id'    => $question->id,
                    'question'       => $question->question,
                    'question_type'  => $question->question_type,
                    'options'        => [
                        'A' => $question->option_a,
                        'B' => $question->option_b,
                        'C' => $question->option_c,
                        'D' => $question->option_d,
                        'E' => $question->option_e,
                    ],
                    'user_answer'    => $userAnswer,
                    'correct_answer' => $question->correct_answer,
                    'explanation'    => $question->pembahasan ?? '-',
                    'is_correct'     => $isCorrect,
                ];
            } elseif ($question->question_type === 'pgk_kategori') {
                // gunakan nama berbeda untuk array correct answers per soal
                $questionCorrectAnswers = json_decode($question->correct_answers, true) ?? [];
                $statements = json_decode($question->statements, true) ?? [];
                $customLabels = json_decode($question->custom_labels, true) ?? [];

                // decode jawaban user (bisa string JSON atau array)
                $userAnswersRaw = is_array($userAnswer) ? $userAnswer : (json_decode($userAnswer, true) ?? []);

                // konversi nilai user ke boolean (true/false)
                $userAnswersBoolean = [];
                foreach ($userAnswersRaw as $k => $v) {
                    // deteksi berbagai kemungkinan nilai "true"
                    $isTrue = ($v === 'true_label') || filter_var($v, FILTER_VALIDATE_BOOLEAN);
                    $userAnswersBoolean[$k] = (bool) $isTrue;
                }

                // cek kebenaran: bandingkan boolean arrays
                $isCorrect = $userAnswersBoolean === $questionCorrectAnswers;

                $results[] = [
                    'question_id'    => $question->id,
                    'question'       => $question->question,
                    'question_type'  => $question->question_type,
                    'statements'     => $statements,
                    'custom_labels'  => $customLabels,
                    'user_answer'    => $userAnswersBoolean,
                    'correct_answer' => $questionCorrectAnswers,
                    'explanation'    => $question->pembahasan ?? '-',
                    'is_correct'     => $isCorrect,
                ];
            } elseif ($question->question_type === 'pgk_mcma') {
                $questionCorrectAnswers = json_decode($question->correct_answers, true) ?? [];

                $userAnswers = is_array($userAnswer) ? $userAnswer : (json_decode($userAnswer, true) ?? []);

                if (is_array($userAnswers) && is_array($questionCorrectAnswers)) {
                    sort($userAnswers);
                    $sortedCorrectAnswers = $questionCorrectAnswers;
                    sort($sortedCorrectAnswers);
                    $isCorrect = $userAnswers === $sortedCorrectAnswers;
                }

                $results[] = [
                    'question_id'    => $question->id,
                    'question'       => $question->question,
                    'question_type'  => $question->question_type,
                    'options'        => [
                        'A' => $question->option_a,
                        'B' => $question->option_b,
                        'C' => $question->option_c,
                        'D' => $question->option_d,
                        'E' => $question->option_e,
                    ],
                    'user_answer'    => $userAnswers,
                    'correct_answer' => $questionCorrectAnswers,
                    'explanation'    => $question->pembahasan ?? '-',
                    'is_correct'     => $isCorrect,
                ];
            }

            if ($isCorrect) {
                $correctCount++;
            }
        }

        // Hitung score — gunakan $correctCount, bukan $correctAnswers (array)
        $score = $totalQuestions > 0 ? ($correctCount / $totalQuestions) * 100 : 0;

        // Simpan hasil quiz
        $quizResultId = DB::table('quiz_result')->insertGetId([
            'user_id'    => $userId,
            'quiz_id'    => $quizId,
            'duration'   => $duration,
            'score'      => $score,
            'duration' => $duration,
            'created_at' => now(),
        ]);

        // Simpan jawaban detail ke DB
        foreach ($answers as $questionId => $answer) {
            $question = $questions->firstWhere('id', $questionId);

            if ($question && $question->question_type === 'pgk_kategori') {
                $decodedAnswer = is_array($answer) ? $answer : (json_decode($answer, true) ?? []);
                $booleanAnswer = [];
                foreach ($decodedAnswer as $k => $v) {
                    $isTrue = ($v === 'true_label') || filter_var($v, FILTER_VALIDATE_BOOLEAN);
                    $booleanAnswer[$k] = (bool) $isTrue;
                }
                $answerToStore = json_encode($booleanAnswer);
            } elseif ($question && $question->question_type === 'pgk_mcma') {
                $decoded = is_array($answer) ? $answer : (json_decode($answer, true) ?? []);
                $answerToStore = json_encode($decoded);
            } else {
                $answerToStore = is_array($answer) ? json_encode($answer) : $answer;
            }

            DB::table('quiz_answer')->insert([
                'quiz_result_id' => $quizResultId,
                'user_id'        => $userId,
                'quiz_id'        => $quizId,
                'question_id'    => $questionId,
                'answer'         => $answerToStore,
                'created_at'     => now(),
            ]);
        }

        // Simpan ke session (gunakan $correctCount)
        session([
            'score'           => $score,
            'correct_answers' => $correctCount,
            'total_questions' => $totalQuestions,
            'answersDetail'   => $results,
            'duration'        => $duration
        ]);

        // Redirect ke route hasil
        return redirect()->route('kelas.latihan.hasil', $quizId);
    }


    public function submitTryout(Request $request, $quizId)
    {

        $userId = Auth::id();
        $answers = $request->input('answers', []);
        $duration = $request->input('duration');

        $quiz = DB::table('quiz')->where('id', $quizId)->first();

        // Ambil semua field yang diperlukan untuk berbagai tipe soal
        $questions = DB::table('quiz_questions')
            ->select(
                'id',
                'question',
                'question_type',
                'option_a',
                'option_b',
                'option_c',
                'option_d',
                'option_e',
                'correct_answer',
                'correct_answers',
                'statements',
                'custom_labels',
                'pembahasan'
            )
            ->where('quiz_id', $quizId)
            ->get();

        $totalQuestions = $questions->count();

        // Ganti nama counter supaya tidak bentrok dengan array correct_answers
        $correctCount = 0;
        $results = [];

        foreach ($questions as $question) {
            $userAnswer = $answers[$question->id] ?? null;
            $isCorrect = false;

            if ($question->question_type === 'multiple_choice') {
                $isCorrect = $userAnswer === $question->correct_answer;

                $results[] = [
                    'question_id'    => $question->id,
                    'question'       => $question->question,
                    'question_type'  => $question->question_type,
                    'options'        => [
                        'A' => $question->option_a,
                        'B' => $question->option_b,
                        'C' => $question->option_c,
                        'D' => $question->option_d,
                        'E' => $question->option_e,
                    ],
                    'user_answer'    => $userAnswer,
                    'correct_answer' => $question->correct_answer,
                    'explanation'    => $question->pembahasan ?? '-',
                    'is_correct'     => $isCorrect,
                ];
            } elseif ($question->question_type === 'pgk_kategori') {
                // gunakan nama berbeda untuk array correct answers per soal
                $questionCorrectAnswers = json_decode($question->correct_answers, true) ?? [];
                $statements = json_decode($question->statements, true) ?? [];
                $customLabels = json_decode($question->custom_labels, true) ?? [];

                // decode jawaban user (bisa string JSON atau array)
                $userAnswersRaw = is_array($userAnswer) ? $userAnswer : (json_decode($userAnswer, true) ?? []);

                // konversi nilai user ke boolean (true/false)
                $userAnswersBoolean = [];
                foreach ($userAnswersRaw as $k => $v) {
                    // deteksi berbagai kemungkinan nilai "true"
                    $isTrue = ($v === 'true_label') || filter_var($v, FILTER_VALIDATE_BOOLEAN);
                    $userAnswersBoolean[$k] = (bool) $isTrue;
                }

                // cek kebenaran: bandingkan boolean arrays
                $isCorrect = $userAnswersBoolean === $questionCorrectAnswers;

                $results[] = [
                    'question_id'    => $question->id,
                    'question'       => $question->question,
                    'question_type'  => $question->question_type,
                    'statements'     => $statements,
                    'custom_labels'  => $customLabels,
                    'user_answer'    => $userAnswersBoolean,
                    'correct_answer' => $questionCorrectAnswers,
                    'explanation'    => $question->pembahasan ?? '-',
                    'is_correct'     => $isCorrect,
                ];
            } elseif ($question->question_type === 'pgk_mcma') {
                $questionCorrectAnswers = json_decode($question->correct_answers, true) ?? [];

                $userAnswers = is_array($userAnswer) ? $userAnswer : (json_decode($userAnswer, true) ?? []);

                if (is_array($userAnswers) && is_array($questionCorrectAnswers)) {
                    sort($userAnswers);
                    $sortedCorrectAnswers = $questionCorrectAnswers;
                    sort($sortedCorrectAnswers);
                    $isCorrect = $userAnswers === $sortedCorrectAnswers;
                }

                $results[] = [
                    'question_id'    => $question->id,
                    'question'       => $question->question,
                    'question_type'  => $question->question_type,
                    'options'        => [
                        'A' => $question->option_a,
                        'B' => $question->option_b,
                        'C' => $question->option_c,
                        'D' => $question->option_d,
                        'E' => $question->option_e,
                    ],
                    'user_answer'    => $userAnswers,
                    'correct_answer' => $questionCorrectAnswers,
                    'explanation'    => $question->pembahasan ?? '-',
                    'is_correct'     => $isCorrect,
                ];
            }

            if ($isCorrect) {
                $correctCount++;
            }
        }

        // Hitung score — gunakan $correctCount, bukan $correctAnswers (array)
        $score = $totalQuestions > 0 ? ($correctCount / $totalQuestions) * 100 : 0;

        // Simpan hasil quiz
        $quizResultId = DB::table('quiz_result')->insertGetId([
            'user_id'    => $userId,
            'quiz_id'    => $quizId,
            'duration'   => $duration,
            'score'      => $score,
            'duration'   => $duration,
            'created_at' => now(),
        ]);

        // Simpan jawaban detail ke DB
        foreach ($answers as $questionId => $answer) {
            $question = $questions->firstWhere('id', $questionId);

            if ($question && $question->question_type === 'pgk_kategori') {
                $decodedAnswer = is_array($answer) ? $answer : (json_decode($answer, true) ?? []);
                $booleanAnswer = [];
                foreach ($decodedAnswer as $k => $v) {
                    $isTrue = ($v === 'true_label') || filter_var($v, FILTER_VALIDATE_BOOLEAN);
                    $booleanAnswer[$k] = (bool) $isTrue;
                }
                $answerToStore = json_encode($booleanAnswer);
            } elseif ($question && $question->question_type === 'pgk_mcma') {
                $decoded = is_array($answer) ? $answer : (json_decode($answer, true) ?? []);
                $answerToStore = json_encode($decoded);
            } else {
                $answerToStore = is_array($answer) ? json_encode($answer) : $answer;
            }

            DB::table('quiz_answer')->insert([
                'quiz_result_id' => $quizResultId,
                'user_id'        => $userId,
                'quiz_id'        => $quizId,
                'question_id'    => $questionId,
                'answer'         => $answerToStore,
                'created_at'     => now(),
            ]);
        }

        // Simpan ke session (gunakan $correctCount)
        session([
            'score'           => $score,
            'correct_answers' => $correctCount,
            'total_questions' => $totalQuestions,
            'answersDetail'   => $results,
            'duration' => $duration,
        ]);

        // Redirect ke route hasil
        return redirect()->route('kelas.tryout.hasil', $quizId);
    }



    public function hasilLatihan($quizId)
    {
        $quiz = DB::table('quiz')
            ->join('courses', 'quiz.course_id', '=', 'courses.id')
            ->select('quiz.*', 'courses.title as course_title', 'courses.id as course_id')
            ->where('quiz.id', $quizId)
            ->where('quiz.quiz_type', 'latihan')
            ->first();

        if (!$quiz) {
            return redirect()->route('kelas.index')->with('error', 'Latihan tidak ditemukan.');
        }

        // Ambil semua field yang diperlukan untuk berbagai tipe soal
        $questions = DB::table('quiz_questions as qq')
            ->select(
                'qq.id as question_id',
                'qq.question',
                'qq.question_type',
                'qq.option_a',
                'qq.option_b',
                'qq.option_c',
                'qq.option_d',
                'qq.option_e',
                'qq.correct_answer',
                'qq.correct_answers',
                'qq.statements',
                'qq.custom_labels',
                'qq.pembahasan'
            )
            ->where('qq.quiz_id', $quizId)
            ->orderBy('qq.id')
            ->get();

        // Format questions berdasarkan tipe soal
        foreach ($questions as $q) {
            if ($q->question_type === 'multiple_choice') {
                $q->formatted_options = [
                    'A' => $q->option_a,
                    'B' => $q->option_b,
                    'C' => $q->option_c,
                    'D' => $q->option_d,
                    'E' => $q->option_e,
                ];
            } elseif ($q->question_type === 'pgk_kategori') {
                $q->statements = json_decode($q->statements, true);
                $q->custom_labels = json_decode($q->custom_labels, true);
                $q->correct_answers = json_decode($q->correct_answers, true);

                // Mapping correct_answers ke custom_labels
                if ($q->correct_answers && $q->custom_labels) {
                    $q->mapped_correct_answers = [];
                    foreach ($q->correct_answers as $answerKey => $isTrue) {
                        // Jika nilai true, gunakan true_label; jika false, gunakan false_label
                        if ($isTrue) {
                            $q->mapped_correct_answers[$answerKey] = $q->custom_labels['true_label'] ?? 'Setuju';
                        } else {
                            $q->mapped_correct_answers[$answerKey] = $q->custom_labels['false_label'] ?? 'Salah';
                        }
                    }
                }
            } elseif ($q->question_type === 'pgk_mcma') {
                $q->formatted_options = [
                    'A' => $q->option_a,
                    'B' => $q->option_b,
                    'C' => $q->option_c,
                    'D' => $q->option_d,
                    'E' => $q->option_e,
                ];
                $q->correct_answers = json_decode($q->correct_answers, true);
            }
        }

        $answers = DB::table('quiz_answer')
            ->where('user_id', Auth::id())
            ->where('quiz_id', $quizId)
            ->get()
            ->keyBy('question_id');

        $answersDetail = [];
        $score = 0;

        foreach ($questions as $q) {
            $answer = $answers[$q->question_id]->answer ?? null;
            $isCorrect = false;
            $mappedUserAnswer = null;

            // Evaluasi jawaban berdasarkan tipe soal
            if ($q->question_type === 'multiple_choice') {
                $isCorrect = $answer === $q->correct_answer;
                $mappedUserAnswer = $answer;
            } elseif ($q->question_type === 'pgk_kategori') {
                $userAnswers = json_decode($answer, true);
                $isCorrect = $userAnswers === $q->correct_answers;

                // Map user answers ke custom_labels
                if ($userAnswers && $q->custom_labels) {
                    $mappedUserAnswer = [];
                    foreach ($userAnswers as $answerKey => $isTrue) {
                        // Jika nilai true, gunakan true_label; jika false, gunakan false_label
                        if ($isTrue) {
                            $mappedUserAnswer[$answerKey] = $q->custom_labels['true_label'] ?? 'Setuju';
                        } else {
                            $mappedUserAnswer[$answerKey] = $q->custom_labels['false_label'] ?? 'Salah';
                        }
                    }
                } else {
                    $mappedUserAnswer = $userAnswers;
                }
            } elseif ($q->question_type === 'pgk_mcma') {
                // Untuk pgk_mcma, jawaban bisa berupa array
                $userAnswers = is_array($answer) ? $answer : json_decode($answer, true);
                if (is_array($userAnswers) && is_array($q->correct_answers)) {
                    sort($userAnswers);
                    sort($q->correct_answers);
                    $isCorrect = $userAnswers === $q->correct_answers;
                }

                // Mapping jawaban user jadi string "A, C, D"
                $mappedUserAnswer = is_array($userAnswers) ? implode(', ', $userAnswers) : $userAnswers;

                // Mapping jawaban benar juga sama
                $q->mapped_correct_answers = is_array($q->correct_answers) ? implode(', ', $q->correct_answers) : $q->correct_answers;
            }

            $answersDetail[$q->question_id] = [
                'answer' => $answer,
                'mapped_answer' => $mappedUserAnswer, // Tambahkan mapped answer
                'is_correct' => $isCorrect,
            ];

            if (isset($q->mapped_correct_answers)) {
                $answersDetail[$q->question_id]['mapped_correct_answer'] = $q->mapped_correct_answers;
            }

            if ($isCorrect) {
                $score++;
            }
        }

        return view('kelas_saya.hasil_latihan', compact('quiz', 'questions', 'answersDetail', 'score'));
    }

    public function hasilTryout($quizId)
    {
        $quiz = DB::table('quiz')
            ->join('courses', 'quiz.course_id', '=', 'courses.id')
            ->select('quiz.*', 'courses.title as course_title', 'courses.id as course_id')
            ->where('quiz.id', $quizId)
            ->where('quiz.quiz_type', 'tryout')
            ->first();

        if (!$quiz) {
            return redirect()->route('kelas.index')->with('error', 'Tryout tidak ditemukan.');
        }

        // Ambil semua field yang diperlukan untuk berbagai tipe soal
        $questions = DB::table('quiz_questions as qq')
            ->select(
                'qq.id as question_id',
                'qq.question',
                'qq.question_type',
                'qq.option_a',
                'qq.option_b',
                'qq.option_c',
                'qq.option_d',
                'qq.option_e',
                'qq.correct_answer',
                'qq.correct_answers',
                'qq.statements',
                'qq.custom_labels',
                'qq.pembahasan'
            )
            ->where('qq.quiz_id', $quizId)
            ->orderBy('qq.id')
            ->get();

        // Format questions berdasarkan tipe soal
        foreach ($questions as $q) {
            if ($q->question_type === 'multiple_choice') {
                $q->formatted_options = [
                    'A' => $q->option_a,
                    'B' => $q->option_b,
                    'C' => $q->option_c,
                    'D' => $q->option_d,
                    'E' => $q->option_e,
                ];
            } elseif ($q->question_type === 'pgk_kategori') {
                $q->statements = json_decode($q->statements, true);
                $q->custom_labels = json_decode($q->custom_labels, true);
                $q->correct_answers = json_decode($q->correct_answers, true);

                // Mapping correct_answers ke custom_labels
                if ($q->correct_answers && $q->custom_labels) {
                    $q->mapped_correct_answers = [];
                    foreach ($q->correct_answers as $answerKey => $isTrue) {
                        // Jika nilai true, gunakan true_label; jika false, gunakan false_label
                        if ($isTrue) {
                            $q->mapped_correct_answers[$answerKey] = $q->custom_labels['true_label'] ?? 'Setuju';
                        } else {
                            $q->mapped_correct_answers[$answerKey] = $q->custom_labels['false_label'] ?? 'Salah';
                        }
                    }
                }
            } elseif ($q->question_type === 'pgk_mcma') {
                $q->formatted_options = [
                    'A' => $q->option_a,
                    'B' => $q->option_b,
                    'C' => $q->option_c,
                    'D' => $q->option_d,
                    'E' => $q->option_e,
                ];
                $q->correct_answers = json_decode($q->correct_answers, true);
            }
        }

        $answers = DB::table('quiz_answer')
            ->where('user_id', Auth::id())
            ->where('quiz_id', $quizId)
            ->get()
            ->keyBy('question_id');

        $answersDetail = [];
        $score = 0;

        foreach ($questions as $q) {
            $answer = $answers[$q->question_id]->answer ?? null;
            $isCorrect = false;
            $mappedUserAnswer = null;

            // Evaluasi jawaban berdasarkan tipe soal
            if ($q->question_type === 'multiple_choice') {
                $isCorrect = $answer === $q->correct_answer;
                $mappedUserAnswer = $answer;
            } elseif ($q->question_type === 'pgk_kategori') {
                $userAnswers = json_decode($answer, true);
                $isCorrect = $userAnswers === $q->correct_answers;

                // Map user answers ke custom_labels
                if ($userAnswers && $q->custom_labels) {
                    $mappedUserAnswer = [];
                    foreach ($userAnswers as $answerKey => $isTrue) {
                        // Jika nilai true, gunakan true_label; jika false, gunakan false_label
                        if ($isTrue) {
                            $mappedUserAnswer[$answerKey] = $q->custom_labels['true_label'] ?? 'Setuju';
                        } else {
                            $mappedUserAnswer[$answerKey] = $q->custom_labels['false_label'] ?? 'Salah';
                        }
                    }
                } else {
                    $mappedUserAnswer = $userAnswers;
                }
            } elseif ($q->question_type === 'pgk_mcma') {
                // Untuk pgk_mcma, jawaban bisa berupa array
                $userAnswers = is_array($answer) ? $answer : json_decode($answer, true);
                if (is_array($userAnswers) && is_array($q->correct_answers)) {
                    sort($userAnswers);
                    sort($q->correct_answers);
                    $isCorrect = $userAnswers === $q->correct_answers;
                }

                // Mapping jawaban user jadi string "A, C, D"
                $mappedUserAnswer = is_array($userAnswers) ? implode(', ', $userAnswers) : $userAnswers;

                // Mapping jawaban benar juga sama
                $q->mapped_correct_answers = is_array($q->correct_answers) ? implode(', ', $q->correct_answers) : $q->correct_answers;
            }

            $answersDetail[$q->question_id] = [
                'answer' => $answer,
                'mapped_answer' => $mappedUserAnswer, // Tambahkan mapped answer
                'is_correct' => $isCorrect,
            ];

            if (isset($q->mapped_correct_answers)) {
                $answersDetail[$q->question_id]['mapped_correct_answer'] = $q->mapped_correct_answers;
            }

            if ($isCorrect) {
                $score++;
            }
        }

        $result = DB::table('quiz_result')
            ->where('user_id', Auth::id())
            ->where('quiz_id', $quizId)
            ->orderByDesc('id')
            ->first();

        $duration = $result ? $result->duration : 0;

        $formattedDuration = sprintf(
            '%02d:%02d:%02d',
            floor($duration / 3600),
            floor(($duration % 3600) / 60),
            $duration % 60
        );
        return view('kelas_saya.hasil_tryout', compact('quiz', 'questions', 'answersDetail', 'score', 'duration', 'formattedDuration'));
    }




    public function riwayat($quizId)
    {
        $quizId = base64_decode($quizId);



        $riwayats = DB::table('quiz_result as qr')
            ->join('quiz as q', 'qr.quiz_id', '=', 'q.id')
            ->select('qr.*', 'q.title as quiz_title', 'q.quiz_type')
            ->where('qr.quiz_id', $quizId)
            ->where('qr.user_id', Auth::id())
            ->orderBy('qr.created_at', 'desc')
            ->get();


        return view('kelas_saya.riwayat_nilai', compact('riwayats'));
    }

    public function hasilriwayatlatihan($id)
    {
        $quiz = DB::table('quiz_result as qr')
            ->join('quiz as q', 'qr.quiz_id', '=', 'q.id')
            ->join('courses as c', 'q.course_id', '=', 'c.id')
            ->select('qr.*', 'q.title', 'q.quiz_type', 'q.course_id', 'c.title as course_title')
            ->where('qr.id', $id)
            ->where('qr.user_id', Auth::id())
            ->orderBy('qr.created_at', 'desc')
            ->first();

        if (!$quiz) {
            return redirect()->route('kelas.index')->with('error', 'Latihan tidak ditemukan.');
        }

        $questions = DB::table('quiz_questions as qq')
            ->select(
                'qq.id as question_id',
                'qq.question',
                'qq.question_type',
                'qq.option_a',
                'qq.option_b',
                'qq.option_c',
                'qq.option_d',
                'qq.option_e',
                'qq.correct_answer',
                'qq.correct_answers',
                'qq.statements',
                'qq.custom_labels',
                'qq.pembahasan'
            )
            ->where('qq.quiz_id', $quiz->quiz_id)
            ->orderBy('qq.id')
            ->get();

        foreach ($questions as $q) {
            if ($q->question_type === 'multiple_choice') {
                $q->formatted_options = [
                    'A' => $q->option_a,
                    'B' => $q->option_b,
                    'C' => $q->option_c,
                    'D' => $q->option_d,
                    'E' => $q->option_e,
                ];
            } elseif ($q->question_type === 'pgk_kategori') {
                $q->statements = json_decode($q->statements, true);
                $q->custom_labels = json_decode($q->custom_labels, true);
                $q->correct_answers = json_decode($q->correct_answers, true);

                // Mapping correct_answers ke custom_labels
                if ($q->correct_answers && $q->custom_labels) {
                    $q->mapped_correct_answers = [];
                    foreach ($q->correct_answers as $answerKey => $isTrue) {
                        // Jika nilai true, gunakan true_label; jika false, gunakan false_label
                        if ($isTrue) {
                            $q->mapped_correct_answers[$answerKey] = $q->custom_labels['true_label'] ?? 'Setuju';
                        } else {
                            $q->mapped_correct_answers[$answerKey] = $q->custom_labels['false_label'] ?? 'Salah';
                        }
                    }
                }
            } elseif ($q->question_type === 'pgk_mcma') {
                $q->formatted_options = [
                    'A' => $q->option_a,
                    'B' => $q->option_b,
                    'C' => $q->option_c,
                    'D' => $q->option_d,
                    'E' => $q->option_e,
                ];
                $q->correct_answers = json_decode($q->correct_answers, true);
            }
        }
        $answers = DB::table('quiz_answer')
            ->where('user_id', Auth::id())
            ->where('quiz_result_id', $quiz->id)
            ->get()
            ->keyBy('question_id');

        $answersDetail = [];
        $score = 0;
        foreach ($questions as $q) {
            $answer = $answers[$q->question_id]->answer ?? null;
            $isCorrect = false;
            $mappedUserAnswer = null;

            // Evaluasi jawaban berdasarkan tipe soal
            if ($q->question_type === 'multiple_choice') {
                $isCorrect = $answer === $q->correct_answer;
                $mappedUserAnswer = $answer;
            } elseif ($q->question_type === 'pgk_kategori') {
                $userAnswers = json_decode($answer, true);
                $isCorrect = $userAnswers === $q->correct_answers;

                // Map user answers ke custom_labels
                if ($userAnswers && $q->custom_labels) {
                    $mappedUserAnswer = [];
                    foreach ($userAnswers as $answerKey => $isTrue) {
                        // Jika nilai true, gunakan true_label; jika false, gunakan false_label
                        if ($isTrue) {
                            $mappedUserAnswer[$answerKey] = $q->custom_labels['true_label'] ?? 'Setuju';
                        } else {
                            $mappedUserAnswer[$answerKey] = $q->custom_labels['false_label'] ?? 'Salah';
                        }
                    }
                } else {
                    $mappedUserAnswer = $userAnswers;
                }
            } elseif ($q->question_type === 'pgk_mcma') {
                // Untuk pgk_mcma, jawaban bisa berupa array
                $userAnswers = is_array($answer) ? $answer : json_decode($answer, true);
                if (is_array($userAnswers) && is_array($q->correct_answers)) {
                    sort($userAnswers);
                    sort($q->correct_answers);
                    $isCorrect = $userAnswers === $q->correct_answers;
                }

                // Mapping jawaban user jadi string "A, C, D"
                $mappedUserAnswer = is_array($userAnswers) ? implode(', ', $userAnswers) : $userAnswers;

                // Mapping jawaban benar juga sama
                $q->mapped_correct_answers = is_array($q->correct_answers) ? implode(', ', $q->correct_answers) : $q->correct_answers;
            }

            $answersDetail[$q->question_id] = [
                'answer' => $answer,
                'mapped_answer' => $mappedUserAnswer, // Tambahkan mapped answer
                'is_correct' => $isCorrect,
            ];

            if (isset($q->mapped_correct_answers)) {
                $answersDetail[$q->question_id]['mapped_correct_answer'] = $q->mapped_correct_answers;
            }

            if ($isCorrect) {
                $score++;
            }
        }

        return view('kelas_saya.hasil_latihan', compact('quiz', 'questions', 'answersDetail', 'score'));
    }

    public function hasilriwayattryout($id)
    {

        $quiz = DB::table('quiz_result as qr')
            ->join('quiz as q', 'qr.quiz_id', '=', 'q.id')
            ->join('courses as c', 'q.course_id', '=', 'c.id')
            ->select('qr.*', 'q.title', 'q.quiz_type', 'q.course_id', 'c.title as course_title', 'c.mapel')
            ->where('qr.id', $id)
            ->where('qr.user_id', Auth::id())
            ->orderBy('qr.created_at', 'desc')
            ->first();

        if (!$quiz) {
            return redirect()->route('kelas.index')->with('error', 'Latihan tidak ditemukan.');
        }

        $questions = DB::table('quiz_questions as qq')
            ->select(
                'qq.id as question_id',
                'qq.question',
                'qq.question_type',
                'qq.option_a',
                'qq.option_b',
                'qq.option_c',
                'qq.option_d',
                'qq.option_e',
                'qq.correct_answer',
                'qq.correct_answers',
                'qq.statements',
                'qq.custom_labels',
                'qq.pembahasan'
            )
            ->where('qq.quiz_id', $quiz->quiz_id)
            ->orderBy('qq.id')
            ->get();

        foreach ($questions as $q) {
            if ($q->question_type === 'multiple_choice') {
                $q->formatted_options = [
                    'A' => $q->option_a,
                    'B' => $q->option_b,
                    'C' => $q->option_c,
                    'D' => $q->option_d,
                    'E' => $q->option_e,
                ];
            } elseif ($q->question_type === 'pgk_kategori') {
                $q->statements = json_decode($q->statements, true);
                $q->custom_labels = json_decode($q->custom_labels, true);
                $q->correct_answers = json_decode($q->correct_answers, true);

                // Mapping correct_answers ke custom_labels
                if ($q->correct_answers && $q->custom_labels) {
                    $q->mapped_correct_answers = [];
                    foreach ($q->correct_answers as $answerKey => $isTrue) {
                        // Jika nilai true, gunakan true_label; jika false, gunakan false_label
                        if ($isTrue) {
                            $q->mapped_correct_answers[$answerKey] = $q->custom_labels['true_label'] ?? 'Setuju';
                        } else {
                            $q->mapped_correct_answers[$answerKey] = $q->custom_labels['false_label'] ?? 'Salah';
                        }
                    }
                }
            } elseif ($q->question_type === 'pgk_mcma') {
                $q->formatted_options = [
                    'A' => $q->option_a,
                    'B' => $q->option_b,
                    'C' => $q->option_c,
                    'D' => $q->option_d,
                    'E' => $q->option_e,
                ];
                $q->correct_answers = json_decode($q->correct_answers, true);
            }
        }



        $answers = DB::table('quiz_answer')
            ->where('user_id', Auth::id())
            ->where('quiz_result_id', $quiz->id)
            ->get()
            ->keyBy('question_id');

        $answersDetail = [];
        $score = 0;
        foreach ($questions as $q) {
            $answer = $answers[$q->question_id]->answer ?? null;
            $isCorrect = false;
            $mappedUserAnswer = null;

            // Evaluasi jawaban berdasarkan tipe soal
            if ($q->question_type === 'multiple_choice') {
                $isCorrect = $answer === $q->correct_answer;
                $mappedUserAnswer = $answer;
            } elseif ($q->question_type === 'pgk_kategori') {
                $userAnswers = json_decode($answer, true);
                $isCorrect = $userAnswers === $q->correct_answers;

                // Map user answers ke custom_labels
                if ($userAnswers && $q->custom_labels) {
                    $mappedUserAnswer = [];
                    foreach ($userAnswers as $answerKey => $isTrue) {
                        // Jika nilai true, gunakan true_label; jika false, gunakan false_label
                        if ($isTrue) {
                            $mappedUserAnswer[$answerKey] = $q->custom_labels['true_label'] ?? 'Setuju';
                        } else {
                            $mappedUserAnswer[$answerKey] = $q->custom_labels['false_label'] ?? 'Salah';
                        }
                    }
                } else {
                    $mappedUserAnswer = $userAnswers;
                }
            } elseif ($q->question_type === 'pgk_mcma') {
                // Untuk pgk_mcma, jawaban bisa berupa array
                $userAnswers = is_array($answer) ? $answer : json_decode($answer, true);
                if (is_array($userAnswers) && is_array($q->correct_answers)) {
                    sort($userAnswers);
                    sort($q->correct_answers);
                    $isCorrect = $userAnswers === $q->correct_answers;
                }

                // Mapping jawaban user jadi string "A, C, D"
                $mappedUserAnswer = is_array($userAnswers) ? implode(', ', $userAnswers) : $userAnswers;

                // Mapping jawaban benar juga sama
                $q->mapped_correct_answers = is_array($q->correct_answers) ? implode(', ', $q->correct_answers) : $q->correct_answers;
            }

            $answersDetail[$q->question_id] = [
                'answer' => $answer,
                'mapped_answer' => $mappedUserAnswer, // Tambahkan mapped answer
                'is_correct' => $isCorrect,
            ];

            if (isset($q->mapped_correct_answers)) {
                $answersDetail[$q->question_id]['mapped_correct_answer'] = $q->mapped_correct_answers;
            }

            if ($isCorrect) {
                $score++;
            }
        }


        $result = DB::table('quiz_result')
            ->where('user_id', Auth::id())
            ->where('id', $id)
            ->orderByDesc('id')
            ->first();

        $duration = $result ? $result->duration : 0;
        $formattedDuration = sprintf(
            '%02d:%02d:%02d',
            floor($duration / 3600),
            floor(($duration % 3600) / 60),
            $duration % 60
        );

        return view('kelas_saya.hasil_tryout', compact('quiz', 'duration', 'questions', 'answersDetail', 'score', 'formattedDuration'));
    }
}
