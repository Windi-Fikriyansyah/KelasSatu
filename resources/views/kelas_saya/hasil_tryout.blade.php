@extends('layouts.app')
@section('title', 'Hasil Tryout - ' . $quiz->title)

@section('content')
    <section class="py-8 bg-gradient-to-b from-primary-50 to-white min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <a href="{{ route('kelas.akses', Str::slug($quiz->course_title) . '-' . $quiz->course_id) }}"
                            class="mr-4 text-gray-600 hover:text-primary-100 transition-colors">
                            <i class="fa-solid fa-arrow-left text-xl"></i>
                        </a>
                        <div>
                            <p class="text-gray-600">{{ $quiz->title }}</p>

                            <!-- Label quiz_type -->
                            @if ($quiz->quiz_type === 'tryout')
                                <span
                                    class="inline-block px-3 py-1 text-sm rounded-full bg-blue-100 text-blue-700 font-medium">
                                    {{ ucfirst($quiz->quiz_type) }}
                                </span>
                            @endif

                            <!-- Nilai dari 100 -->
                            @php
                                $nilai = round(($score / count($questions)) * 100);
                            @endphp
                            <span
                                class="inline-block px-3 py-1 text-sm rounded-full bg-green-100 text-green-700 font-medium ml-2">
                                Nilai: {{ $nilai }}
                            </span>
                            <span
                                class="inline-block px-3 py-1 text-sm rounded-full bg-purple-100 text-purple-700 font-medium ml-2">
                                Waktu Pengerjaan : {{ $formattedDuration }}
                            </span>

                        </div>
                    </div>
                </div>
            </div>



            <!-- Layout 2 Kolom -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

                <!-- Navigasi Soal (Kiri) -->
                <!-- Navigasi Soal (Kiri) -->
                <div class="bg-white rounded-xl shadow-lg p-6 h-fit lg:sticky lg:top-24">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Navigasi Soal</h3>
                    <div class="grid grid-cols-8 lg:grid-cols-3 gap-2">
                        @foreach ($questions as $index => $question)
                            @php
                                $userAnswer = $answersDetail[$question->question_id]['answer'] ?? null;
                                $isCorrect = $answersDetail[$question->question_id]['is_correct'] ?? false;
                            @endphp
                            <button type="button"
                                class="question-nav w-10 h-10 rounded-lg border text-white font-semibold
                    {{ $isCorrect ? 'bg-green-500 border-green-500' : ($userAnswer ? 'bg-red-500 border-red-500' : 'bg-gray-400 border-gray-400') }}"
                                onclick="goToQuestion({{ $index + 1 }})">
                                {{ $index + 1 }}
                            </button>
                        @endforeach
                    </div>
                </div>


                <!-- Soal & Pembahasan (Kanan) -->
                <div class="lg:col-span-3">
                    @foreach ($questions as $index => $question)
                        @php
                            $userAnswer = $answersDetail[$question->question_id]['answer'] ?? null;
                            $isCorrect = $answersDetail[$question->question_id]['is_correct'] ?? false;
                        @endphp

                        <div class="question-card bg-white rounded-xl shadow-lg overflow-hidden mb-6 {{ $index > 0 ? 'hidden' : '' }}"
                            data-question="{{ $index + 1 }}">
                            <div class="p-6">
                                <div class="flex items-start mb-6">
                                    <div
                                        class="bg-primary-100 text-white rounded-full w-8 h-8 flex items-center justify-center font-semibold mr-4 flex-shrink-0">
                                        {{ $index + 1 }}
                                    </div>
                                    <div class="flex-1">
                                        <!-- Soal -->
                                        <div class="text-lg font-medium text-gray-900 mb-4 ckeditor-output">
                                            {!! $question->question !!}
                                        </div>

                                        <!-- Tampilan berdasarkan tipe soal -->
                                        @if ($question->question_type === 'multiple_choice')
                                            <!-- Tampilan Multiple Choice (existing code) -->
                                            <div class="space-y-3 mb-4">
                                                @foreach ($question->formatted_options ?? [] as $optionKey => $option)
                                                    @if ($option)
                                                        <div
                                                            class="flex items-center p-3 border rounded-lg
                                            {{ $optionKey === $question->correct_answer ? 'border-green-500 bg-green-50' : '' }}
                                            {{ $userAnswer === $optionKey && $optionKey !== $question->correct_answer ? 'border-red-500 bg-red-50' : '' }}">
                                                            <span
                                                                class="circle-label flex items-center justify-center w-8 h-8 rounded-full font-semibold mr-3
                                                {{ $optionKey === $question->correct_answer ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-700' }}">
                                                                {{ $optionKey }}
                                                            </span>
                                                            <span
                                                                class="text-gray-700 flex-1 ckeditor-output">{!! $option !!}</span>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @elseif($question->question_type === 'pgk_kategori')
                                            <!-- Tampilan PGK Kategori -->
                                            <div class="overflow-x-auto mt-4">
                                                <table class="w-full border-collapse border border-gray-300">
                                                    <thead>
                                                        <tr class="bg-gray-50">
                                                            <th
                                                                class="border border-gray-300 px-4 py-3 text-left font-semibold text-gray-700">
                                                                Pernyataan
                                                            </th>
                                                            @foreach ($question->custom_labels as $labelKey => $labelText)
                                                                <th
                                                                    class="border border-gray-300 px-4 py-3 text-center font-semibold text-gray-700 min-w-[100px]">
                                                                    {{ $labelText }}
                                                                </th>
                                                            @endforeach
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($question->statements as $sIndex => $statement)
                                                            @php
                                                                $userAnswerForStatement = is_array($userAnswer)
                                                                    ? $userAnswer[$sIndex] ?? null
                                                                    : null;
                                                                $correctAnswerForStatement =
                                                                    $question->correct_answers[$sIndex] ?? null;
                                                                $isStatementCorrect =
                                                                    $userAnswerForStatement ===
                                                                    $correctAnswerForStatement;
                                                            @endphp
                                                            <tr class="hover:bg-gray-50">
                                                                <td
                                                                    class="border border-gray-300 px-4 py-3 ckeditor-output">
                                                                    {!! $statement !!}
                                                                </td>
                                                                @foreach ($question->custom_labels as $labelKey => $labelText)
                                                                    <td
                                                                        class="border border-gray-300 px-4 py-3 text-center">
                                                                        <div class="flex justify-center">
                                                                            <div
                                                                                class="pgk-radio-circle w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all duration-200
                                                                {{ $labelKey === $correctAnswerForStatement ? 'border-green-500 bg-green-100' : 'border-gray-300' }}
                                                                {{ $userAnswerForStatement === $labelKey && !$isStatementCorrect ? 'border-red-500 bg-red-100' : '' }}">
                                                                                @if ($labelKey === $correctAnswerForStatement)
                                                                                    <span
                                                                                        class="text-green-600 text-sm">✓</span>
                                                                                @elseif($userAnswerForStatement === $labelKey && !$isStatementCorrect)
                                                                                    <span
                                                                                        class="text-red-600 text-sm">✗</span>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                @endforeach
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @elseif($question->question_type === 'pgk_mcma')
                                            <!-- Tampilan PGK MCMA -->
                                            <div class="space-y-3 mb-4">
                                                @foreach ($question->formatted_options as $optionKey => $option)
                                                    @if ($option)
                                                        @php
                                                            $isOptionCorrect = in_array(
                                                                $optionKey,
                                                                $question->correct_answers ?? [],
                                                            );
                                                            $isUserSelected = is_array($userAnswer)
                                                                ? in_array($optionKey, $userAnswer)
                                                                : false;
                                                        @endphp
                                                        <div
                                                            class="flex items-center p-3 border rounded-lg
                                            {{ $isOptionCorrect ? 'border-green-500 bg-green-50' : '' }}
                                            {{ $isUserSelected && !$isOptionCorrect ? 'border-red-500 bg-red-50' : '' }}">
                                                            <span
                                                                class="circle-label flex items-center justify-center w-8 h-8 rounded-full font-semibold mr-3
                                                {{ $isOptionCorrect ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-700' }}">
                                                                {{ $optionKey }}
                                                            </span>
                                                            <span
                                                                class="text-gray-700 flex-1 ckeditor-output">{!! $option !!}</span>
                                                            @if ($isUserSelected)
                                                                <span
                                                                    class="ml-2 text-sm font-semibold
                                                    {{ $isOptionCorrect ? 'text-green-600' : 'text-red-600' }}">
                                                                    {{ $isOptionCorrect ? '✓' : '✗' }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @endif

                                        <!-- Status Jawaban dan Pembahasan -->
                                        <div class="p-4 bg-gray-50 border rounded-lg text-sm text-gray-700 space-y-2 mt-4">
                                            <!-- Status Jawaban User -->
                                            @if ($userAnswer)
                                                <div
                                                    class="p-4 mb-4 rounded-lg text-[19px]
                                    {{ $isCorrect ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}
                                    font-[_Inter_Fallback_f4ae04]">
                                                    <p>
                                                    <div class="ms-3 flex flex-col justify-center leading-snug">
                                                        <strong>
                                                            @if ($question->question_type === 'pgk_kategori')
                                                                Jawaban kamu:
                                                                @if (is_array($answersDetail[$question->question_id]['mapped_answer']))
                                                                    {{ implode(', ', $answersDetail[$question->question_id]['mapped_answer']) }}
                                                                @else
                                                                    {{ $answersDetail[$question->question_id]['mapped_answer'] }}
                                                                @endif
                                                            @elseif($question->question_type === 'pgk_mcma')
                                                                Jawaban kamu:
                                                                {{ $answersDetail[$question->question_id]['mapped_answer'] }}
                                                            @else
                                                                Jawaban kamu adalah {{ $userAnswer }}
                                                            @endif
                                                        </strong>
                                                    </div>
                                                    </p>

                                                    @if ($isCorrect)
                                                        <div class="flex items-stretch p-4 mb-3 text-green-500 rounded-lg bg-white"
                                                            role="alert">
                                                            <div
                                                                class="flex items-center justify-center p-3 bg-green-100 text-green-600 rounded-xl border border-green-300 shadow-md">
                                                                <svg class="w-6 h-6" aria-hidden="true"
                                                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                                    viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd"
                                                                        d="M16.707 5.293a1 1 0 0 0-1.414 0L9 11.586 6.707 9.293a1 1 0 0 0-1.414 1.414l3 3a1 1 0 0 0 1.414 0l7-7a1 1 0 0 0 0-1.414z"
                                                                        clip-rule="evenodd" />
                                                                </svg>
                                                            </div>
                                                            <div class="ms-3 flex flex-col justify-center leading-snug">
                                                                <div class="text-xl font-medium">Yes, Jawaban kamu benar!
                                                                </div>
                                                                <div class="text-sm font-medium text-black">Try out lagi ya,
                                                                    agar makin paham. Oke!</div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="flex items-stretch p-4 rounded-lg bg-white text-red-700 border shadow-sm"
                                                            role="alert">
                                                            <div
                                                                class="flex items-center justify-center p-3 bg-red-100 rounded-lg border border-red-300">
                                                                <svg class="w-6 h-6" aria-hidden="true"
                                                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                                    viewBox="0 0 20 20">
                                                                    <path
                                                                        d="M10 0a10 10 0 1 0 10 10A10 10 0 0 0 10 0ZM13.414 6.586a1 1 0 0 1 0 1.414L11.414 10l2 2a1 1 0 0 1-1.414 1.414L10 11.414l-2 2a1 1 0 0 1-1.414-1.414l2-2-2-2a1 1 0 0 1 1.414-1.414l2 2 2-2a1 1 0 0 1 1.414 0Z" />
                                                                </svg>
                                                            </div>
                                                            <div class="ms-3 flex flex-col justify-center leading-snug">
                                                                <div class="text-xl font-medium">Jawaban kamu kurang
                                                                    tepat.</div>
                                                                <div class="text-sm font-medium text-black">Try out lagi ya,
                                                                    agar
                                                                    makin paham. Ok!</div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            @else
                                                <div class="p-4 mb-4 rounded-lg text-[17px] text-gray-700 bg-gray-200">
                                                    <p><strong>Kamu tidak menjawab soal ini.</strong></p>
                                                </div>
                                            @endif

                                            <!-- Jawaban Benar + Pembahasan -->
                                            <div class="p-4 bg-white border rounded-lg space-y-2">
                                                <div>
                                                    <strong>Jawaban Benar:</strong>
                                                    <span class="text-green-600 font-semibold ml-2">
                                                        @if ($question->question_type === 'pgk_kategori')
                                                            @if (isset($question->mapped_correct_answers) && is_array($question->mapped_correct_answers))
                                                                {{ implode(', ', $question->mapped_correct_answers) }}
                                                            @else
                                                                {{ is_array($question->correct_answers) ? implode(', ', $question->correct_answers) : $question->correct_answers }}
                                                            @endif
                                                        @elseif($question->question_type === 'pgk_mcma')
                                                            {{ is_array($question->correct_answers) ? implode(', ', $question->correct_answers) : $question->correct_answers }}
                                                        @else
                                                            {{ $question->correct_answer }}
                                                        @endif

                                                    </span>
                                                </div>

                                                <div>
                                                    <strong>Pembahasan:</strong>
                                                    <div class="ckeditor-output mt-1">
                                                        {!! $question->pembahasan ?? 'Belum ada pembahasan untuk soal ini.' !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <!-- Tombol Navigasi -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <div class="flex justify-between items-center">
                            <button type="button" id="prev-btn"
                                class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium transition-colors flex items-center disabled:opacity-50 disabled:cursor-not-allowed"
                                onclick="previousQuestion()" disabled>
                                <i class="fa-solid fa-arrow-left mr-2"></i>
                                Sebelumnya
                            </button>

                            <button type="button" id="next-btn"
                                class="bg-primary-100 hover:bg-primary-200 text-white px-6 py-3 rounded-lg font-medium transition-colors flex items-center"
                                onclick="nextQuestion()">
                                Selanjutnya
                                <i class="fa-solid fa-arrow-right ml-2"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div id="party-popper-container" class="fixed top-0 left-0 w-full h-0 overflow-visible z-50 pointer-events-none"></div>
@endsection

@push('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.css">
    <style>
        .party-popper {
            position: absolute;
            top: -50px;
            width: 12px;
            height: 18px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 18'%3E%3Cpath fill='%23FFD700' d='M6 0L7.5 5H12L8.5 8L10 13L6 10L2 13L3.5 8L0 5H4.5L6 0Z'/%3E%3C/svg%3E");
            background-size: contain;
            background-repeat: no-repeat;
            animation: fall linear forwards;
            pointer-events: none;
        }

        @keyframes fall {
            to {
                transform: translateY(calc(100vh + 50px)) rotate(360deg);
            }
        }

        .option-label .circle-label {
            background-color: #f3f4f6;
            /* abu terang */
            color: #374151;
            /* abu tua */
            border: 2px solid #d1d5db;
            /* abu border */
            transition: all 0.3s ease;
        }

        /* Kotak jawaban saat dipilih */
        .option-label.selected {
            border-color: #2563eb;
            /* biru */
            background-color: #eff6ff;
            /* biru sangat muda */
        }

        /* Lingkaran huruf saat dipilih */
        .option-label.selected .circle-label {
            background-color: #2563eb;
            /* biru solid */
            color: white;
            /* teks putih */
            border-color: #2563eb;
            box-shadow: 0 0 6px rgba(37, 99, 235, 0.6);
        }

        .question-card .text-lg {
            font-size: 0.95rem;
            /* lebih kecil dari default text-lg */
        }

        /* Ukuran font jawaban */
        .question-card .option-label span {
            font-size: 0.9rem;
            /* perkecil teks jawaban */
        }

        /* Tabel tetap rapi */
        .question-card table {
            width: 100%;
            border: 1px solid #d1d5db;
            border-collapse: collapse;
            font-size: 0.85rem;
            /* kecilkan font dalam tabel juga */
        }

        .question-card table th,
        .question-card table td {
            border: 1px solid #d1d5db;
            padding: 6px;
            text-align: center;
        }

        .question-card figure.table {
            display: block;
            overflow-x: auto;
        }
    </style>
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.js"></script>
    <script>
        // === Function render LaTeX dengan KaTeX ===
        function renderLatex(content) {
            if (!content) return content;

            // Block mode $$...$$
            content = content.replace(/\$\$([\s\S]+?)\$\$/g, (match, latex) => {
                try {
                    return katex.renderToString(latex, {
                        throwOnError: false,
                        displayMode: true
                    });
                } catch {
                    return match;
                }
            });

            // Inline mode $...$
            content = content.replace(/(?:^|[^$])\$([^$]+)\$(?!\$)/g, (match, latex) => {
                const prefix = match[0][0] === '$' ? '' : match[0][0];
                try {
                    return prefix + katex.renderToString(latex, {
                        throwOnError: false,
                        displayMode: false
                    });
                } catch {
                    return match;
                }
            });

            return content;
        }

        function applyLatexRender() {
            document.querySelectorAll(
                '.question-card .text-lg, ' + // Soal
                '.question-card .option-label, ' + // Opsi jawaban
                '.question-card .text-gray-700, ' + // Opsi isi span
                '.question-card .pembahasan' // Pembahasan
            ).forEach(el => {
                el.innerHTML = renderLatex(el.innerHTML);
            });
        }

        document.addEventListener("DOMContentLoaded", function() {
            applyLatexRender();
        });
    </script>
    <script>
        let currentQuestion = 1;
        const totalQuestions = {{ count($questions) }};

        function showQuestion(num) {
            document.querySelectorAll('.question-card').forEach(card => card.classList.add('hidden'));
            document.querySelector(`.question-card[data-question="${num}"]`).classList.remove('hidden');

            document.getElementById('prev-btn').disabled = num === 1;
            document.getElementById('next-btn').disabled = num === totalQuestions;

            currentQuestion = num;
        }

        function nextQuestion() {
            if (currentQuestion < totalQuestions) showQuestion(currentQuestion + 1);
        }

        function previousQuestion() {
            if (currentQuestion > 1) showQuestion(currentQuestion - 1);
        }

        function goToQuestion(num) {
            showQuestion(num);
        }

        document.addEventListener('DOMContentLoaded', function() {
            createPartyPoppers();
        });

        function createPartyPoppers() {
            const container = document.getElementById('party-popper-container');
            const colors = ['#FFD700', '#FF4136', '#0074D9', '#2ECC40', '#B10DC9', '#FF851B'];

            // Create 30 poppers
            for (let i = 0; i < 30; i++) {
                const popper = document.createElement('div');
                popper.className = 'party-popper';

                // Random position
                const left = Math.random() * 100;
                popper.style.left = `${left}%`;

                // Random delay
                const delay = Math.random() * 2;
                popper.style.animationDelay = `${delay}s`;

                // Random duration (3-6 seconds)
                const duration = 3 + Math.random() * 3;
                popper.style.animationDuration = `${duration}s`;

                // Random color
                const color = colors[Math.floor(Math.random() * colors.length)];
                popper.style.backgroundColor = color;
                popper.style.backgroundImage =
                    `url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 18'%3E%3Cpath fill='${encodeURIComponent(color)}' d='M6 0L7.5 5H12L8.5 8L10 13L6 10L2 13L3.5 8L0 5H4.5L6 0Z'/%3E%3C/svg%3E")`;

                container.appendChild(popper);

                // Remove popper after animation completes
                setTimeout(() => {
                    popper.remove();
                }, (duration + delay) * 1000);
            }
        }
    </script>
@endpush
