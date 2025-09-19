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
        // Validasi apakah user memiliki akses ke quiz ini
        $quiz = DB::table('quiz')
            ->join('courses', 'quiz.course_id', '=', 'courses.id')
            ->join('enrollments', function ($join) {
                $join->on('courses.id', '=', 'enrollments.course_id')
                    ->where('enrollments.user_id', Auth::id());
            })
            ->select('quiz.*', 'courses.title as course_title')
            ->where('quiz.id', $quizId)
            ->where('quiz.quiz_type', 'latihan')
            ->first();

        if (!$quiz) {
            return redirect()->route('kelas.index')
                ->with('error', 'Anda tidak memiliki akses ke latihan ini.');
        }

        // Ambil semua soal untuk quiz ini
        $questions = DB::table('quiz_questions as qq')
            ->select(
                'qq.id as question_id',
                'qq.question',
                'qq.option_a',
                'qq.option_b',
                'qq.option_c',
                'qq.option_d',
                'qq.option_e',
                'qq.correct_answer'
            )
            ->where('qq.quiz_id', $quizId)
            ->orderBy('qq.id')
            ->get();

        // Format options untuk setiap soal
        foreach ($questions as $question) {
            $question->formatted_options = [
                'A' => $question->option_a,
                'B' => $question->option_b,
                'C' => $question->option_c,
                'D' => $question->option_d,
                'E' => $question->option_e,
            ];
            $question->correct_answer = $question->correct_answer;
        }

        return view('kelas_saya.latihan', compact('quiz', 'questions'));
    }


    public function tryout($quizId)
    {

        $quizId = base64_decode($quizId);

        // Validasi apakah user memiliki akses ke quiz ini
        $quiz = DB::table('quiz')
            ->join('courses', 'quiz.course_id', '=', 'courses.id')
            ->join('enrollments', function ($join) {
                $join->on('courses.id', '=', 'enrollments.course_id')
                    ->where('enrollments.user_id', Auth::id());
            })
            ->select('quiz.*', 'courses.title as course_title')
            ->where('quiz.id', $quizId)
            ->where('quiz.quiz_type', 'tryout')
            ->first();

        if (!$quiz) {
            return redirect()->route('kelas.index')
                ->with('error', 'Anda tidak memiliki akses ke tryout ini.');
        }

        // Ambil semua soal untuk quiz ini
        $questions = DB::table('quiz_questions as qq')
            ->select(
                'qq.id as question_id',
                'qq.question',
                'qq.option_a',
                'qq.option_b',
                'qq.option_c',
                'qq.option_d',
                'qq.option_e',
                'qq.correct_answer'
            )
            ->where('qq.quiz_id', $quizId)
            ->orderBy('qq.id')
            ->get();

        // Format options untuk setiap soal
        foreach ($questions as $question) {
            $question->formatted_options = [
                'A' => $question->option_a,
                'B' => $question->option_b,
                'C' => $question->option_c,
                'D' => $question->option_d,
                'E' => $question->option_e,
            ];
            $question->correct_answer = $question->correct_answer;
        }

        return view('kelas_saya.tryout', compact('quiz', 'questions'));
    }




    public function submitLatihan(Request $request, $quizId)
    {
        $userId = Auth::id();
        $answers = $request->input('answers', []);
        $duration = $request->input('duration');


        $quiz = DB::table('quiz')->where('id', $quizId)->first();
        $questions = DB::table('quiz_questions')->where('quiz_id', $quizId)->get();



        $totalQuestions = $questions->count();
        $correctAnswers = 0;
        $results = [];

        foreach ($questions as $question) {
            $userAnswer = $answers[$question->id] ?? null;
            $isCorrect = $userAnswer === $question->correct_answer;

            if ($isCorrect) $correctAnswers++;

            $results[] = [
                'question'       => $question->question,
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
        }

        $score = $totalQuestions > 0 ? ($correctAnswers / $totalQuestions) * 100 : 0;
        $quizResultId = DB::table('quiz_result')->insertGetId([
            'user_id'    => $userId,
            'quiz_id'    => $quizId,
            'duration'   => $duration, // dalam detik
            'score'      => $score,
            'created_at' => now(),
        ]);

        foreach ($answers as $questionId => $answer) {
            DB::table('quiz_answer')->insert([
                'quiz_result_id' => $quizResultId,
                'user_id' => $userId,
                'quiz_id' => $quizId,
                'question_id' => $questionId,
                'answer' => $answer,
                'created_at' => now(),
            ]);
        }
        // simpan ke session
        session([
            'score' => $score,
            'correct_answers' => $correctAnswers,
            'total_questions' => $totalQuestions,
            'answersDetail' => $results,
            'duration' => $duration
        ]);

        // redirect ke route hasil (sesuaikan nama routenya)
        return redirect()->route('kelas.latihan.hasil', $quizId);
    }

    public function submitTryout(Request $request, $quizId)
    {
        $userId = Auth::id();
        $answers = $request->input('answers', []);
        $duration = $request->input('duration');


        $quiz = DB::table('quiz')->where('id', $quizId)->first();
        $questions = DB::table('quiz_questions')->where('quiz_id', $quizId)->get();



        $totalQuestions = $questions->count();
        $correctAnswers = 0;
        $results = [];

        foreach ($questions as $question) {
            $userAnswer = $answers[$question->id] ?? null;
            $isCorrect = $userAnswer === $question->correct_answer;

            if ($isCorrect) $correctAnswers++;

            $results[] = [
                'question'       => $question->question,
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
        }

        $score = $totalQuestions > 0 ? ($correctAnswers / $totalQuestions) * 100 : 0;

        $quizResultId = DB::table('quiz_result')->insertGetId([
            'user_id'    => $userId,
            'quiz_id'    => $quizId,
            'duration'   => $duration, // dalam detik
            'score'      => $score,
            'created_at' => now(),
        ]);

        foreach ($answers as $questionId => $answer) {
            DB::table('quiz_answer')->insert([
                'quiz_result_id' => $quizResultId,
                'user_id' => $userId,
                'quiz_id' => $quizId,
                'question_id' => $questionId,
                'answer' => $answer,
                'created_at' => now(),
            ]);
        }
        // simpan ke session
        session([
            'score' => $score,
            'correct_answers' => $correctAnswers,
            'total_questions' => $totalQuestions,
            'answersDetail' => $results,
            'duration' => $duration,
        ]);

        // redirect ke route hasil (sesuaikan nama routenya)
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

        $questions = DB::table('quiz_questions as qq')
            ->select('qq.id as question_id', 'qq.question', 'qq.option_a', 'qq.option_b', 'qq.option_c', 'qq.option_d', 'qq.option_e', 'qq.correct_answer', 'qq.pembahasan')
            ->where('qq.quiz_id', $quizId)
            ->orderBy('qq.id')
            ->get();

        foreach ($questions as $q) {
            $q->formatted_options = [
                'A' => $q->option_a,
                'B' => $q->option_b,
                'C' => $q->option_c,
                'D' => $q->option_d,
                'E' => $q->option_e,
            ];
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
            $answersDetail[$q->question_id] = [
                'answer' => $answer,
                'is_correct' => $answer === $q->correct_answer,
            ];
            if ($answer === $q->correct_answer) {
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
            return redirect()->route('kelas.index')->with('error', 'Latihan tidak ditemukan.');
        }

        $questions = DB::table('quiz_questions as qq')
            ->select('qq.id as question_id', 'qq.question', 'qq.option_a', 'qq.option_b', 'qq.option_c', 'qq.option_d', 'qq.option_e', 'qq.correct_answer', 'qq.pembahasan')
            ->where('qq.quiz_id', $quizId)
            ->orderBy('qq.id')
            ->get();

        foreach ($questions as $q) {
            $q->formatted_options = [
                'A' => $q->option_a,
                'B' => $q->option_b,
                'C' => $q->option_c,
                'D' => $q->option_d,
                'E' => $q->option_e,
            ];
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
            $answersDetail[$q->question_id] = [
                'answer' => $answer,
                'is_correct' => $answer === $q->correct_answer,
            ];
            if ($answer === $q->correct_answer) {
                $score++;
            }
        }

        $result = DB::table('quiz_result')
            ->where('user_id', Auth::id())
            ->where('quiz_id', $quizId)
            ->orderByDesc('id')
            ->first();

        $duration = $result ? $result->duration : 0;
        // dd($duration);

        return view('kelas_saya.hasil_tryout', compact('quiz', 'questions', 'answersDetail', 'score', 'duration'));
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
            ->select('qq.id as question_id', 'qq.question', 'qq.option_a', 'qq.option_b', 'qq.option_c', 'qq.option_d', 'qq.option_e', 'qq.correct_answer', 'qq.pembahasan')
            ->where('qq.quiz_id', $quiz->quiz_id)
            ->orderBy('qq.id')
            ->get();

        foreach ($questions as $q) {
            $q->formatted_options = [
                'A' => $q->option_a,
                'B' => $q->option_b,
                'C' => $q->option_c,
                'D' => $q->option_d,
                'E' => $q->option_e,
            ];
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
            $answersDetail[$q->question_id] = [
                'answer' => $answer,
                'is_correct' => $answer === $q->correct_answer,
            ];
            if ($answer === $q->correct_answer) {
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
            ->select('qr.*', 'q.title', 'q.quiz_type', 'q.course_id', 'c.title as course_title')
            ->where('qr.id', $id)
            ->where('qr.user_id', Auth::id())
            ->orderBy('qr.created_at', 'desc')
            ->first();

        if (!$quiz) {
            return redirect()->route('kelas.index')->with('error', 'Latihan tidak ditemukan.');
        }

        $questions = DB::table('quiz_questions as qq')
            ->select('qq.id as question_id', 'qq.question', 'qq.option_a', 'qq.option_b', 'qq.option_c', 'qq.option_d', 'qq.option_e', 'qq.correct_answer', 'qq.pembahasan')
            ->where('qq.quiz_id', $quiz->quiz_id)
            ->orderBy('qq.id')
            ->get();

        foreach ($questions as $q) {
            $q->formatted_options = [
                'A' => $q->option_a,
                'B' => $q->option_b,
                'C' => $q->option_c,
                'D' => $q->option_d,
                'E' => $q->option_e,
            ];
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
            $answersDetail[$q->question_id] = [
                'answer' => $answer,
                'is_correct' => $answer === $q->correct_answer,
            ];
            if ($answer === $q->correct_answer) {
                $score++;
            }
        }

        $result = DB::table('quiz_result')
            ->where('user_id', Auth::id())
            ->where('id', $id)
            ->orderByDesc('id')
            ->first();

        $duration = $result ? $result->duration : 0;
        return view('kelas_saya.hasil_tryout', compact('quiz', 'duration', 'questions', 'answersDetail', 'score'));
    }
}
