@extends('layouts.app')
@section('title', 'Hasil Try Out - ' . $quiz->title)

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
                                function formatDuration($seconds)
                                {
                                    $h = floor($seconds / 3600);
                                    $m = floor(($seconds % 3600) / 60);
                                    $s = $seconds % 60;

                                    return sprintf('%02d:%02d:%02d', $h, $m, $s);
                                }
                            @endphp
                            <span
                                class="inline-block px-3 py-1 text-sm rounded-full bg-green-100 text-green-700 font-medium ml-2">
                                Nilai: {{ $nilai }}
                            </span>

                            <span
                                class="inline-block px-3 py-1 text-sm rounded-full bg-purple-100 text-purple-700 font-medium ml-2">
                                Waktu Pengerjaan : {{ formatDuration($duration) }}
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
                                $isCorrect = $userAnswer === $question->correct_answer;
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
                            $isCorrect = $userAnswer === $question->correct_answer;
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
                                        <div class="text-lg font-medium text-gray-900 mb-4">
                                            {!! $question->question !!}
                                        </div>

                                        <!-- Pilihan -->
                                        <div class="space-y-3 mb-4">
                                            @foreach ($question->formatted_options as $optionKey => $option)
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
                                                        <span class="text-gray-700 flex-1">{!! $option !!}</span>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>

                                        <!-- Pembahasan + Jawaban -->
                                        <div class="p-4 bg-gray-50 border rounded-lg text-sm text-gray-700 space-y-2">
                                            <!-- Jawaban User -->
                                            @if ($userAnswer)
                                                <div
                                                    class="p-4 mb-4 rounded-lg text-sm text-white
        {{ $isCorrect ? 'bg-green-500' : 'bg-red-500' }}">
                                                    <p><strong>Jawaban kamu adalah {{ $userAnswer }}</strong></p>

                                                    @if ($isCorrect)
                                                        <p class="mt-1">Benar 🎉</p>
                                                        <p class="mt-1">Mantap! Pertahankan terus ya 💪</p>
                                                    @else
                                                        <p class="mt-1">Salah ❌</p>
                                                        <p class="mt-1">Jawaban kamu kurang tepat</p>
                                                        <p class="mt-1">tryout terus supaya makin mahir, ya!</p>
                                                    @endif
                                                </div>
                                            @else
                                                <div class="p-4 mb-4 rounded-lg text-sm text-gray-700 bg-gray-200">
                                                    <p><strong>Kamu tidak menjawab soal ini.</strong></p>
                                                </div>
                                            @endif

                                            <!-- Jawaban Benar + Pembahasan -->
                                            <div class="p-4 bg-gray-50 border rounded-lg text-sm text-gray-700 space-y-2">
                                                <div>
                                                    <strong>Jawaban Benar:</strong>
                                                    <span class="text-green-600 font-semibold">
                                                        {{ $question->correct_answer }}. {!! $question->formatted_options[$question->correct_answer] ?? '-' !!}
                                                    </span>
                                                </div>

                                                <div>
                                                    <strong>Pembahasan:</strong>
                                                    {!! $question->pembahasan ?? 'Belum ada pembahasan untuk soal ini.' !!}
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
