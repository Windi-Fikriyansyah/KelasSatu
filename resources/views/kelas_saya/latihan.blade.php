@extends('layouts.app')
@section('title', 'Latihan - ' . $quiz->title)
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
                </div>
            </div>

            <!-- Layout 2 Kolom -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

                <!-- Navigasi Soal (Kiri) -->
                <div class="bg-white rounded-xl shadow-lg p-6 h-fit lg:sticky lg:top-24">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Navigasi Soal</h3>
                    <div class="grid grid-cols-8 lg:grid-cols-3 gap-2">
                        @foreach ($questions as $index => $question)
                            <button type="button"
                                class="question-nav w-10 h-10 rounded-lg border border-gray-300 text-gray-600 hover:border-primary-100 hover:bg-primary-50 transition-all duration-200 {{ $index === 0 ? 'bg-primary-100 text-white border-primary-100' : '' }}"
                                data-question="{{ $index + 1 }}" onclick="goToQuestion({{ $index + 1 }})">
                                {{ $index + 1 }}
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
                    <form id="quiz-form" method="POST" action="{{ route('kelas.latihan.submit', $quiz->id) }}">
                        @csrf

                        @foreach ($questions as $index => $question)
                            <div class="question-card bg-white rounded-xl shadow-lg overflow-hidden mb-6 {{ $index > 0 ? 'hidden' : '' }}"
                                data-question="{{ $index + 1 }}">
                                <div class="p-6">
                                    <div class="flex items-start mb-6">
                                        <div
                                            class="bg-primary-100 text-white rounded-full w-8 h-8 flex items-center justify-center font-semibold mr-4 flex-shrink-0">
                                            {{ $index + 1 }}
                                        </div>
                                        <div class="flex-1">
                                            <div class="prose max-w-none text-gray-900 mb-4">
                                                {!! $question->question !!}
                                            </div>

                                            <div class="space-y-3">
                                                @foreach ($question->formatted_options as $optionKey => $option)
                                                    @if ($option)
                                                        <label
                                                            class="option-label flex items-center p-4 border border-gray-200 rounded-lg hover:border-primary-100 hover:bg-primary-50/30 cursor-pointer transition-all duration-200"
                                                            data-question="{{ $question->question_id }}"
                                                            data-value="{{ $optionKey }}">

                                                            {{-- Radio disembunyikan --}}
                                                            <input type="radio"
                                                                name="answers[{{ $question->question_id }}]"
                                                                value="{{ $optionKey }}" class="hidden">

                                                            {{-- Huruf pilihan --}}
                                                            {{-- Huruf pilihan dalam lingkaran --}}
                                                            <span
                                                                class="circle-label flex items-center justify-center w-8 h-8 rounded-full font-semibold mr-3">
                                                                {{ $optionKey }}
                                                            </span>


                                                            {{-- Isi jawaban --}}
                                                            <span class="text-gray-700 leading-relaxed flex-1">
                                                                {!! $option !!}
                                                            </span>
                                                        </label>
                                                    @endif
                                                @endforeach
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
                    <p class="text-gray-600 text-center mb-6">Apakah Anda yakin ingin menyelesaikan latihan ini?</p>
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
    <style>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let currentQuestion = 1;
        const totalQuestions = {{ count($questions) }};
        const storageKey = "quiz_answers_{{ $quiz->id }}";

        // Simpan jawaban ke localStorage
        function saveAnswers() {
            let answers = {};
            document.querySelectorAll('input[type="radio"]:checked, textarea').forEach(input => {
                if (input.type === "radio") {
                    const qid = input.name.match(/\d+/)[0];
                    answers[qid] = input.value;
                } else if (input.tagName.toLowerCase() === "textarea") {
                    const qid = input.name.match(/\d+/)[0];
                    answers[qid] = input.value.trim();
                }
            });
            localStorage.setItem(storageKey, JSON.stringify(answers));
        }

        // Load jawaban dari localStorage
        function loadAnswers() {
            const saved = localStorage.getItem(storageKey);
            if (saved) {
                const answers = JSON.parse(saved);
                Object.keys(answers).forEach(qid => {
                    const val = answers[qid];
                    const radio = document.querySelector(`input[name="answers[${qid}]"][value="${val}"]`);
                    const textarea = document.querySelector(`textarea[name="answers[${qid}]"]`);

                    if (radio) {
                        radio.checked = true;

                        // Tambahkan class selected ke label jawaban
                        const label = radio.closest('.option-label');
                        if (label) label.classList.add('selected');
                    }

                    if (textarea) {
                        textarea.value = val;
                    }
                });
            }
        }

        function updateQuestionNav() {
            document.querySelectorAll('.question-nav').forEach((btn, index) => {
                const questionNum = index + 1;
                const answered = isQuestionAnswered(questionNum);
                btn.classList.remove('bg-primary-100', 'text-white', 'border-primary-100', 'bg-green-500');

                if (questionNum === currentQuestion) {
                    btn.classList.add('bg-primary-100', 'text-white', 'border-primary-100');
                } else if (answered) {
                    btn.classList.add('bg-green-500', 'text-white');
                }
            });
        }

        function isQuestionAnswered(questionNum) {
            const questionCard = document.querySelector(`.question-card[data-question="${questionNum}"]`);
            if (questionCard.querySelector('input[type="radio"]')) {
                return questionCard.querySelector('input[type="radio"]:checked') !== null;
            } else if (questionCard.querySelector('textarea')) {
                return questionCard.querySelector('textarea').value.trim() !== '';
            }
            return false;
        }

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
            const durationInput = document.createElement('input');
            durationInput.type = 'hidden';
            durationInput.name = 'duration';
            durationInput.value = getDurationInSeconds();
            document.getElementById('quiz-form').appendChild(durationInput);

            localStorage.removeItem(storageKey);
            localStorage.removeItem('quiz_start_time_{{ $quiz->id }}'); // hapus jawaban setelah submit
            document.getElementById('quiz-form').submit();
        }

        document.addEventListener('DOMContentLoaded', function() {
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

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.option-label').forEach(label => {
                label.addEventListener('click', function() {
                    const questionId = this.dataset.question;

                    // reset pilihan lain dalam soal yang sama
                    document.querySelectorAll(`.option-label[data-question="${questionId}"]`)
                        .forEach(el => el.classList.remove('selected'));

                    // tandai label yang dipilih
                    this.classList.add('selected');
                    this.querySelector('input[type="radio"]').checked = true;

                    saveAnswers();
                    updateQuestionNav();
                });
            });
        });
    </script>
@endpush
