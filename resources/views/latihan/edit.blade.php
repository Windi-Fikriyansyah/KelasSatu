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
                            <input type="hidden" name="quiz_id" value="{{ $soal->id ?? '' }}">
                            <input type="hidden" name="quiz_type" value="latihan">

                            <!-- Title -->
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="title">Judul Quiz</label>
                                <div class="col-sm-10">
                                    <input type="text" id="title" name="title"
                                        class="form-control @error('title') is-invalid @enderror"
                                        value="{{ old('title', isset($soal) ? $soal->title : '') }}" required />
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
                                            @if (!isset($soal))
                                                <button type="button" class="btn btn-success me-2" id="saveDraftBtn">
                                                    <i class="bi bi-save"></i> Simpan Draft
                                                </button>
                                            @endif
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

                        <!-- Pertanyaan (pakai CKEditor) -->
                        <div class="mb-3">
                            <label for="question" class="form-label">Pertanyaan *</label>
                            <textarea name="question" id="question" class="form-control ckeditor" rows="4" required></textarea>
                            <div class="invalid-feedback">Pertanyaan harus diisi</div>
                        </div>

                        <!-- Options -->
                        <div class="mb-3">
                            <label class="form-label">Pilihan Jawaban *</label>
                            <div class="input-group mb-2">
                                <span class="input-group-text">A</span>
                                <textarea name="option_a" id="option_a" class="form-control ckeditor" placeholder="Pilihan A" required></textarea>
                                <div class="invalid-feedback">Pilihan A harus diisi</div>
                            </div>
                            <div class="input-group mb-2">
                                <span class="input-group-text">B</span>
                                <textarea name="option_b" id="option_b" class="form-control ckeditor" placeholder="Pilihan B" required></textarea>
                                <div class="invalid-feedback">Pilihan B harus diisi</div>
                            </div>
                            <div class="input-group mb-2">
                                <span class="input-group-text">C</span>
                                <textarea name="option_c" id="option_c" class="form-control ckeditor" placeholder="Pilihan C"></textarea>
                            </div>
                            <div class="input-group mb-2">
                                <span class="input-group-text">D</span>
                                <textarea name="option_d" id="option_d" class="form-control ckeditor" placeholder="Pilihan D"></textarea>
                            </div>
                            <div class="input-group mb-2">
                                <span class="input-group-text">E</span>
                                <textarea name="option_e" id="option_e" class="form-control ckeditor" placeholder="Pilihan E"></textarea>
                            </div>
                        </div>

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
                            <div class="invalid-feedback" id="correct_answer_feedback">
                                Jawaban benar harus dipilih dan pilihan tersebut harus sudah diisi.
                            </div>
                        </div>
                        <!-- Pembahasan (pakai CKEditor) -->
                        <div class="mb-3">
                            <label for="explanation" class="form-label">Pembahasan</label>
                            <textarea name="explanation" id="explanation" class="form-control ckeditor" rows="4"
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

        .ck-content table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }

        .ck-content table th,
        .ck-content table td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        .ck-content table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .ck-content img {
            max-width: 200px !important;
            height: auto !important;
            display: inline-block !important;
            float: left !important;
            margin: 5px 10px 5px 0 !important;
        }
    </style>
@endpush

