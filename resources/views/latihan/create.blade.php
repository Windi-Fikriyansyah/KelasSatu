@extends('template.app')
@section('title', isset($soal) ? 'Edit Soal' : 'Tambah Soal')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="row">
            <div class="col-xxl">
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">{{ isset($soal) ? 'Edit' : 'Tambah' }} Soal</h5>
                        <small class="text-muted float-end">Form Soal untuk "{{ $course->title }}"</small>
                    </div>
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            <i class="bx bx-check-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    <div class="card-body">
                        <form id="mainForm"
                            action="{{ isset($soal) ? route('latihan.update', $soal->id) : route('latihan.store') }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            @if (isset($soal))
                                @method('PUT')
                            @endif

                            <input type="hidden" name="course_id" value="{{ $course->id }}">
                            <input type="hidden" name="quiz_type" value="latihan">

                            <!-- Title -->
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="title">Judul Quiz</label>
                                <div class="col-sm-10">
                                    <input type="text" id="title" name="title"
                                        class="form-control @error('title') is-invalid @enderror"
                                        value="{{ old('title', $soal->title ?? '') }}" required />
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="card radius-10">
                                <div class="card-header">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <h5 class="card-title mb-0">Daftar Soal-Soal</h5>
                                        <div class="d-flex justify-content-end mb-3">
                                            <button type="button" class="btn btn-success me-2" id="saveDraftBtn">
                                                <i class="bi bi-save"></i> Simpan Draft
                                            </button>
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#soalModal">
                                                <i class="bi bi-plus"></i> Tambah Soal
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div id="questionsContainer">
                                        <!-- Questions will be added here dynamically -->
                                    </div>

                                    <div class="text-center mt-3" id="noQuestionsMsg">
                                        <p class="text-muted">Belum ada soal. Klik "Tambah Soal" untuk menambahkan soal
                                            baru.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="button" class="btn btn-secondary me-2"
                                    onclick="window.history.back()">Batal</button>
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="bi bi-check"></i> Simpan Quiz
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk Tambah/Edit Soal -->
    <div class="modal fade" id="soalModal" tabindex="-1" aria-labelledby="soalModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="soalModalLabel">Tambah Soal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form id="questionForm">
                        <input type="hidden" id="editIndex" value="">

                        <!-- Pertanyaan -->
                        <div class="mb-3">
                            <label for="question" class="form-label">Pertanyaan *</label>
                            <textarea name="question" id="question" class="form-control" rows="4" required></textarea>
                            <div class="invalid-feedback">Pertanyaan harus diisi</div>
                        </div>

                        <!-- Options -->
                        <div class="mb-3">
                            <label class="form-label">Pilihan Jawaban *</label>
                            <div class="input-group mb-2">
                                <span class="input-group-text">A</span>
                                <input type="text" name="option_a" id="option_a" class="form-control"
                                    placeholder="Pilihan A" required>
                                <div class="invalid-feedback">Pilihan A harus diisi</div>
                            </div>
                            <div class="input-group mb-2">
                                <span class="input-group-text">B</span>
                                <input type="text" name="option_b" id="option_b" class="form-control"
                                    placeholder="Pilihan B" required>
                                <div class="invalid-feedback">Pilihan B harus diisi</div>
                            </div>
                            <div class="input-group mb-2">
                                <span class="input-group-text">C</span>
                                <input type="text" name="option_c" id="option_c" class="form-control"
                                    placeholder="Pilihan C">
                            </div>
                            <div class="input-group mb-2">
                                <span class="input-group-text">D</span>
                                <input type="text" name="option_d" id="option_d" class="form-control"
                                    placeholder="Pilihan D">
                            </div>
                            <div class="input-group mb-2">
                                <span class="input-group-text">E</span>
                                <input type="text" name="option_e" id="option_e" class="form-control"
                                    placeholder="Pilihan E">
                            </div>
                        </div>

                        <!-- Correct Answer -->
                        <div class="mb-3">
                            <label for="correct_answer" class="form-label">Jawaban Benar *</label>
                            <select name="correct_answer" id="correct_answer" class="form-select" required>
                                <option value="">-- Pilih Jawaban Benar --</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                                <option value="D">D</option>
                                <option value="E">E</option>
                            </select>
                            <div class="invalid-feedback">Jawaban benar harus dipilih</div>
                        </div>
                        <!-- Di dalam modal soal (setelah correct_answer) -->
                        <div class="mb-3">
                            <label for="explanation" class="form-label">Pembahasan</label>
                            <textarea name="explanation" id="explanation" class="form-control" rows="4"
                                placeholder="Pembahasan jawaban (opsional)"></textarea>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="saveQuestionBtn">
                        <i class="bi bi-check"></i> <span id="saveQuestionBtnText">Simpan Soal</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap5-theme@1.3.2/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
    <style>
        .explanation-content {
            background-color: #f8f9fa;
            border-left: 4px solid #0dcaf0;
            padding: 12px;
            border-radius: 0 4px 4px 0;
            margin-top: 10px;
        }

        .question-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .question-card:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .question-header {
            background-color: #f8f9fa;
            padding: 15px;
            border-bottom: 1px solid #dee2e6;
            border-radius: 8px 8px 0 0;
        }

        .question-body {
            padding: 15px;
        }

        .option-item {
            padding: 8px 12px;
            margin: 5px 0;
            border: 1px solid #e9ecef;
            border-radius: 4px;
            background-color: #fff;
        }

        .option-item.correct-answer {
            background-color: #d4edda;
            border-color: #c3e6cb;
            font-weight: bold;
            color: #155724;
        }

        .autosave-status {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
        }

        .is-invalid {
            border-color: #dc3545 !important;
        }

        .invalid-feedback {
            display: block;
        }
    </style>
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        let questions = [];
        let editingIndex = -1;
        let autosaveTimer;

        $(document).ready(function() {
            // Load existing questions jika dalam mode edit
            @if (isset($soal) && $soal->questions)
                questions = {!! json_encode(
                    $soal->questions->map(function ($q) {
                        return [
                            'question' => $q->question,
                            'option_a' => $q->option_a,
                            'option_b' => $q->option_b,
                            'option_c' => $q->option_c,
                            'option_d' => $q->option_d,
                            'option_e' => $q->option_e,
                            'correct_answer' => $q->correct_answer,
                            'id' => $q->id,
                        ];
                    }),
                ) !!};
                renderQuestions();
            @else
                // Load draft dari database hanya jika bukan mode edit
                loadDraftFromDatabase();
            @endif

            // Autosave setiap 30 detik (hanya jika bukan mode edit)
            @if (!isset($soal))
                autosaveTimer = setInterval(saveDraftToDatabase, 30000);
                $('#title').on('input change', debounce(saveDraftToDatabase, 3000));
            @endif

            // Initialize Select2
            $('.select2').select2({
                theme: 'bootstrap-5',
                placeholder: '-- Pilih --',
                allowClear: true,
                width: '100%'
            });
        });

        // Debounce function untuk mengurangi frekuensi autosave
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        // Fungsi untuk load draft dari database (hanya untuk mode tambah)
        function loadDraftFromDatabase() {
            $.ajax({
                url: '{{ route('draft.load') }}',
                method: 'POST',
                data: {
                    course_id: {{ $course->id }},
                    quiz_type: 'latihan'
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success && response.data) {
                        // Load form data
                        if (response.data.title) {
                            $('#title').val(response.data.title);
                        }

                        // Load questions
                        if (response.data.questions && response.data.questions.length > 0) {
                            questions = response.data.questions;
                            renderQuestions();
                            showNotification(
                                `Draft terakhir dimuat (disimpan: ${response.data.saved_at})`,
                                'success'
                            );
                        }
                    }
                },
                error: function(xhr) {
                    console.log('No draft found or error loading draft');
                }
            });
        }

        // Fungsi untuk save draft ke database (hanya untuk mode tambah)
        function saveDraftToDatabase() {
            @if (!isset($soal))
                if (!$('#title').val().trim() && questions.length === 0) {
                    return; // Tidak ada data untuk disimpan
                }

                $.ajax({
                    url: '{{ route('draft.save') }}',
                    method: 'POST',
                    data: {
                        course_id: {{ $course->id }},
                        quiz_type: 'latihan',
                        title: $('#title').val(),
                        questions: questions,
                        form_data: {
                            // Data form tambahan jika diperlukan
                        }
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            showNotification('Draft tersimpan otomatis', 'info', 2000);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error saving draft:', xhr.responseJSON);
                        showNotification('Gagal menyimpan draft', 'error', 3000);
                    }
                });
            @endif
        }

        // Fungsi manual save draft
        $('#saveDraftBtn').click(function() {
            if (!$('#title').val().trim() && questions.length === 0) {
                showNotification('Tidak ada data untuk disimpan', 'warning');
                return;
            }

            // Show loading
            const originalText = $(this).html();
            $(this).html('<i class="spinner-border spinner-border-sm me-2"></i>Menyimpan...');
            $(this).prop('disabled', true);

            $.ajax({
                url: '{{ route('draft.save') }}',
                method: 'POST',
                data: {
                    course_id: {{ $course->id }},
                    quiz_type: 'latihan',
                    title: $('#title').val(),
                    questions: questions,
                    form_data: {}
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        showNotification('Draft berhasil disimpan', 'success');
                    }
                },
                error: function(xhr) {
                    console.error('Error saving draft:', xhr.responseJSON);
                    showNotification('Gagal menyimpan draft', 'error');
                },
                complete: function() {
                    // Restore button
                    $('#saveDraftBtn').html(originalText);
                    $('#saveDraftBtn').prop('disabled', false);
                }
            });
        });

        // Fungsi untuk menampilkan notifikasi
        function showNotification(message, type = 'info', duration = 3000) {
            // Remove existing notifications
            $('.autosave-status').remove();

            const alertClass = type === 'success' ? 'alert-success' :
                type === 'error' ? 'alert-danger' :
                type === 'warning' ? 'alert-warning' : 'alert-info';

            const notification = `
        <div class="alert ${alertClass} alert-dismissible autosave-status" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;

            $('body').append(notification);

            // Auto hide
            setTimeout(function() {
                $('.autosave-status').fadeOut(function() {
                    $(this).remove();
                });
            }, duration);
        }

        // Handle modal show
        $('#soalModal').on('show.bs.modal', function() {
            if (editingIndex === -1) {
                resetForm();
            }
        });

        // Reset form
        function resetForm() {
            $('#questionForm')[0].reset();
            $('#editIndex').val('');
            $('#soalModalLabel').text('Tambah Soal');
            $('#saveQuestionBtnText').text('Simpan Soal');
            editingIndex = -1;

            // Remove validation classes
            $('.is-invalid').removeClass('is-invalid');
        }

        // Save question
        $('#saveQuestionBtn').click(function() {
            if (validateForm()) {
                const questionData = {
                    question: $('#question').val().trim(),
                    option_a: $('#option_a').val().trim(),
                    option_b: $('#option_b').val().trim(),
                    option_c: $('#option_c').val().trim(),
                    option_d: $('#option_d').val().trim(),
                    option_e: $('#option_e').val().trim(),
                    correct_answer: $('#correct_answer').val(),
                    explanation: $('#explanation').val().trim()
                };

                // Jika dalam mode edit, pertahankan ID soal
                if (editingIndex >= 0 && questions[editingIndex].id) {
                    questionData.id = questions[editingIndex].id;
                }

                if (editingIndex >= 0) {
                    // Edit existing question
                    questions[editingIndex] = questionData;
                    showNotification('Soal berhasil diupdate', 'success');
                } else {
                    // Add new question
                    questions.push(questionData);
                    showNotification('Soal berhasil ditambahkan', 'success');
                }

                renderQuestions();
                saveDraftToDatabase(); // Auto save setelah perubahan
                $('#soalModal').modal('hide');
                resetForm();
            }
        });

        // Validate form
        function validateForm() {
            const required = [{
                    id: 'question',
                    message: 'Pertanyaan harus diisi'
                },
                {
                    id: 'option_a',
                    message: 'Pilihan A harus diisi'
                },
                {
                    id: 'option_b',
                    message: 'Pilihan B harus diisi'
                },
                {
                    id: 'correct_answer',
                    message: 'Jawaban benar harus dipilih'
                }
            ];

            let isValid = true;

            // Reset previous validation
            $('.is-invalid').removeClass('is-invalid');

            required.forEach(field => {
                const element = $('#' + field.id);
                const value = element.val().trim();

                if (!value) {
                    element.addClass('is-invalid');
                    element.siblings('.invalid-feedback').text(field.message);
                    isValid = false;
                }
            });

            // Validate correct answer matches available options
            const correctAnswer = $('#correct_answer').val();
            if (correctAnswer) {
                const optionValue = $('#option_' + correctAnswer.toLowerCase()).val().trim();
                if (!optionValue) {
                    $('#correct_answer').addClass('is-invalid');
                    $('#correct_answer').siblings('.invalid-feedback').text(
                        'Jawaban benar harus sesuai dengan pilihan yang tersedia');
                    isValid = false;
                }
            }

            return isValid;
        }

        // Render questions
        function renderQuestions() {
            const container = $('#questionsContainer');
            container.empty();

            if (questions.length === 0) {
                $('#noQuestionsMsg').show();
                return;
            }

            $('#noQuestionsMsg').hide();

            questions.forEach((q, index) => {
                const options = [];
                if (q.option_a) options.push({
                    key: 'A',
                    value: q.option_a,
                    isCorrect: q.correct_answer === 'A'
                });
                if (q.option_b) options.push({
                    key: 'B',
                    value: q.option_b,
                    isCorrect: q.correct_answer === 'B'
                });
                if (q.option_c) options.push({
                    key: 'C',
                    value: q.option_c,
                    isCorrect: q.correct_answer === 'C'
                });
                if (q.option_d) options.push({
                    key: 'D',
                    value: q.option_d,
                    isCorrect: q.correct_answer === 'D'
                });
                if (q.option_e) options.push({
                    key: 'E',
                    value: q.option_e,
                    isCorrect: q.correct_answer === 'E'
                });

                const optionsHtml = options.map(opt =>
                    `<div class="option-item ${opt.isCorrect ? 'correct-answer' : ''}">${opt.key}. ${opt.value}</div>`
                ).join('');

                const questionHtml = `
            <div class="question-card" data-index="${index}">
                <div class="question-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Soal ${index + 1}</h6>
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-primary me-1" onclick="editQuestion(${index})">
                                <i class="bi bi-pencil"></i> Edit
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteQuestion(${index})">
                                <i class="bi bi-trash"></i> Hapus
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-info me-1" onclick="toggleExplanation(${index})">
    <i class="bi bi-chat-text"></i> Pembahasan
</button>
                        </div>
                    </div>
                </div>
                <div class="question-body">
                    <div class="mb-3">
                        <strong>Pertanyaan:</strong>
                        <div class="mt-2">${q.question}</div>
                    </div>
                    <div class="row">
                        <div class="col-md-8">
                            <strong>Pilihan Jawaban:</strong>
                            <div class="mt-2">${optionsHtml}</div>
                        </div>
                        <div class="col-md-4">
                            <strong>Jawaban Benar:</strong>
                            <div class="mt-2">
                                <span class="badge bg-success">${q.correct_answer}</span>
                            </div>
                        </div>
                        <div class="mt-3" id="explanation-${index}" style="${q.explanation ? '' : 'display: none;'}">
                            <strong>Pembahasan:</strong>
                            <div class="mt-2">${q.explanation || ''}</div>
                        </div>
                    </div>

                    <!-- Hidden inputs untuk submit -->
                    ${q.id ? `<input type="hidden" name="questions[${index}][id]" value="${q.id}">` : ''}
                    <input type="hidden" name="questions[${index}][question]" value="${escapeHtml(q.question)}">
                    <input type="hidden" name="questions[${index}][option_a]" value="${escapeHtml(q.option_a)}">
                    <input type="hidden" name="questions[${index}][option_b]" value="${escapeHtml(q.option_b)}">
                    <input type="hidden" name="questions[${index}][option_c]" value="${escapeHtml(q.option_c)}">
                    <input type="hidden" name="questions[${index}][option_d]" value="${escapeHtml(q.option_d)}">
                    <input type="hidden" name="questions[${index}][option_e]" value="${escapeHtml(q.option_e)}">
                    <input type="hidden" name="questions[${index}][correct_answer]" value="${q.correct_answer}">
                    <input type="hidden" name="questions[${index}][explanation]" value="${escapeHtml(q.explanation || '')}">
                </div>
            </div>
        `;
                container.append(questionHtml);
            });
        }

        // Fungsi untuk menampilkan/sembunyikan pembahasan
        function toggleExplanation(index) {
            const explanationEl = $(`#explanation-${index}`);
            explanationEl.toggle();
        }
        // Escape HTML untuk mencegah XSS
        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Edit question
        function editQuestion(index) {
            editingIndex = index;
            const q = questions[index];

            $('#question').val(q.question);
            $('#option_a').val(q.option_a);
            $('#option_b').val(q.option_b);
            $('#option_c').val(q.option_c);
            $('#option_d').val(q.option_d);
            $('#option_e').val(q.option_e);
            $('#correct_answer').val(q.correct_answer);
            $('#explanation').val(q.explanation || '');

            $('#soalModalLabel').text('Edit Soal');
            $('#saveQuestionBtnText').text('Update Soal');

            // Remove validation classes
            $('.is-invalid').removeClass('is-invalid');

            $('#soalModal').modal('show');
        }

        // Delete question
        function deleteQuestion(index) {
            Swal.fire({
                title: 'Hapus Soal?',
                text: "Soal yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    questions.splice(index, 1);
                    renderQuestions();
                    saveDraftToDatabase(); // Auto save setelah perubahan
                    showNotification('Soal berhasil dihapus', 'success');
                }
            });
        }

        // Handle form submit
        $('#mainForm').submit(function(e) {
            if (questions.length === 0) {
                e.preventDefault();
                Swal.fire('Error!', 'Minimal harus ada 1 soal!', 'error');
                return false;
            }

            // Hapus draft setelah submit berhasil (hanya untuk mode tambah)
            @if (!isset($soal))
                // Clear autosave timer
                if (autosaveTimer) {
                    clearInterval(autosaveTimer);
                }

                // Hapus draft dari database setelah form berhasil submit
                $(this).on('submit', function() {
                    setTimeout(() => {
                        $.ajax({
                            url: '{{ route('draft.delete') }}',
                            method: 'DELETE',
                            data: {
                                course_id: {{ $course->id }},
                                quiz_type: 'latihan'
                            },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                console.log('Draft deleted after submit');
                            }
                        });
                    }, 1000);
                });
            @endif

            return true;
        });

        // Clear draft confirmation on page unload (hanya untuk mode tambah)
        @if (!isset($soal))
            let hasUnsavedChanges = false;

            // Track changes
            $('#title').on('input', function() {
                hasUnsavedChanges = true;
            });

            // Fungsi untuk menonaktifkan beforeunload
            function disableBeforeUnload() {
                hasUnsavedChanges = false;
                $(window).off('beforeunload');
            }

            $(window).on('beforeunload', function(e) {
                if (hasUnsavedChanges || questions.length > 0) {
                    return 'Anda memiliki perubahan yang belum disimpan. Yakin ingin meninggalkan halaman?';
                }
            });

            // Reset flag setelah save
            $(document).on('ajaxSuccess', function(event, xhr, settings) {
                if (settings.url.includes('draft.save')) {
                    hasUnsavedChanges = false;
                }
            });

            // Nonaktifkan beforeunload saat form disubmit
            $('#mainForm').submit(function(e) {
                disableBeforeUnload();

                if (questions.length === 0) {
                    e.preventDefault();
                    Swal.fire('Error!', 'Minimal harus ada 1 soal!', 'error');
                    return false;
                }

                // Hapus draft setelah submit berhasil (hanya untuk mode tambah)
                // Clear autosave timer
                if (autosaveTimer) {
                    clearInterval(autosaveTimer);
                }

                // Hapus draft dari database setelah form berhasil submit
                $(this).on('submit', function() {
                    setTimeout(() => {
                        $.ajax({
                            url: '{{ route('draft.delete') }}',
                            method: 'DELETE',
                            data: {
                                course_id: {{ $course->id }},
                                quiz_type: 'latihan'
                            },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                console.log('Draft deleted after submit');
                            }
                        });
                    }, 1000);
                });

                return true;
            });
        @endif



        // Include SweetAlert2 if not already included
        if (typeof Swal === 'undefined') {
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
            document.head.appendChild(script);
        }
    </script>
@endpush
