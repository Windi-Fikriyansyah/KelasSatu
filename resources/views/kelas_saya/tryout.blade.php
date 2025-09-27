@extends('layouts.app')
@section('title', 'Tryout - ' . $quiz->title)
@section('content')
    <section class="py-8 bg-gradient-to-b from-primary-50 to-white min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <a href="javascript:history.back()"
                            class="mr-4 text-gray-600 hover:text-primary-100 transition-colors">
                            <i class="fa-solid fa-arrow-left text-xl"></i>
                        </a>
                        <div>
                            <p class="text-gray-600">{{ $quiz->title }}</p>
                        </div>

                    </div>
                    <div class="flex justify-center mb-4">
                        <div id="quiz-timer" class="bg-red-50 border border-red-200 rounded-lg px-6 py-3">
                            <div class="flex items-center justify-center space-x-2">
                                <i class="fa-solid fa-clock text-red-600"></i>
                                <span class="text-lg font-bold text-red-600" id="timer-display">
                                    {{ gmdate('H:i:s', $quizDuration) }}
                                </span>
                                <span class="text-red-600 font-medium">Sisa Waktu</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Layout 2 Kolom -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

                <!-- Navigasi Soal (Kiri) -->
                <div class="bg-white rounded-xl shadow-lg p-6 h-fit lg:sticky lg:top-24">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Navigasi Soal</h3>
                    @if ($quiz->mapel === 'wajib' && !empty($questionRanges))
                        <!-- Tabs untuk jenis soal -->
                        <div class="mb-4">
                            <div class="flex flex-wrap gap-2 mb-3">
                                @if (isset($questionRanges) && !empty($questionRanges))
                                    @php
                                        // Urutkan questionRanges berdasarkan order
                                        $sortedRanges = collect($questionRanges)->sortBy('order');
                                    @endphp
                                    @foreach ($sortedRanges as $type => $data)
                                        <button type="button"
                                            class="tab-button px-3 py-1 text-xs rounded-full border transition-all {{ $loop->first ? 'active' : '' }}"
                                            data-type="{{ $type }}">
                                            {{ $data['range'] }}
                                        </button>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    @endif
                    <div class="grid grid-cols-8 lg:grid-cols-3 gap-2">
                        @foreach ($questions as $question)
                            <button type="button"
                                class="question-nav w-10 h-10 rounded-lg border border-gray-300 text-gray-600 hover:border-primary-100 hover:bg-primary-50 transition-all duration-200 {{ $question->display_number === 1 ? 'bg-primary-100 text-white border-primary-100' : '' }}"
                                data-question="{{ $question->display_number }}"
                                data-question-type="{{ $question->question_type }}"
                                onclick="goToQuestion({{ $question->display_number }})">
                                {{ $question->display_number }}
                            </button>
                        @endforeach
                    </div>
                    <div class="flex flex-col gap-2 mt-4 text-sm">
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-primary-100 rounded mr-2"></div>
                            <span class="text-gray-600">Sedang dikerjakan</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-green-500 rounded mr-2"></div>
                            <span class="text-gray-600">Sudah dijawab</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 border border-gray-300 rounded mr-2"></div>
                            <span class="text-gray-600">Belum dijawab</span>
                        </div>
                    </div>
                </div>

                <!-- Soal & Navigasi (Kanan) -->
                <div class="lg:col-span-3">
                    <form id="quiz-form" method="POST" action="{{ route('kelas.tryout.submit', $quiz->id) }}">
                        @csrf
                        <input type="hidden" name="duration" id="duration">
                        @foreach ($questions as $index => $question)
                            <div class="question-card bg-white rounded-xl shadow-lg overflow-hidden mb-6 {{ $index > 0 ? 'hidden' : '' }}"
                                data-question="{{ $index + 1 }}">
                                <div class="p-6">
                                    <div class="flex items-start mb-6">
                                        <div
                                            class="bg-primary-100 text-white rounded-full w-8 h-8 flex items-center justify-center font-semibold mr-4 flex-shrink-0">
                                            {{ $question->display_number }}
                                        </div>
                                        <div class="flex-1">
                                            <div class="ckeditor-output">
                                                {!! $question->question !!}
                                            </div>

                                            {{-- === Multiple Choice === --}}
                                            @if ($question->question_type === 'multiple_choice')
                                                <div class="space-y-3">
                                                    @foreach ($question->formatted_options as $optionKey => $option)
                                                        @if ($option)
                                                            <label
                                                                class="option-label flex items-center p-4 border rounded-lg cursor-pointer transition-all"
                                                                data-question="{{ $question->question_id }}"
                                                                data-value="{{ $optionKey }}">
                                                                <input type="radio"
                                                                    name="answers[{{ $question->question_id }}]"
                                                                    value="{{ $optionKey }}" class="hidden">
                                                                <span
                                                                    class="circle-label w-8 h-8 flex items-center justify-center rounded-full font-semibold mr-3">
                                                                    {{ $optionKey }}
                                                                </span>
                                                                <span
                                                                    class="ckeditor-output flex-1">{!! $option !!}</span>
                                                            </label>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @endif

                                            {{-- === PGK Kategori === --}}
                                            {{-- === PGK Kategori === --}}
                                            @if ($question->question_type === 'pgk_kategori')
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
                                                                <tr class="hover:bg-gray-50">
                                                                    <td class="border border-gray-300 px-4 py-3">
                                                                        <div class="ckeditor-output">
                                                                            {!! $statement !!}
                                                                        </div>
                                                                    </td>
                                                                    @foreach ($question->custom_labels as $labelKey => $labelText)
                                                                        <td
                                                                            class="border border-gray-300 px-4 py-3 text-center">
                                                                            <div class="flex justify-center">
                                                                                <label
                                                                                    class="pgk-radio-label cursor-pointer inline-block">
                                                                                    <input type="radio"
                                                                                        name="answers[{{ $question->question_id }}][{{ $sIndex }}]"
                                                                                        value="{{ $labelKey }}"
                                                                                        class="pgk-radio-input hidden">
                                                                                    <div
                                                                                        class="pgk-radio-circle w-6 h-6 rounded-full border-2 border-gray-300 bg-white flex items-center justify-center transition-all duration-200 hover:border-green-400">
                                                                                        <span
                                                                                            class="pgk-radio-check text-green-600 text-sm opacity-0 transition-opacity duration-200">
                                                                                            ✓
                                                                                        </span>
                                                                                    </div>

                                                                                </label>
                                                                            </div>
                                                                        </td>
                                                                    @endforeach
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @endif

                                            {{-- === PGK MCMA (Multiple Choice Multiple Answer) === --}}
                                            @if ($question->question_type === 'pgk_mcma')
                                                <div class="space-y-3">
                                                    @foreach ($question->formatted_options as $optionKey => $option)
                                                        @if ($option)
                                                            <label
                                                                class="option-label flex items-center p-4 border rounded-lg cursor-pointer transition-all"
                                                                data-question="{{ $question->question_id }}"
                                                                data-value="{{ $optionKey }}">
                                                                <input type="checkbox"
                                                                    name="answers[{{ $question->question_id }}][]"
                                                                    value="{{ $optionKey }}" class="hidden">
                                                                <span
                                                                    class="circle-label w-8 h-8 flex items-center justify-center rounded-full font-semibold mr-3">
                                                                    {{ $optionKey }}
                                                                </span>
                                                                <span
                                                                    class="ckeditor-output flex-1">{!! $option !!}</span>
                                                            </label>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @endif
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

                                <div class="flex space-x-4">
                                    <button type="button" id="next-btn"
                                        class="bg-primary-100 hover:bg-primary-200 text-white px-6 py-3 rounded-lg font-medium transition-colors flex items-center"
                                        onclick="nextQuestion()">
                                        Selanjutnya
                                        <i class="fa-solid fa-arrow-right ml-2"></i>
                                    </button>

                                    <button type="button" id="submit-btn"
                                        class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-lg font-medium transition-colors hidden"
                                        onclick="confirmSubmit()">
                                        Selesai
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Konfirmasi Submit Modal -->
    <div id="submit-modal" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md">
                    <h3 class="text-lg font-semibold text-center text-gray-900 mb-2">Konfirmasi Submit</h3>
                    <p class="text-gray-600 text-center mb-6">Apakah Anda yakin ingin menyelesaikan tryout ini?</p>
                    <div class="flex space-x-4">
                        <button type="button"
                            class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg font-medium transition-colors"
                            onclick="closeModal()">
                            Batal
                        </button>
                        <button type="button"
                            class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors"
                            onclick="submitQuiz()">
                            Ya, Selesai
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.css">
    <style>
        .tab-button {
            background-color: #f9fafb;
            color: #6b7280;
            border-color: #d1d5db;
        }

        .tab-button.active {
            background-color: #2563eb;
            color: white;
            border-color: #2563eb;
        }

        .tab-button:hover:not(.active) {
            background-color: #e5e7eb;
            border-color: #9ca3af;
        }


        .pgk-radio-input:checked+.pgk-radio-circle {
            border-color: #16a34a;
            /* hijau */
            background-color: #dcfce7;
            /* hijau muda */
        }

        .pgk-radio-input:checked+.pgk-radio-circle .pgk-radio-check {
            opacity: 1;
            /* tampilkan ceklis */
        }

        /* Hover state */
        .pgk-radio-label:hover .pgk-radio-circle {
            border-color: #22c55e;
            /* hijau terang */
            background-color: #f0fdf4;
            /* hijau sangat muda */
        }

        /* Table styling untuk PGK Kategori */
        .pgk-kategori-table {
            font-size: 0.9rem;
        }

        .pgk-kategori-table th {
            background-color: #f9fafb;
            font-weight: 600;
        }

        .pgk-kategori-table td {
            vertical-align: middle;
        }

        /* Responsif untuk mobile */
        @media (max-width: 768px) {
            .pgk-kategori-table {
                font-size: 0.8rem;
            }

            .pgk-kategori-table th,
            .pgk-kategori-table td {
                padding: 8px 4px;
            }

            .pgk-radio-circle {
                width: 20px;
                height: 20px;
            }

            .pgk-radio-dot {
                width: 10px;
                height: 10px;
            }
        }

        .ckeditor-output * {
            all: revert;
            /* atau bisa all: unset; tergantung kebutuhan */
        }

        .prose h1,
        .prose h2,
        .prose h3,
        .prose h4,
        .prose h5,
        .prose h6 {
            all: revert;
            /* reset ke default browser / bawaan inline style */
            margin-top: 1rem;
            margin-bottom: 0.5rem;
        }

        .prose h1 {
            font-size: 2rem;
        }

        .prose h2 {
            font-size: 1.5rem;
        }

        .prose h3 {
            font-size: 1.25rem;
        }

        .option-label input[type="checkbox"]:checked~.circle-label {
            background-color: #2563eb;
            color: white;
            border-color: #2563eb;
            box-shadow: 0 0 6px rgba(37, 99, 235, 0.6);
        }

        .option-label input[type="checkbox"]:checked {
            /* Untuk visual feedback */
        }

        .option-label.selected.checkbox-selected {
            border-color: #2563eb;
            background-color: #eff6ff;
        }

        .option-label.checkbox-selected .circle-label {
            background-color: #2563eb;
            color: white;
            border-color: #2563eb;
            box-shadow: 0 0 6px rgba(37, 99, 235, 0.6);
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

        .question-nav {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #d1d5db;
            color: #6b7280;
            background-color: white;
            transition: all 0.2s ease;
        }

        .question-nav.bg-primary-100 {
            border-color: #2563eb;
        }

        .question-nav.bg-green-500 {
            border-color: #10b981;
        }

        /* Timer Styles */
        #quiz-timer {
            transition: all 0.3s ease;
        }

        .timer-warning {
            animation: pulse 1s infinite;
        }

        @keyframes pulse {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }

            100% {
                opacity: 1;
            }
        }
    </style>
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.js"></script>
    <script>
        let quizDuration = {{ $quizDuration }}; // Durasi dalam detik dari controller
        let timeLeft = quizDuration;
        let timerInterval;

        function startTimer() {
            // Cek jika ada waktu tersisa di localStorage (untuk resume)
            const savedTime = localStorage.getItem('quiz_time_left_{{ $quiz->id }}');
            if (savedTime) {
                timeLeft = parseInt(savedTime, 10);

                // Jika waktu sudah habis, submit otomatis
                if (timeLeft <= 0) {
                    autoSubmitQuiz();
                    return;
                }
            }

            timerInterval = setInterval(function() {
                timeLeft--;

                // Simpan waktu tersisa ke localStorage setiap detik
                localStorage.setItem('quiz_time_left_{{ $quiz->id }}', timeLeft.toString());

                // Update display
                updateTimerDisplay();

                // Warna warning saat waktu hampir habis
                if (timeLeft <= 300) { // 5 menit tersisa
                    document.getElementById('quiz-timer').classList.remove('bg-red-50', 'border-red-200');
                    document.getElementById('quiz-timer').classList.add('bg-red-100', 'border-red-300');
                }

                if (timeLeft <= 60) { // 1 menit tersisa
                    document.getElementById('quiz-timer').classList.remove('bg-red-100', 'border-red-300');
                    document.getElementById('quiz-timer').classList.add('bg-red-200', 'border-red-400');

                    // Blink effect untuk 30 detik terakhir
                    if (timeLeft <= 30) {
                        document.getElementById('quiz-timer').classList.toggle('bg-red-300');
                    }
                }

                // Auto submit ketika waktu habis
                if (timeLeft <= 0) {
                    clearInterval(timerInterval);
                    autoSubmitQuiz();
                }
            }, 1000);
        }

        function updateTimerDisplay() {
            const hours = Math.floor(timeLeft / 3600);
            const minutes = Math.floor((timeLeft % 3600) / 60);
            const seconds = timeLeft % 60;

            const timerDisplay = document.getElementById('timer-display');
            if (timerDisplay) {
                timerDisplay.textContent =
                    `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            }
        }

        function autoSubmitQuiz() {
            Swal.fire({
                title: 'Waktu Habis!',
                text: 'Waktu pengerjaan tryout telah berakhir.',
                icon: 'warning',
                confirmButtonText: 'Lihat Hasil',
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then((result) => {
                // Set duration untuk auto submit
                document.getElementById('duration').value = quizDuration;
                submitQuiz();
            });
        }
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-activate first tab on page load
            const firstVisibleTab = document.querySelector('.tab-button');
            if (firstVisibleTab) {
                firstVisibleTab.classList.add('active');

                // Apply filter based on first tab
                const selectedType = firstVisibleTab.dataset.type;
                document.querySelectorAll('.question-nav').forEach(navBtn => {
                    const questionType = navBtn.dataset.questionType;
                    if (questionType === selectedType) {
                        navBtn.style.display = 'inline-flex';
                    } else {
                        navBtn.style.display = 'none';
                    }
                });
            }

            // Tab button event listeners
            document.querySelectorAll('.tab-button').forEach(button => {
                button.addEventListener('click', function() {
                    const selectedType = this.dataset.type;

                    // Update active tab
                    document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove(
                        'active'));
                    this.classList.add('active');

                    // Filter navigation buttons
                    document.querySelectorAll('.question-nav').forEach(navBtn => {
                        const questionType = navBtn.dataset.questionType;
                        if (questionType === selectedType) {
                            navBtn.style.display = 'inline-flex';
                        } else {
                            navBtn.style.display = 'none';
                        }
                    });

                    updateQuestionNav();
                });
            });
        });
        // === Function render LaTeX dengan KaTeX ===
        function renderLatex(content) {
            const inlineRegex = /\$([^$]+)\$/g;
            const blockRegex = /\$\$([^$]+)\$\$/g;

            let renderedContent = content;

            renderedContent = renderedContent.replace(blockRegex, (match, latex) => {
                try {
                    return katex.renderToString(latex, {
                        throwOnError: false,
                        displayMode: true
                    });
                } catch (e) {
                    return match;
                }
            });

            renderedContent = renderedContent.replace(inlineRegex, (match, latex) => {
                try {
                    return katex.renderToString(latex, {
                        throwOnError: false,
                        displayMode: false
                    });
                } catch (e) {
                    return match;
                }
            });

            return renderedContent;
        }

        function applyLatexRender() {
            document.querySelectorAll('.ckeditor-output').forEach(el => {
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
        const storageKey = "quiz_answers_{{ $quiz->id }}";

        function debugPGKStructure() {
            console.log('=== DEBUG PGK KATEGORI ===');

            // Cek semua input PGK
            const pgkInputs = document.querySelectorAll('.pgk-radio-input');
            console.log('Total PGK inputs found:', pgkInputs.length);

            pgkInputs.forEach((input, index) => {
                console.log(`PGK Input ${index}:`, {
                    name: input.name,
                    value: input.value,
                    checked: input.checked,
                    element: input
                });
            });

            // Cek localStorage
            const saved = localStorage.getItem(storageKey);
            if (saved) {
                console.log('Saved data in localStorage:', JSON.parse(saved));
            }
        }

        function saveAnswers() {
            let answers = {};

            console.log('=== SAVING ANSWERS ===');

            // Handle radio buttons (multiple choice dan pgk kategori)
            document.querySelectorAll('input[type="radio"]:checked').forEach(input => {
                console.log('Processing radio:', input.name, input.value);

                // PERBAIKAN REGEX - Menangkap format answers[qid][subindex] dengan benar
                const nameMatch = input.name.match(/^answers\[(\d+)\](?:\[([^\]]+)\])?$/);

                if (nameMatch) {
                    const qid = nameMatch[1];
                    const subIndex = nameMatch[2]; // ini bisa berupa huruf atau angka

                    console.log('Matched:', {
                        qid,
                        subIndex,
                        value: input.value
                    });

                    if (subIndex !== undefined && subIndex !== null) {
                        // PGK Kategori - jawaban berbentuk object
                        if (!answers[qid]) answers[qid] = {};
                        answers[qid][subIndex] = input.value;
                        console.log('Saved PGK answer:', qid, subIndex, input.value);
                    } else {
                        // Multiple Choice - jawaban tunggal
                        answers[qid] = input.value;
                        console.log('Saved MC answer:', qid, input.value);
                    }
                } else {
                    console.warn('Could not match input name:', input.name);
                }
            });

            // Handle checkboxes (pgk_mcma)
            document.querySelectorAll('input[type="checkbox"]:checked').forEach(input => {
                const nameMatch = input.name.match(/^answers\[(\d+)\]\[\]$/);
                if (nameMatch) {
                    const qid = nameMatch[1];
                    if (!answers[qid]) answers[qid] = [];
                    answers[qid].push(input.value);
                    console.log('Saved checkbox answer:', qid, input.value);
                }
            });

            // Handle textarea
            document.querySelectorAll('textarea').forEach(input => {
                const nameMatch = input.name.match(/^answers\[(\d+)\]$/);
                if (nameMatch) {
                    const qid = nameMatch[1];
                    const value = input.value.trim();
                    if (value) {
                        answers[qid] = value;
                        console.log('Saved textarea answer:', qid, value);
                    }
                }
            });

            console.log('Final answers to save:', answers);
            localStorage.setItem(storageKey, JSON.stringify(answers));
        }

        // PERBAIKI FUNGSI loadAnswers - Untuk menangani loading PGK dengan benar
        function loadAnswers() {
            const saved = localStorage.getItem(storageKey);
            if (!saved) {
                console.log('No saved data found');
                return;
            }

            const answers = JSON.parse(saved);
            console.log('=== LOADING ANSWERS ===');
            console.log('Data to load:', answers);

            Object.keys(answers).forEach(qid => {
                const val = answers[qid];
                console.log(`Loading question ${qid}:`, val);

                if (typeof val === 'object' && !Array.isArray(val)) {
                    // PGK Kategori - val adalah object dengan subIndex
                    console.log(`Question ${qid} is PGK Kategori`);
                    Object.keys(val).forEach(subIndex => {
                        const inputName = `answers[${qid}][${subIndex}]`;
                        const inputValue = val[subIndex];
                        const selector = `input[name="${inputName}"][value="${inputValue}"]`;

                        console.log('Looking for PGK input:', {
                            inputName,
                            inputValue,
                            selector
                        });

                        const radio = document.querySelector(selector);
                        if (radio) {
                            console.log('Found PGK radio, setting checked');
                            radio.checked = true;

                            // Update visual feedback
                            const label = radio.closest('.pgk-radio-label');
                            if (label) {
                                const circle = label.querySelector('.pgk-radio-circle');
                                const check = label.querySelector('.pgk-radio-check');
                                if (circle) {
                                    circle.style.borderColor = '#16a34a';
                                    circle.style.backgroundColor = '#dcfce7';
                                }
                                if (check) {
                                    check.style.opacity = '1';
                                }
                            }
                        } else {
                            console.error('PGK radio not found:', selector);
                            // Debug: tampilkan semua input untuk question ini
                            const allInputsForQ = document.querySelectorAll(
                                `input[name^="answers[${qid}]"]`);
                            console.log(`All inputs for question ${qid}:`, allInputsForQ);
                            allInputsForQ.forEach(inp => {
                                console.log(' -', inp.name, inp.value, inp.checked);
                            });
                        }
                    });
                } else if (Array.isArray(val)) {
                    // PGK MCMA - val adalah array
                    console.log(`Question ${qid} is checkbox array`);
                    val.forEach(checkVal => {
                        const checkbox = document.querySelector(
                            `input[name="answers[${qid}][]"][value="${checkVal}"]`);
                        if (checkbox) {
                            checkbox.checked = true;
                            const label = checkbox.closest('.option-label');
                            if (label) {
                                label.classList.add('selected', 'checkbox-selected');
                            }
                        }
                    });
                } else {
                    // Multiple Choice atau Essay - val adalah string
                    console.log(`Question ${qid} is single value`);
                    const radio = document.querySelector(`input[name="answers[${qid}]"][value="${val}"]`);
                    const textarea = document.querySelector(`textarea[name="answers[${qid}]"]`);

                    if (radio) {
                        radio.checked = true;
                        const label = radio.closest('.option-label');
                        if (label) label.classList.add('selected');
                    }

                    if (textarea) {
                        textarea.value = val;
                    }
                }
            });
        }

        // 3. PERBAIKI FUNGSI isQuestionAnswered() untuk pgk_kategori
        function isQuestionAnswered(questionNum) {
            const questionCard = document.querySelector(`.question-card[data-question="${questionNum}"]`);

            if (questionCard.querySelector('input[type="radio"]')) {
                // Cek apakah ini PGK Kategori dengan melihat apakah ada tbody
                const pgkTable = questionCard.querySelector('tbody');
                if (pgkTable) {
                    // Ini PGK Kategori - hitung berapa statement yang harus dijawab
                    const totalStatements = pgkTable.querySelectorAll('tr').length;
                    const answeredStatements = pgkTable.querySelectorAll('input[type="radio"]:checked').length;
                    return answeredStatements === totalStatements;
                } else {
                    // Multiple choice biasa
                    const radios = questionCard.querySelectorAll('input[type="radio"]:checked');
                    return radios.length > 0;
                }
            } else if (questionCard.querySelector('input[type="checkbox"]')) {
                return questionCard.querySelector('input[type="checkbox"]:checked') !== null;
            } else if (questionCard.querySelector('textarea')) {
                return questionCard.querySelector('textarea').value.trim() !== '';
            }
            return false;
        }

        function updateQuestionNav() {
            document.querySelectorAll('.question-nav').forEach((btn, index) => {
                const questionNum = index + 1;
                const answered = isQuestionAnswered(questionNum);

                // Hanya update tombol yang sedang visible (tidak hidden)
                if (btn.style.display !== 'none') {
                    btn.classList.remove('bg-primary-100', 'text-white', 'border-primary-100', 'bg-green-500',
                        'text-white');

                    if (questionNum === currentQuestion) {
                        btn.classList.add('bg-primary-100', 'text-white', 'border-primary-100');
                    } else if (answered) {
                        btn.classList.add('bg-green-500', 'text-white');
                    } else {
                        // Reset ke state default untuk tombol yang belum dijawab
                        btn.classList.add('border-gray-300', 'text-gray-600');
                        btn.classList.remove('bg-primary-100', 'bg-green-500', 'text-white');
                    }
                }
            });
        }
        document.addEventListener('DOMContentLoaded', function() {
            // Event listener untuk PGK Kategori radio buttons - DIPERBAIKI
            document.querySelectorAll('.pgk-radio-label').forEach(label => {
                label.addEventListener('click', function(e) {
                    e.preventDefault(); // Prevent double firing

                    const input = this.querySelector('.pgk-radio-input');
                    if (input && !input.checked) {
                        // Uncheck other radios in the same row
                        const row = input.closest('tr');
                        if (row) {
                            row.querySelectorAll('.pgk-radio-input').forEach(radio => {
                                radio.checked = false;
                                // Reset visual
                                const circle = radio.parentElement.querySelector(
                                    '.pgk-radio-circle');
                                const check = radio.parentElement.querySelector(
                                    '.pgk-radio-check');
                                if (circle && check) {
                                    circle.style.borderColor = '#d1d5db';
                                    circle.style.backgroundColor = 'white';
                                    check.style.opacity = '0';
                                }
                            });
                        }

                        // Check current radio
                        input.checked = true;

                        // Update visual
                        const circle = this.querySelector('.pgk-radio-circle');
                        const check = this.querySelector('.pgk-radio-check');
                        if (circle && check) {
                            circle.style.borderColor = '#16a34a';
                            circle.style.backgroundColor = '#dcfce7';
                            check.style.opacity = '1';
                        }

                        // Save and update navigation
                        saveAnswers();
                        updateQuestionNav();
                    }
                });
            });

            // Event listener untuk input changes - DIPERBAIKI
            document.querySelectorAll('input[type="radio"], input[type="checkbox"], textarea').forEach(input => {
                input.addEventListener('change', function() {
                    console.log('Input changed:', this.name, this.value, this.checked); // Debug log
                    saveAnswers();
                    updateQuestionNav();
                });

                if (input.tagName.toLowerCase() === 'textarea') {
                    input.addEventListener('input', function() {
                        saveAnswers();
                        updateQuestionNav();
                    });
                }
            });

            // Event listener untuk option labels (multiple choice dan pgk_mcma)
            document.querySelectorAll('.option-label').forEach(label => {
                label.addEventListener('click', function() {
                    const questionId = this.dataset.question;
                    const input = this.querySelector('input[type="radio"], input[type="checkbox"]');

                    if (input) {
                        if (input.type === "radio") {
                            // Reset pilihan lain dalam soal yang sama
                            document.querySelectorAll(
                                    `.option-label[data-question="${questionId}"]`)
                                .forEach(el => el.classList.remove('selected'));

                            this.classList.add('selected');
                            input.checked = true;

                        } else if (input.type === "checkbox") {
                            // Toggle checkbox
                            if (input.checked) {
                                this.classList.remove('selected', 'checkbox-selected');
                                input.checked = false;
                            } else {
                                this.classList.add('selected', 'checkbox-selected');
                                input.checked = true;
                            }
                        }

                        saveAnswers();
                        updateQuestionNav();
                    }
                });
            });

            // Load saved answers and update navigation
            loadAnswers();
            updateQuestionNav();

            console.log('All event listeners initialized'); // Debug log
        });

        function showQuestion(questionNum) {
            document.querySelectorAll('.question-card').forEach(card => card.classList.add('hidden'));
            document.querySelector(`.question-card[data-question="${questionNum}"]`).classList.remove('hidden');
            document.getElementById('prev-btn').disabled = questionNum === 1;

            if (questionNum === totalQuestions) {
                document.getElementById('next-btn').classList.add('hidden');
                document.getElementById('submit-btn').classList.remove('hidden');
            } else {
                document.getElementById('next-btn').classList.remove('hidden');
                document.getElementById('submit-btn').classList.add('hidden');
            }

            // Auto-update tab when showing question
            const questionNavBtn = document.querySelector(`.question-nav[data-question="${questionNum}"]`);
            if (questionNavBtn) {
                const questionType = questionNavBtn.dataset.questionType;
                const correspondingTab = document.querySelector(`.tab-button[data-type="${questionType}"]`);
                if (correspondingTab && !correspondingTab.classList.contains('active')) {
                    // Trigger click on the corresponding tab
                    correspondingTab.click();
                }
            }

            updateQuestionNav();
        }

        function nextQuestion() {
            if (currentQuestion < totalQuestions) {
                currentQuestion++;
                showQuestion(currentQuestion);
            }
        }

        function previousQuestion() {
            if (currentQuestion > 1) {
                currentQuestion--;
                showQuestion(currentQuestion);
            }
        }

        function goToQuestion(questionNum) {
            currentQuestion = questionNum;
            showQuestion(currentQuestion);

            // Auto-switch to appropriate tab based on question type
            const questionNavBtn = document.querySelector(`.question-nav[data-question="${questionNum}"]`);
            if (questionNavBtn) {
                const questionType = questionNavBtn.dataset.questionType;

                // Find and activate the corresponding tab
                let correspondingTab = null;
                document.querySelectorAll('.tab-button').forEach(tab => {
                    if (tab.dataset.type === questionType) {
                        correspondingTab = tab;
                    }
                });

                if (correspondingTab) {
                    // Update active tab
                    document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
                    correspondingTab.classList.add('active');

                    // Apply filter
                    document.querySelectorAll('.question-nav').forEach(navBtn => {
                        const navQuestionType = navBtn.dataset.questionType;
                        if (questionType === navQuestionType) {
                            navBtn.style.display = 'inline-flex';
                        } else {
                            navBtn.style.display = 'none';
                        }
                    });

                    updateQuestionNav();
                }
            }
        }

        function confirmSubmit() {
            document.getElementById('submit-modal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('submit-modal').classList.add('hidden');
        }

        let startTime;

        // Cek apakah sudah ada startTime di localStorage
        if (localStorage.getItem('quiz_start_time_{{ $quiz->id }}')) {
            startTime = parseInt(localStorage.getItem('quiz_start_time_{{ $quiz->id }}'), 10);
        } else {
            startTime = Date.now();
            localStorage.setItem('quiz_start_time_{{ $quiz->id }}', startTime);
        }

        // Hitung durasi
        function getDurationInSeconds() {
            const now = Date.now();
            return Math.floor((now - startTime) / 1000);
        }


        function submitQuiz() {
            // Hitung durasi aktual yang digunakan
            const actualDuration = quizDuration - timeLeft;
            document.getElementById('duration').value = actualDuration;

            // Hapus data dari localStorage
            localStorage.removeItem(storageKey);
            localStorage.removeItem('quiz_start_time_{{ $quiz->id }}');
            localStorage.removeItem('quiz_time_left_{{ $quiz->id }}');

            // Hentikan timer
            if (timerInterval) {
                clearInterval(timerInterval);
            }

            // Submit form
            document.getElementById('quiz-form').submit();
        }

        document.addEventListener('DOMContentLoaded', function() {
            startTimer();
            loadAnswers();
            updateQuestionNav();

            // Simpan jawaban saat ada perubahan
            document.querySelectorAll('input[type="radio"], textarea').forEach(input => {
                input.addEventListener('change', () => {
                    saveAnswers();
                    updateQuestionNav();
                });
                if (input.tagName.toLowerCase() === 'textarea') {
                    input.addEventListener('input', () => {
                        saveAnswers();
                        updateQuestionNav();
                    });
                }
            });

            // Intercept semua link keluar halaman (kecuali reload)
            document.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', function(e) {
                    const href = this.getAttribute('href');
                    if (href && href !== "javascript:history.back()") {
                        e.preventDefault();
                        confirmExit(href);
                    }
                });
            });

            // Tangkap tombol arrow back custom
            const backBtn = document.querySelector('a[href="javascript:history.back()"]');
            if (backBtn) {
                backBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    confirmExit(document.referrer || '/');
                });
            }

            // Tangkap tombol back browser
            window.addEventListener('popstate', function(e) {
                e.preventDefault();
                confirmExit(document.referrer || '/');
            });
        });

        function confirmExit(targetUrl) {
            Swal.fire({
                title: "Yakin mau keluar?",
                text: "Progres tidak akan tersimpan jika keluar sebelum submit.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Ya, keluar",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    localStorage.removeItem(storageKey);
                    localStorage.removeItem('quiz_start_time_{{ $quiz->id }}');
                    localStorage.removeItem('quiz_time_left_{{ $quiz->id }}');
                    if (timerInterval) {
                        clearInterval(timerInterval);
                    }
                    window.location.href = targetUrl;
                }
            });
        }

        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowRight') {
                if (currentQuestion < totalQuestions) nextQuestion();
            } else if (e.key === 'ArrowLeft') {
                if (currentQuestion > 1) previousQuestion();
            }
        });
    </script>
@endpush