@push('js')
    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/super-build/ckeditor.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        let editorInstances = {};
        let editingIndex = -1;
        let questions = [];
        let autosaveTimer;

        function initCKEditor(id) {
            CKEDITOR.ClassicEditor.create(document.querySelector('#' + id), {
                ckfinder: {
                    uploadUrl: "{{ route('latihan.ckeditor.upload') }}?_token={{ csrf_token() }}"
                },
                removePlugins: [
                    // Collaboration
                    'RealTimeCollaborativeComments',
                    'RealTimeCollaborativeTrackChanges',
                    'RealTimeCollaborativeRevisionHistory',
                    'PresenceList',
                    'Comments',
                    'TrackChanges',
                    'TrackChangesData',
                    'RevisionHistory',

                    // Premium (butuh license, jadi matikan)
                    'Pagination',
                    'WProofreader',
                    'MathType',
                    'DocumentOutline',
                    'TableOfContents',
                    'AiAssistant',
                    'FormatPainter',
                    'Template',
                    'SlashCommand',
                    'PasteFromOfficeEnhanced',
                    'CaseChange',
                    'AIAssistant', // <- buang AI
                    'MultiLevelList'
                ],
                toolbar: {
                    items: [
                        'heading', '|',
                        'bold', 'italic', 'link', '|',
                        'fontFamily', 'fontSize', '|',
                        'alignment', '|',
                        'bulletedList', 'numberedList', '|',
                        'insertTable', 'imageUpload', '|',
                        'undo', 'redo'
                    ]
                },
                fontFamily: {
                    options: [
                        'default',
                        'Inter_Fallback_f4ae04, sans-serif',
                        'Arial, Helvetica, sans-serif',
                        'Courier New, Courier, monospace',
                        'Georgia, serif',
                        'Lucida Sans Unicode, Lucida Grande, sans-serif',
                        'Tahoma, Geneva, sans-serif',
                        'Times New Roman, Times, serif',
                        'Trebuchet MS, Helvetica, sans-serif',
                        'Verdana, Geneva, sans-serif'
                    ],
                    supportAllValues: true
                },
                fontSize: {
                    options: [9, 11, 13, 'default', 17, 19, 21],
                    supportAllValues: true
                },
                alignment: {
                    options: ['left', 'center', 'right',
                        'justify'
                    ] // opsional, bisa pilih alignment yang ditampilkan
                },
                table: {
                    contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells']
                }
            }).then(editor => {
                editorInstances[id] = editor;

                editor.model.document.on('change:data', () => {
                    $('#' + id).removeClass('is-invalid border-warning');

                    if (id.startsWith('option_')) {
                        const fieldLetter = id.split('_')[1].toUpperCase();
                        const correctAnswer = $('#correct_answer').val();

                        if (fieldLetter === correctAnswer && getEditorData(id).trim()) {
                            $('#correct_answer').removeClass('is-invalid');
                        }
                    }
                });
            }).catch(error => console.error(error));
        }

        function getEditorData(editorId) {
            if (editorInstances[editorId]) {
                return editorInstances[editorId].getData();
            }
            return '';
        }

        function setEditorData(editorId, data) {
            if (editorInstances[editorId]) {
                editorInstances[editorId].setData(data || '');
            }
        }

        function validateForm() {
            let isValid = true;
            $('.is-invalid').removeClass('is-invalid');

            const questionData = getEditorData('question');
            if (!questionData.trim()) {
                $('#question').addClass('is-invalid');
                isValid = false;
            }

            const optionAData = getEditorData('option_a');
            if (!optionAData.trim()) {
                $('#option_a').addClass('is-invalid');
                isValid = false;
            }

            const optionBData = getEditorData('option_b');
            if (!optionBData.trim()) {
                $('#option_b').addClass('is-invalid');
                isValid = false;
            }

            const correctAnswer = $('#correct_answer').val();
            if (!correctAnswer) {
                $('#correct_answer').addClass('is-invalid');
                isValid = false;
            } else {
                const optionData = getEditorData('option_' + correctAnswer.toLowerCase());
                if (!optionData.trim()) {
                    $('#correct_answer').addClass('is-invalid');
                    $('#correct_answer').siblings('.invalid-feedback').text(
                        `Anda memilih ${correctAnswer} sebagai jawaban benar, tetapi pilihan ${correctAnswer} masih kosong.`
                    );
                    isValid = false;

                    $('#option_' + correctAnswer.toLowerCase()).addClass('is-invalid');
                }
            }

            return isValid;
        }

        $('#saveQuestionBtn').click(function() {
            if (validateForm()) {
                const questionData = {
                    question: getEditorData('question'),
                    option_a: getEditorData('option_a'),
                    option_b: getEditorData('option_b'),
                    option_c: getEditorData('option_c'),
                    option_d: getEditorData('option_d'),
                    option_e: getEditorData('option_e'),
                    correct_answer: $('#correct_answer').val(),
                    explanation: getEditorData('explanation')
                };

                if (editingIndex >= 0) {
                    if (questions[editingIndex].id) {
                        questionData.id = questions[editingIndex].id;
                    }
                    questions[editingIndex] = questionData;
                    showNotification('Soal berhasil diupdate', 'success');
                } else {
                    questions.push(questionData);
                    showNotification('Soal berhasil ditambahkan', 'success');
                }

                renderQuestions();

                @if (!isset($soal))
                    saveDraftToDatabase();
                @endif

                $('#soalModal').modal('hide');
                resetForm();
            }
        });

        function resetForm() {
            $('#questionForm')[0].reset();
            $('#editIndex').val('');
            $('#soalModalLabel').text('Tambah Soal');
            $('#saveQuestionBtnText').text('Simpan Soal');
            editingIndex = -1;

            Object.keys(editorInstances).forEach(editorId => {
                setEditorData(editorId, '');
            });

            $('.is-invalid').removeClass('is-invalid');
        }

        function editQuestion(index) {
            editingIndex = index;
            const q = questions[index];

            setEditorData('question', q.question);
            setEditorData('option_a', q.option_a);
            setEditorData('option_b', q.option_b);
            setEditorData('option_c', q.option_c);
            setEditorData('option_d', q.option_d);
            setEditorData('option_e', q.option_e);
            setEditorData('explanation', q.explanation || '');

            $('#correct_answer').val(q.correct_answer);

            $('#soalModalLabel').text('Edit Soal');
            $('#saveQuestionBtnText').text('Update Soal');

            $('.is-invalid').removeClass('is-invalid');

            $('#soalModal').modal('show');
        }

        $('#correct_answer').change(function() {
            $(this).removeClass('is-invalid');

            const selectedOption = $(this).val();
            if (selectedOption) {
                const optionData = getEditorData('option_' + selectedOption.toLowerCase());
                if (!optionData.trim()) {
                    $('#option_' + selectedOption.toLowerCase()).addClass('border-warning');

                    if (editorInstances['option_' + selectedOption.toLowerCase()]) {
                        editorInstances['option_' + selectedOption.toLowerCase()].focus();
                    }
                }
            }
        });

        $(document).ready(function() {
            const editorIds = ['question', 'explanation', 'option_a', 'option_b', 'option_c', 'option_d',
                'option_e'
            ];

            editorIds.forEach(id => {
                initCKEditor(id);
            });

            @if (isset($soal) && $soal->questions)
                questions = {!! json_encode(
                    $soal->questions->map(function ($q) {
                        return [
                            'id' => $q->id,
                            'question' => $q->question,
                            'option_a' => $q->option_a ?? '',
                            'option_b' => $q->option_b ?? '',
                            'option_c' => $q->option_c ?? '',
                            'option_d' => $q->option_d ?? '',
                            'option_e' => $q->option_e ?? '',
                            'correct_answer' => $q->correct_answer,
                            'explanation' => $q->pembahasan ?? '',
                        ];
                    }),
                ) !!};
                renderQuestions();
            @else
                loadDraftFromDatabase();
            @endif

            @if (!isset($soal))
                autosaveTimer = setInterval(saveDraftToDatabase, 30000);
                $('#title').on('input change', debounce(saveDraftToDatabase, 3000));
            @endif

            $('.select2').select2({
                theme: 'bootstrap-5',
                placeholder: '-- Pilih --',
                allowClear: true,
                width: '100%'
            });
        });

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
                        if (response.data.title) {
                            $('#title').val(response.data.title);
                        }

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

        function saveDraftToDatabase() {
            @if (!isset($soal))
                if (!$('#title').val().trim() && questions.length === 0) {
                    return;
                }

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
                            showNotification('Draft tersimpan otomatis', 'info', 2000);
                        } else {
                            console.error('Draft save failed:', response.message);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error saving draft:', xhr.responseJSON);
                        showNotification('Gagal menyimpan draft', 'error', 3000);
                    }
                });
            @endif
        }

        $('#saveDraftBtn').click(function() {
            if (!$('#title').val().trim() && questions.length === 0) {
                showNotification('Tidak ada data untuk disimpan', 'warning');
                return;
            }

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
                    $('#saveDraftBtn').html(originalText);
                    $('#saveDraftBtn').prop('disabled', false);
                }
            });
        });

        function showNotification(message, type = 'info', duration = 3000) {
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

            setTimeout(function() {
                $('.autosave-status').fadeOut(function() {
                    $(this).remove();
                });
            }, duration);
        }

        $('#soalModal').on('show.bs.modal', function() {
            if (editingIndex === -1) {
                resetForm();
            }
        });

        function renderQuestions() {
            const container = $('#questionsContainer');
            container.empty();

            if (questions.length === 0) {
                $('#noQuestionsMsg').show();
                return;
            }

            $('#noQuestionsMsg').hide();

            questions.forEach((q, index) => {
                const questionCard = $(`
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
                                <div class="mt-2 question-content-display ck-content"></div>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <strong>Pilihan Jawaban:</strong>
                                    <div class="mt-2" id="options-display-${index}"></div>
                                </div>
                                <div class="col-md-4">
                                    <strong>Jawaban Benar:</strong>
                                    <div class="mt-2">
                                        <span class="badge bg-success">${q.correct_answer}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3" id="explanation-${index}" style="${q.explanation ? '' : 'display: none;'}">
                                <strong>Pembahasan:</strong>
                                <div class="mt-2 explanation-content-display ck-content"></div>
                            </div>
                        </div>
                    </div>
                `);

                questionCard.find('.question-content-display').html(q.question || '');

                const optionsContainer = questionCard.find(`#options-display-${index}`);
                const options = [{
                        key: 'A',
                        value: q.option_a
                    },
                    {
                        key: 'B',
                        value: q.option_b
                    },
                    {
                        key: 'C',
                        value: q.option_c
                    },
                    {
                        key: 'D',
                        value: q.option_d
                    },
                    {
                        key: 'E',
                        value: q.option_e
                    }
                ];

                options.forEach(opt => {
                    if (opt.value && opt.value.trim()) {
                        const isCorrect = q.correct_answer === opt.key;
                        const optionElement = $(`
                            <div class="option-item ${isCorrect ? 'correct-answer' : ''}">
                                <strong>${opt.key}.</strong>
                                <span class="option-content-display ck-content"></span>
                            </div>
                        `);

                        optionElement.find('.option-content-display').html(opt.value);
                        optionsContainer.append(optionElement);
                    }
                });

                if (q.explanation) {
                    questionCard.find('.explanation-content-display').html(q.explanation);
                }

                const hiddenInputsHtml = `
                    ${q.id ? `<input type="hidden" name="questions[${index}][id]" value="${q.id}">` : ''}
                    <input type="hidden" name="questions[${index}][question]" value="${escapeForInput(q.question)}">
                    <input type="hidden" name="questions[${index}][option_a]" value="${escapeForInput(q.option_a)}">
                    <input type="hidden" name="questions[${index}][option_b]" value="${escapeForInput(q.option_b)}">
                    <input type="hidden" name="questions[${index}][option_c]" value="${escapeForInput(q.option_c)}">
                    <input type="hidden" name="questions[${index}][option_d]" value="${escapeForInput(q.option_d)}">
                    <input type="hidden" name="questions[${index}][option_e]" value="${escapeForInput(q.option_e)}">
                    <input type="hidden" name="questions[${index}][correct_answer]" value="${q.correct_answer}">
                    <input type="hidden" name="questions[${index}][explanation]" value="${escapeForInput(q.explanation || '')}">
                `;

                questionCard.find('.question-body').append(hiddenInputsHtml);
                container.append(questionCard);
            });
        }

        function escapeForInput(text) {
            if (!text) return '';
            return text.replace(/"/g, '&quot;').replace(/'/g, '&#x27;');
        }

        function toggleExplanation(index) {
            const explanationEl = $(`#explanation-${index}`);
            explanationEl.toggle();
        }

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

                    @if (!isset($soal))
                        saveDraftToDatabase();
                    @endif

                    showNotification('Soal berhasil dihapus', 'success');
                }
            });
        }

        $('#mainForm').submit(function(e) {
            if (questions.length === 0) {
                e.preventDefault();
                Swal.fire('Mohon Maaf!', 'Minimal harus ada 1 soal!', 'error');
                return false;
            }

            @if (!isset($soal))
                if (autosaveTimer) {
                    clearInterval(autosaveTimer);
                }

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

        @if (!isset($soal))
            let hasUnsavedChanges = false;

            $('#title').on('input', function() {
                hasUnsavedChanges = true;
            });

            function disableBeforeUnload() {
                hasUnsavedChanges = false;
                $(window).off('beforeunload');
            }

            $(window).on('beforeunload', function(e) {
                if (hasUnsavedChanges || questions.length > 0) {
                    return 'Anda memiliki perubahan yang belum disimpan. Yakin ingin meninggalkan halaman?';
                }
            });

            $(document).on('ajaxSuccess', function(event, xhr, settings) {
                if (settings.url.includes('draft.save')) {
                    hasUnsavedChanges = false;
                }
            });

            $('#mainForm').submit(function(e) {
                disableBeforeUnload();

                if (questions.length === 0) {
                    e.preventDefault();
                    Swal.fire('Mohon Maaf!', 'Minimal harus ada 1 soal!', 'error');
                    return false;
                }

                if (autosaveTimer) {
                    clearInterval(autosaveTimer);
                }

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

        if (typeof Swal === 'undefined') {
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
            document.head.appendChild(script);
        }
    </script>
@endpush
