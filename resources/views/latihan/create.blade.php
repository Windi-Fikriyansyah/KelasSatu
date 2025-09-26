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
                            {{-- Tambahkan setelah input quiz_type --}}
                            @if (isset($soal))
                                <input type="hidden" name="quiz_id" value="{{ $soal->id }}">
                            @endif

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
                                            {{-- <button type="button" class="btn btn-success me-2" id="saveDraftBtn">
                                                <i class="bi bi-save"></i> Simpan Draft
                                            </button> --}}
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
    <!-- Modal untuk Tambah/Edit Soal -->
    <div class="modal fade" id="soalModal" tabindex="-1" aria-labelledby="soalModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="soalModalLabel">Tambah Soal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Modal HTML - Ganti bagian modal-body -->
                <div class="modal-body">
                    <!-- Tabs Navigation -->
                    <ul class="nav nav-tabs mb-3" id="questionTypeTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="pilihan-ganda-tab" data-bs-toggle="tab"
                                data-bs-target="#pilihan-ganda" type="button" role="tab" aria-controls="pilihan-ganda"
                                aria-selected="true">
                                Pilihan Ganda
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pgk-kategori-tab" data-bs-toggle="tab"
                                data-bs-target="#pgk-kategori" type="button" role="tab" aria-controls="pgk-kategori"
                                aria-selected="false">
                                PGK Kategori
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pgk-mcma-tab" data-bs-toggle="tab" data-bs-target="#pgk-mcma"
                                type="button" role="tab" aria-controls="pgk-mcma" aria-selected="false">
                                PGK MCMA
                            </button>
                        </li>
                    </ul>

                    <!-- Tabs Content -->
                    <div class="tab-content" id="questionTypeTabsContent">
                        <!-- Pilihan Ganda Tab -->
                        <div class="tab-pane fade show active" id="pilihan-ganda" role="tabpanel"
                            aria-labelledby="pilihan-ganda-tab">
                            <form id="questionForm">
                                <input type="hidden" id="editIndex" value="">
                                <input type="hidden" id="questionType" value="multiple_choice">

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

                        <!-- PGK Kategori Tab -->
                        <div class="tab-pane fade" id="pgk-kategori" role="tabpanel" aria-labelledby="pgk-kategori-tab">
                            <form id="pgkKategoriForm">
                                <input type="hidden" id="editIndexPgkKat" value="">
                                <input type="hidden" id="questionTypePgkKat" value="pgk_kategori">

                                <!-- Pertanyaan -->
                                <div class="mb-3">
                                    <label for="questionPgkKat" class="form-label">Pertanyaan *</label>
                                    <textarea name="question" id="questionPgkKat" class="form-control ckeditor" rows="4" required></textarea>
                                    <div class="invalid-feedback">Pertanyaan harus diisi</div>
                                </div>

                                <!-- Custom Labels -->
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="trueLabel" class="form-label">Label Benar *</label>
                                        <input type="text" id="trueLabel" class="form-control"
                                            placeholder="Contoh: Setuju, Benar, Ya" value="Benar" required>
                                        <div class="invalid-feedback">Label benar harus diisi</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="falseLabel" class="form-label">Label Salah *</label>
                                        <input type="text" id="falseLabel" class="form-control"
                                            placeholder="Contoh: Tidak Setuju, Salah, Tidak" value="Salah" required>
                                        <div class="invalid-feedback">Label salah harus diisi</div>
                                    </div>
                                </div>

                                <!-- Pernyataan A-E -->
                                <div class="mb-3">
                                    <label class="form-label">Pernyataan *</label>

                                    <div class="statement-item mb-3 border rounded p-3">
                                        <div class="mb-2">
                                            <label for="statementA" class="form-label fw-bold">Pernyataan A *</label>
                                            <textarea id="statementA" class="form-control ckeditor" rows="3" required placeholder="Tulis pernyataan A"></textarea>
                                            <div class="invalid-feedback">Pernyataan A harus diisi</div>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Jawaban untuk Pernyataan A *</label>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="answerA"
                                                    id="answerA_true" value="true" required>
                                                <label class="form-check-label" for="answerA_true"
                                                    id="labelA_true">Benar</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="answerA"
                                                    id="answerA_false" value="false" required>
                                                <label class="form-check-label" for="answerA_false"
                                                    id="labelA_false">Salah</label>
                                            </div>
                                            <div class="invalid-feedback">Pilih jawaban untuk pernyataan A</div>
                                        </div>
                                    </div>

                                    <div class="statement-item mb-3 border rounded p-3">
                                        <div class="mb-2">
                                            <label for="statementB" class="form-label fw-bold">Pernyataan B *</label>
                                            <textarea id="statementB" class="form-control ckeditor" rows="3" required placeholder="Tulis pernyataan B"></textarea>
                                            <div class="invalid-feedback">Pernyataan B harus diisi</div>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Jawaban untuk Pernyataan B *</label>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="answerB"
                                                    id="answerB_true" value="true" required>
                                                <label class="form-check-label" for="answerB_true"
                                                    id="labelB_true">Benar</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="answerB"
                                                    id="answerB_false" value="false" required>
                                                <label class="form-check-label" for="answerB_false"
                                                    id="labelB_false">Salah</label>
                                            </div>
                                            <div class="invalid-feedback">Pilih jawaban untuk pernyataan B</div>
                                        </div>
                                    </div>

                                    <div class="statement-item mb-3 border rounded p-3">
                                        <div class="mb-2">
                                            <label for="statementC" class="form-label fw-bold">Pernyataan C</label>
                                            <textarea id="statementC" class="form-control ckeditor" rows="3" placeholder="Tulis pernyataan C (opsional)"></textarea>
                                        </div>
                                        <div class="mb-2" id="answerC_group" style="display: none;">
                                            <label class="form-label">Jawaban untuk Pernyataan C</label>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="answerC"
                                                    id="answerC_true" value="true">
                                                <label class="form-check-label" for="answerC_true"
                                                    id="labelC_true">Benar</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="answerC"
                                                    id="answerC_false" value="false">
                                                <label class="form-check-label" for="answerC_false"
                                                    id="labelC_false">Salah</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="statement-item mb-3 border rounded p-3">
                                        <div class="mb-2">
                                            <label for="statementD" class="form-label fw-bold">Pernyataan D</label>
                                            <textarea id="statementD" class="form-control ckeditor" rows="3" placeholder="Tulis pernyataan D (opsional)"></textarea>
                                        </div>
                                        <div class="mb-2" id="answerD_group" style="display: none;">
                                            <label class="form-label">Jawaban untuk Pernyataan D</label>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="answerD"
                                                    id="answerD_true" value="true">
                                                <label class="form-check-label" for="answerD_true"
                                                    id="labelD_true">Benar</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="answerD"
                                                    id="answerD_false" value="false">
                                                <label class="form-check-label" for="answerD_false"
                                                    id="labelD_false">Salah</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="statement-item mb-3 border rounded p-3">
                                        <div class="mb-2">
                                            <label for="statementE" class="form-label fw-bold">Pernyataan E</label>
                                            <textarea id="statementE" class="form-control ckeditor" rows="3" placeholder="Tulis pernyataan E (opsional)"></textarea>
                                        </div>
                                        <div class="mb-2" id="answerE_group" style="display: none;">
                                            <label class="form-label">Jawaban untuk Pernyataan E</label>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="answerE"
                                                    id="answerE_true" value="true">
                                                <label class="form-check-label" for="answerE_true"
                                                    id="labelE_true">Benar</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="answerE"
                                                    id="answerE_false" value="false">
                                                <label class="form-check-label" for="answerE_false"
                                                    id="labelE_false">Salah</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Pembahasan -->
                                <div class="mb-3">
                                    <label for="explanationPgkKat" class="form-label">Pembahasan</label>
                                    <textarea name="explanation" id="explanationPgkKat" class="form-control ckeditor" rows="4"
                                        placeholder="Pembahasan jawaban (opsional)"></textarea>
                                </div>
                            </form>
                        </div>

                        <!-- PGK MCMA Tab -->
                        <div class="tab-pane fade" id="pgk-mcma" role="tabpanel" aria-labelledby="pgk-mcma-tab">
                            <form id="pgkMcmaForm">
                                <input type="hidden" id="editIndexPgkMcma" value="">
                                <input type="hidden" id="questionTypePgkMcma" value="pgk_mcma">

                                <!-- Pertanyaan -->
                                <div class="mb-3">
                                    <label for="questionPgkMcma" class="form-label">Pertanyaan *</label>
                                    <textarea name="question" id="questionPgkMcma" class="form-control ckeditor" rows="4" required></textarea>
                                    <div class="invalid-feedback">Pertanyaan harus diisi</div>
                                </div>

                                <!-- Options dengan checkbox untuk multiple correct answers -->
                                <div class="mb-3">
                                    <label class="form-label">Pilihan Jawaban * <small class="text-muted">(Pilih semua
                                            jawaban yang benar)</small></label>

                                    <div class="option-item mb-2 border rounded p-2">
                                        <div class="input-group mb-2">
                                            <span class="input-group-text">A</span>
                                            <input type="text" name="option_a" id="optionMcmaA" class="form-control"
                                                placeholder="Pilihan A" required>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="correctMcmaA"
                                                name="correct_answers" value="A">
                                            <label class="form-check-label" for="correctMcmaA">
                                                Pilihan A adalah jawaban yang benar
                                            </label>
                                        </div>
                                    </div>

                                    <div class="option-item mb-2 border rounded p-2">
                                        <div class="input-group mb-2">
                                            <span class="input-group-text">B</span>
                                            <input type="text" name="option_b" id="optionMcmaB" class="form-control"
                                                placeholder="Pilihan B" required>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="correctMcmaB"
                                                name="correct_answers" value="B">
                                            <label class="form-check-label" for="correctMcmaB">
                                                Pilihan B adalah jawaban yang benar
                                            </label>
                                        </div>
                                    </div>

                                    <div class="option-item mb-2 border rounded p-2">
                                        <div class="input-group mb-2">
                                            <span class="input-group-text">C</span>
                                            <input type="text" name="option_c" id="optionMcmaC" class="form-control"
                                                placeholder="Pilihan C">
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="correctMcmaC"
                                                name="correct_answers" value="C">
                                            <label class="form-check-label" for="correctMcmaC">
                                                Pilihan C adalah jawaban yang benar
                                            </label>
                                        </div>
                                    </div>

                                    <div class="option-item mb-2 border rounded p-2">
                                        <div class="input-group mb-2">
                                            <span class="input-group-text">D</span>
                                            <input type="text" name="option_d" id="optionMcmaD" class="form-control"
                                                placeholder="Pilihan D">
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="correctMcmaD"
                                                name="correct_answers" value="D">
                                            <label class="form-check-label" for="correctMcmaD">
                                                Pilihan D adalah jawaban yang benar
                                            </label>
                                        </div>
                                    </div>

                                    <div class="option-item mb-2 border rounded p-2">
                                        <div class="input-group mb-2">
                                            <span class="input-group-text">E</span>
                                            <input type="text" name="option_e" id="optionMcmaE" class="form-control"
                                                placeholder="Pilihan E">
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="correctMcmaE"
                                                name="correct_answers" value="E">
                                            <label class="form-check-label" for="correctMcmaE">
                                                Pilihan E adalah jawaban yang benar
                                            </label>
                                        </div>
                                    </div>

                                    <div class="mt-2">
                                        <small class="text-danger" id="mcma_error" style="display: none;">
                                            Minimal pilih satu jawaban yang benar dan pastikan pilihan tersebut sudah diisi.
                                        </small>
                                    </div>
                                </div>

                                <!-- Pembahasan -->
                                <div class="mb-3">
                                    <label for="explanationPgkMcma" class="form-label">Pembahasan</label>
                                    <textarea name="explanation" id="explanationPgkMcma" class="form-control ckeditor" rows="4"
                                        placeholder="Pembahasan jawaban (opsional)"></textarea>
                                </div>
                            </form>
                        </div>
                    </div>
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
    {{-- KaTeX CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.css">
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
            /* ukuran maksimal */
            height: auto !important;
            /* biar proporsional */
            display: inline-block !important;
            /* biar tidak full block */
            float: left !important;
            /* taruh ke kiri */
            margin: 5px 10px 5px 0 !important;
        }
    </style>
@endpush

@push('js')
    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/super-build/ckeditor.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.js"></script>
    <script>
        let editorInstances = {};
        let editingIndex = -1; // default: tidak sedang edit soal
        let questions = [];
        let autosaveTimer = null;
        let quizId = @json(isset($soal) ? $soal->id : null);

        function renderLatex(content) {
            // Regex untuk menemukan LaTeX inline $...$ dan block $$...$$
            const inlineRegex = /\$([^$]+)\$/g;
            const blockRegex = /\$\$([^$]+)\$\$/g;

            let renderedContent = content;

            // Render inline LaTeX
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

            // Render block LaTeX
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

            return renderedContent;
        }

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
                        'bulletedList', 'numberedList', '|',
                        'alignment', '|',
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
                // Simpan instance editor untuk digunakan nanti
                editorInstances[id] = editor;

                // Tambahkan event listener untuk auto-remove validation error
                editor.model.document.on('change:data', () => {
                    $('#' + id).removeClass('is-invalid border-warning');

                    // Jika field yang diubah sesuai dengan jawaban benar yang dipilih, hilangkan error
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

        // Fungsi helper untuk mendapatkan data dari CKEditor
        function getEditorData(editorId) {
            if (editorInstances[editorId]) {
                return editorInstances[editorId].getData();
            }
            return '';
        }

        // Fungsi helper untuk set data ke CKEditor
        function setEditorData(id, value) {
            if (editorInstances[id]) {
                editorInstances[id].setData(value || '');
            } else {
                $(`#${id}`).val(value || '');
            }
        }


        // Update fungsi validateForm untuk menggunakan CKEditor
        function validateForm() {
            let isValid = true;
            $('.is-invalid').removeClass('is-invalid');

            // Get active tab
            const activeTab = $('.tab-pane.active').attr('id');

            if (activeTab === 'pilihan-ganda') {
                return validateMultipleChoice();
            } else if (activeTab === 'pgk-kategori') {
                return validatePgkKategori();
            } else if (activeTab === 'pgk-mcma') {
                return validatePgkMcma();
            }

            return isValid;
        }

        function validateMultipleChoice() {
            let isValid = true;

            // Validasi pertanyaan
            const questionData = getEditorData('question');
            if (!questionData.trim()) {
                $('#question').addClass('is-invalid');
                isValid = false;
            }

            // Validasi pilihan A dan B (wajib)
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

            // Validasi jawaban benar
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

        function validatePgkKategori() {
            let isValid = true;

            // Validasi pertanyaan
            const questionData = getEditorData('questionPgkKat');
            if (!questionData.trim()) {
                $('#questionPgkKat').addClass('is-invalid');
                isValid = false;
            }

            // Validasi label
            if (!$('#trueLabel').val().trim()) {
                $('#trueLabel').addClass('is-invalid');
                isValid = false;
            }

            if (!$('#falseLabel').val().trim()) {
                $('#falseLabel').addClass('is-invalid');
                isValid = false;
            }

            // Validasi pernyataan A dan B (wajib)
            const statementA = getEditorData('statementA');
            if (!statementA.trim()) {
                $('#statementA').addClass('is-invalid');
                isValid = false;
            } else {
                // Cek apakah jawaban A sudah dipilih
                if (!$('input[name="answerA"]:checked').length) {
                    $('input[name="answerA"]').addClass('is-invalid');
                    isValid = false;
                }
            }

            const statementB = getEditorData('statementB');
            if (!statementB.trim()) {
                $('#statementB').addClass('is-invalid');
                isValid = false;
            } else {
                // Cek apakah jawaban B sudah dipilih
                if (!$('input[name="answerB"]:checked').length) {
                    $('input[name="answerB"]').addClass('is-invalid');
                    isValid = false;
                }
            }

            // Validasi pernyataan opsional (C, D, E) - jika diisi harus ada jawaban
            ['C', 'D', 'E'].forEach(letter => {
                const statementContent = getEditorData(`statement${letter}`);
                if (statementContent.trim()) {
                    if (!$(`input[name="answer${letter}"]:checked`).length) {
                        $(`input[name="answer${letter}"]`).addClass('is-invalid');
                        isValid = false;
                    }
                }
            });

            return isValid;
        }

        // 7. Fungsi validasi untuk PGK MCMA
        function validatePgkMcma() {
            let isValid = true;

            // Validasi pertanyaan
            const questionData = getEditorData('questionPgkMcma');
            if (!questionData.trim()) {
                $('#questionPgkMcma').addClass('is-invalid');
                isValid = false;
            }

            // Validasi pilihan A dan B (wajib)
            if (!$('#optionMcmaA').val().trim()) {
                $('#optionMcmaA').addClass('is-invalid');
                isValid = false;
            }

            if (!$('#optionMcmaB').val().trim()) {
                $('#optionMcmaB').addClass('is-invalid');
                isValid = false;
            }

            // Validasi minimal satu jawaban benar dipilih
            const checkedAnswers = $('input[name="correct_answers"]:checked');
            if (checkedAnswers.length === 0) {
                $('#mcma_error').show();
                isValid = false;
            } else {
                $('#mcma_error').hide();

                // Validasi bahwa pilihan yang dipilih sebagai benar sudah diisi
                let hasEmptyCorrectOption = false;
                checkedAnswers.each(function() {
                    const optionValue = $(this).val();
                    const optionInput = $(`#optionMcma${optionValue}`);
                    if (!optionInput.val().trim()) {
                        optionInput.addClass('is-invalid');
                        hasEmptyCorrectOption = true;
                    }
                });

                if (hasEmptyCorrectOption) {
                    $('#mcma_error').text('Pilihan yang dipilih sebagai jawaban benar harus diisi terlebih dahulu.').show();
                    isValid = false;
                }
            }

            return isValid;
        }



        // Update fungsi Save question untuk menggunakan CKEditor
        $('#saveQuestionBtn').click(function() {
            if (validateForm()) {
                const activeTab = $('.tab-pane.active').attr('id');
                let questionData = {};

                // Tentukan editingIndex berdasarkan tab aktif
                if (activeTab === 'pgk-kategori') {
                    editingIndex = $('#editIndexPgkKat').val() !== '' ? parseInt($('#editIndexPgkKat').val()) : -1;
                } else if (activeTab === 'pgk-mcma') {
                    editingIndex = $('#editIndexPgkMcma').val() !== '' ? parseInt($('#editIndexPgkMcma').val()) : -
                        1;
                } else {
                    editingIndex = $('#editIndex').val() !== '' ? parseInt($('#editIndex').val()) : -1;
                }

                if (activeTab === 'pilihan-ganda') {
                    questionData = {
                        question_type: 'multiple_choice',
                        question: getEditorData('question'),
                        option_a: getEditorData('option_a'),
                        option_b: getEditorData('option_b'),
                        option_c: getEditorData('option_c'),
                        option_d: getEditorData('option_d'),
                        option_e: getEditorData('option_e'),
                        correct_answer: $('#correct_answer').val(),
                        explanation: getEditorData('explanation')
                    };

                    if (editingIndex >= 0 && questions[editingIndex] && questions[editingIndex].id) {
                        questionData.id = questions[editingIndex].id;
                    }

                } else if (activeTab === 'pgk-kategori') {
                    const statements = {};
                    const answers = {};

                    ['A', 'B', 'C', 'D', 'E'].forEach(letter => {
                        const statementContent = getEditorData(`statement${letter}`);
                        if (statementContent && statementContent.trim()) {
                            statements[letter.toLowerCase()] = statementContent;
                            const selectedAnswer = $(`input[name="answer${letter}"]:checked`).val();
                            if (selectedAnswer) {
                                // PERBAIKAN: Simpan sebagai boolean, bukan string
                                answers[letter.toLowerCase()] = selectedAnswer === 'true';
                            }
                        }
                    });

                    questionData = {
                        question_type: 'pgk_kategori',
                        question: getEditorData('questionPgkKat'),
                        statements: statements,
                        correct_answers: answers, // Ini akan berupa object dengan boolean values
                        custom_labels: {
                            true_label: $('#trueLabel').val(),
                            false_label: $('#falseLabel').val()
                        },
                        explanation: getEditorData('explanationPgkKat')
                    };

                    if (editingIndex >= 0 && questions[editingIndex] && questions[editingIndex].id) {
                        questionData.id = questions[editingIndex].id;
                    }

                } else if (activeTab === 'pgk-mcma') {
                    const options = {};
                    const correctAnswers = [];

                    ['A', 'B', 'C', 'D', 'E'].forEach(letter => {
                        const optionValue = $(`#optionMcma${letter}`).val();
                        if (optionValue && optionValue.trim()) {
                            options[`option_${letter.toLowerCase()}`] = optionValue;

                            if ($(`#correctMcma${letter}`).is(':checked')) {
                                correctAnswers.push(letter);
                            }
                        }
                    });

                    questionData = {
                        question_type: 'pgk_mcma',
                        question: getEditorData('questionPgkMcma'),
                        ...options,
                        correct_answers: correctAnswers,
                        explanation: getEditorData('explanationPgkMcma')
                    };

                    if (editingIndex >= 0 && questions[editingIndex] && questions[editingIndex].id) {
                        questionData.id = questions[editingIndex].id;
                    }
                }



                if (editingIndex >= 0) {
                    questions[editingIndex] = questionData;
                    showNotification('Soal berhasil diupdate', 'success');
                } else {
                    questions.push(questionData);
                    showNotification('Soal berhasil ditambahkan', 'success');
                }

                renderQuestions();
                saveDraftToDatabase();

                $('#soalModal').modal('hide');
                resetForm();
            }
        });

        // Update fungsi resetForm untuk CKEditor
        function resetForm() {
            // Reset semua form
            $('#questionForm')[0].reset();
            $('#pgkKategoriForm')[0].reset();
            $('#pgkMcmaForm')[0].reset();

            // Reset edit indexes
            $('#editIndex').val('');
            $('#editIndexPgkKat').val('');
            $('#editIndexPgkMcma').val('');

            // Reset modal labels
            $('#soalModalLabel').text('Tambah Soal');
            $('#saveQuestionBtnText').text('Simpan Soal');
            editingIndex = -1;

            // Reset semua CKEditor instances
            Object.keys(editorInstances).forEach(editorId => {
                setEditorData(editorId, '');
            });

            // Reset form-specific elements
            $('#trueLabel').val('Benar');
            $('#falseLabel').val('Salah');
            updatePgkKategoriLabels();

            // PERBAIKAN: Reset radio buttons dengan lebih thorough
            ['A', 'B', 'C', 'D', 'E'].forEach(letter => {
                $(`#answer${letter}_group`).hide();

                // Clear semua radio button selections
                $(`input[name="answer${letter}"]`).prop('checked', false);
                $(`input[name="answer${letter}"]`).removeAttr('required');

                // Reset ke state default
                $(`input[name="answer${letter}"][value="false"]`).prop('checked', false);
                $(`input[name="answer${letter}"][value="true"]`).prop('checked', false);
            });

            // Reset MCMA checkboxes
            $('input[name="correct_answers"]').prop('checked', false);
            $('#mcma_error').hide();

            // Remove validation classes
            $('.is-invalid').removeClass('is-invalid');
            $('.border-warning').removeClass('border-warning');

            // Kembali ke tab pertama
            $('#pilihan-ganda-tab').tab('show');


        }
        $('#soalModal').on('hidden.bs.modal', function() {
            // Reset form ketika modal ditutup tanpa save
            if (editingIndex !== -1) {
                resetForm();
            }
        });

        function editQuestion(index) {
            const q = questions[index];
            editingIndex = index;



            // Update modal title dan button text SEBELUM reset
            $('#soalModalLabel').text('Edit Soal');
            $('#saveQuestionBtnText').text('Update Soal');

            if (q.question_type === 'pgk_kategori') {
                // Switch ke tab PGK Kategori dulu
                $('#pgk-kategori-tab').tab('show');

                // Tunggu tab aktif, lalu isi data
                setTimeout(() => {
                    // Set question dan explanation
                    setEditorData('questionPgkKat', q.question);
                    setEditorData('explanationPgkKat', q.explanation || '');

                    // Set custom labels
                    if (q.custom_labels) {
                        $('#trueLabel').val(q.custom_labels.true_label || 'Benar');
                        $('#falseLabel').val(q.custom_labels.false_label || 'Salah');
                        updatePgkKategoriLabels();
                    }

                    // Set statements dan answers
                    if (q.statements) {
                        Object.keys(q.statements).forEach(letter => {
                            const upperLetter = letter.toUpperCase();
                            setEditorData(`statement${upperLetter}`, q.statements[letter]);

                            // Show answer group untuk statement yang terisi
                            $(`#answer${upperLetter}_group`).show();
                            $(`input[name="answer${upperLetter}"]`).attr('required', true);
                        });
                    }

                    // PERBAIKAN: Set correct answers dengan konversi yang benar
                    if (q.correct_answers) {


                        Object.keys(q.correct_answers).forEach(letter => {
                            const upperLetter = letter.toUpperCase();
                            let answerValue;

                            // Handle berbagai format data
                            if (typeof q.correct_answers[letter] === 'boolean') {
                                // Jika sudah boolean, convert ke string
                                answerValue = q.correct_answers[letter] ? 'true' : 'false';
                            } else if (typeof q.correct_answers[letter] === 'string') {
                                // Jika string, gunakan langsung
                                answerValue = q.correct_answers[letter];
                            } else {
                                // Default fallback
                                answerValue = 'false';
                            }



                            // Clear existing selections dulu
                            $(`input[name="answer${upperLetter}"]`).prop('checked', false);

                            // Set selection yang benar
                            $(`input[name="answer${upperLetter}"][value="${answerValue}"]`).prop('checked',
                                true);
                        });
                    }

                    // Set editIndex
                    $('#editIndexPgkKat').val(index);


                }, 500);

            } else if (q.question_type === 'pgk_mcma') {
                // Switch ke tab PGK MCMA
                $('#pgk-mcma-tab').tab('show');

                setTimeout(() => {
                    // Set question dan explanation
                    setEditorData('questionPgkMcma', q.question);
                    setEditorData('explanationPgkMcma', q.explanation || '');

                    // Clear semua checkboxes dulu
                    $('input[name="correct_answers"]').prop('checked', false);

                    // Set options
                    ['A', 'B', 'C', 'D', 'E'].forEach(letter => {
                        const optionKey = `option_${letter.toLowerCase()}`;
                        if (q[optionKey]) {
                            $(`#optionMcma${letter}`).val(q[optionKey]);
                        }
                    });

                    // Set correct answers
                    if (q.correct_answers && Array.isArray(q.correct_answers)) {
                        q.correct_answers.forEach(answer => {
                            $(`#correctMcma${answer}`).prop('checked', true);
                        });
                    }

                    // Set editIndex
                    $('#editIndexPgkMcma').val(index);
                }, 500);

            } else {
                // Default: Multiple Choice
                $('#pilihan-ganda-tab').tab('show');

                setTimeout(() => {
                    // Set question dan explanation
                    setEditorData('question', q.question);
                    setEditorData('explanation', q.explanation || '');

                    // Set options
                    setEditorData('option_a', q.option_a || '');
                    setEditorData('option_b', q.option_b || '');
                    setEditorData('option_c', q.option_c || '');
                    setEditorData('option_d', q.option_d || '');
                    setEditorData('option_e', q.option_e || '');

                    // Set correct answer
                    $('#correct_answer').val(q.correct_answer || '').trigger('change');

                    // Set editIndex
                    $('#editIndex').val(index);
                }, 500);
            }

            // Show modal
            $('#soalModal').modal('show');
        }

        // Update event handler untuk correct_answer dropdown
        $('#correct_answer').change(function() {
            $(this).removeClass('is-invalid');

            const selectedOption = $(this).val();
            if (selectedOption) {
                const optionData = getEditorData('option_' + selectedOption.toLowerCase());
                if (!optionData.trim()) {
                    // Beri highlight atau warning bahwa field harus diisi
                    $('#option_' + selectedOption.toLowerCase()).addClass('border-warning');

                    // Focus ke CKEditor yang diperlukan
                    if (editorInstances['option_' + selectedOption.toLowerCase()]) {
                        editorInstances['option_' + selectedOption.toLowerCase()].focus();
                    }
                }
            }
        });

        // Init semua editor saat dokumen ready
        $(document).ready(function() {
            // Init CKEditor untuk semua field yang diperlukan
            const editorIds = [
                // Pilihan Ganda
                'question', 'explanation', 'option_a', 'option_b', 'option_c', 'option_d', 'option_e',
                // PGK Kategori
                'questionPgkKat', 'explanationPgkKat', 'statementA', 'statementB', 'statementC', 'statementD',
                'statementE',
                // PGK MCMA
                'questionPgkMcma', 'explanationPgkMcma'
            ];
            editorIds.forEach(id => {
                initCKEditor(id);
            });

            // Event handlers untuk PGK Kategori - update label dinamis
            $('#trueLabel, #falseLabel').on('input', function() {
                updatePgkKategoriLabels();
            });

            // Event handlers untuk menampilkan/sembunyikan grup jawaban PGK Kategori
            $('#statementC, #statementD, #statementE').on('input', function() {
                const statementId = $(this).attr('id');
                const letter = statementId.replace('statement', '');
                toggleAnswerGroup(letter);
            });
            // Load existing questions jika dalam mode edit
            @if (isset($soal) && $soal->questions)
                questions = {!! json_encode(
                    $soal->questions->map(function ($q) {
                        $questionData = [
                            'question' => $q->question,
                            'explanation' => $q->explanation,
                            'question_type' => $q->question_type ?? 'multiple_choice',
                            'id' => $q->id,
                        ];

                        if ($q->question_type === 'pgk_kategori') {
                            $questionData['statements'] = $q->statements ?? [];
                            $questionData['correct_answers'] = $q->correct_answers ?? [];
                            $questionData['custom_labels'] = $q->custom_labels ?? [];
                        } elseif ($q->question_type === 'pgk_mcma') {
                            $questionData['option_a'] = $q->option_a;
                            $questionData['option_b'] = $q->option_b;
                            $questionData['option_c'] = $q->option_c;
                            $questionData['option_d'] = $q->option_d;
                            $questionData['option_e'] = $q->option_e;
                            $questionData['correct_answers'] = $q->correct_answers ?? [];
                        } else {
                            $questionData['option_a'] = $q->option_a;
                            $questionData['option_b'] = $q->option_b;
                            $questionData['option_c'] = $q->option_c;
                            $questionData['option_d'] = $q->option_d;
                            $questionData['option_e'] = $q->option_e;
                            $questionData['correct_answer'] = $q->correct_answer;
                        }

                        return $questionData;
                    }),
                ) !!};


                renderQuestions();
            @else
                loadDraftFromDatabase();
            @endif

            // Autosave setiap 30 detik (hanya jika bukan mode edit)
            @if (!isset($soal))
                let autosaveTimer = setInterval(saveDraftToDatabase, 30000);
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

        function updatePgkKategoriLabels() {
            const trueLabel = $('#trueLabel').val() || 'Benar';
            const falseLabel = $('#falseLabel').val() || 'Salah';

            ['A', 'B', 'C', 'D', 'E'].forEach(letter => {
                $(`#label${letter}_true`).text(trueLabel);
                $(`#label${letter}_false`).text(falseLabel);
            });
        }

        function toggleAnswerGroup(letter) {
            const statementContent = getEditorData(`statement${letter}`);
            const answerGroup = $(`#answer${letter}_group`);

            if (statementContent.trim()) {
                answerGroup.show();
                // Set required untuk radio buttons
                $(`input[name="answer${letter}"]`).attr('required', true);
            } else {
                answerGroup.hide();
                // Uncheck radio buttons dan remove required
                $(`input[name="answer${letter}"]`).prop('checked', false).removeAttr('required');
            }
        }

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

                        // Load questions dari questions_data dengan parsing yang benar
                        if (response.data.questions && response.data.questions.length > 0) {
                            // Pastikan data dari database di-parse dengan benar
                            questions = response.data.questions.map(q => {
                                // Parse JSON strings jika diperlukan
                                if (q.question_type === 'pgk_kategori') {
                                    return {
                                        ...q,
                                        statements: typeof q.statements === 'string' ? JSON.parse(q
                                            .statements) : q.statements,
                                        correct_answers: typeof q.correct_answers === 'string' ? JSON
                                            .parse(q.correct_answers) : q.correct_answers,
                                        custom_labels: typeof q.custom_labels === 'string' ? JSON.parse(
                                            q.custom_labels) : q.custom_labels
                                    };
                                } else if (q.question_type === 'pgk_mcma') {
                                    return {
                                        ...q,
                                        correct_answers: typeof q.correct_answers === 'string' ? JSON
                                            .parse(q.correct_answers) : q.correct_answers
                                    };
                                }
                                return q;
                            });


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
        // Fungsi untuk save draft ke database (hanya untuk mode tambah)
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
                        questions: questions, // Data akan disimpan di questions_data
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




        // Hilangkan warning ketika user mulai mengisi field
        $('#option_a, #option_b, #option_c, #option_d, #option_e').on('input', function() {
            $(this).removeClass('is-invalid border-warning');

            // Jika field yang diisi sesuai dengan jawaban benar yang dipilih, hilangkan error
            const fieldLetter = $(this).attr('id').split('_')[1].toUpperCase();
            const correctAnswer = $('#correct_answer').val();

            if (fieldLetter === correctAnswer) {
                $('#correct_answer').removeClass('is-invalid');
            }
        });

        $('#correct_answer').change(function() {
            $(this).removeClass('is-invalid');
        });
        // Render questions
        // Fungsi untuk render questions - PERBAIKAN BAGIAN INI
        function renderQuestions() {
            const container = $('#questionsContainer');
            container.empty();

            if (questions.length === 0) {
                $('#noQuestionsMsg').show();
                return;
            }

            $('#noQuestionsMsg').hide();

            questions.forEach((q, index) => {


                let questionTypeLabel = '';
                let optionsHtml = '';
                let correctAnswerHtml = '';

                if (q.question_type === 'pgk_kategori') {
                    questionTypeLabel = '<span class="badge bg-info">PGK Kategori</span>';

                    // Render statements dengan pengecekan yang lebih teliti
                    if (q.statements) {
                        const statementsData = typeof q.statements === 'string' ?
                            JSON.parse(q.statements) : q.statements;

                        const answersData = typeof q.correct_answers === 'string' ?
                            JSON.parse(q.correct_answers) : q.correct_answers;

                        const labelsData = typeof q.custom_labels === 'string' ?
                            JSON.parse(q.custom_labels) : q.custom_labels;



                        Object.keys(statementsData).forEach(key => {
                            const letter = key.toUpperCase();

                            // PERBAIKAN: Handle string 'true'/'false' dan boolean true/false
                            let isCorrect = false;
                            if (answersData && answersData[key] !== undefined) {
                                if (typeof answersData[key] === 'string') {
                                    isCorrect = answersData[key] === 'true';
                                } else {
                                    isCorrect = answersData[key] === true;
                                }
                            }


                            const correctLabel = labelsData ?
                                (isCorrect ? labelsData.true_label : labelsData.false_label) :
                                (isCorrect ? 'Benar' : 'Salah');

                            optionsHtml += `
                        <div class="statement-item ${isCorrect ? 'correct-answer' : ''}">
                            <strong>Pernyataan ${letter}:</strong>
                            <div class="ck-content">${renderLatex(statementsData[key])}</div>
                            <small class="text-muted">Jawaban: <strong>${correctLabel}</strong></small>
                            ${isCorrect ? '<i class="bi bi-check-circle text-success ms-2"></i>' : '<i class="bi bi-x-circle text-danger ms-2"></i>'}
                        </div>
                    `;
                        });
                    }

                    correctAnswerHtml =
                        '<span class="text-muted">Lihat jawaban pada masing-masing pernyataan</span>';

                } else if (q.question_type === 'pgk_mcma') {
                    questionTypeLabel = '<span class="badge bg-warning">PGK MCMA</span>';

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

                    const correctAnswersArray = Array.isArray(q.correct_answers) ?
                        q.correct_answers :
                        (typeof q.correct_answers === 'string' ? JSON.parse(q.correct_answers) : []);

                    options.forEach(opt => {
                        if (opt.value && opt.value.trim()) {
                            const isCorrect = correctAnswersArray.includes(opt.key);
                            optionsHtml += `
                        <div class="option-item ${isCorrect ? 'correct-answer' : ''}">
                            <strong>${opt.key}.</strong>
                            <span class="ck-content">${renderLatex(opt.value)}</span>
                            ${isCorrect ? '<i class="bi bi-check-circle text-success ms-2"></i>' : ''}
                        </div>
                    `;
                        }
                    });

                    correctAnswerHtml = correctAnswersArray.length > 0 ?
                        correctAnswersArray.map(ans => `<span class="badge bg-success me-1">${ans}</span>`).join(
                            '') :
                        '<span class="text-muted">Tidak ada</span>';

                } else {
                    // Multiple choice
                    questionTypeLabel = '<span class="badge bg-primary">Pilihan Ganda</span>';

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
                            optionsHtml += `
                        <div class="option-item ${isCorrect ? 'correct-answer' : ''}">
                            <strong>${opt.key}.</strong>
                            <span class="ck-content">${renderLatex(opt.value)}</span>
                            ${isCorrect ? '<i class="bi bi-check-circle text-success ms-2"></i>' : ''}
                        </div>
                    `;
                        }
                    });

                    correctAnswerHtml = q.correct_answer ?
                        `<span class="badge bg-success">${q.correct_answer}</span>` :
                        '<span class="text-muted">Belum diset</span>';
                }

                const questionCard = $(`
            <div class="question-card" data-index="${index}">
                <div class="question-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Soal ${index + 1} ${questionTypeLabel}</h6>
                        </div>
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
                        <div class="mt-2 ck-content">${renderLatex(q.question || '')}</div>
                    </div>
                    <div class="row">
                        <div class="col-md-8">
                            <strong>${q.question_type === 'pgk_kategori' ? 'Pernyataan:' : 'Pilihan Jawaban:'}</strong>
                            <div class="mt-2">${optionsHtml}</div>
                        </div>
                        <div class="col-md-4">
                            <strong>Jawaban Benar:</strong>
                            <div class="mt-2">${correctAnswerHtml}</div>
                        </div>
                    </div>
                    <div class="mt-3" id="explanation-${index}" style="${q.explanation && q.explanation.trim() ? '' : 'display: none;'}">
                        <strong>Pembahasan:</strong>
                        <div class="mt-2 ck-content explanation-content">${renderLatex(q.explanation || '')}</div>
                    </div>
                </div>
            </div>
        `);

                // Generate hidden inputs dengan data yang benar
                let hiddenInputsHtml = generateHiddenInputs(q, index);
                questionCard.find('.question-body').append(hiddenInputsHtml);

                container.append(questionCard);
            });
        }

        function generateHiddenInputs(q, index) {
            let hiddenInputsHtml = `
        ${q.id ? `<input type="hidden" name="questions[${index}][id]" value="${q.id}">` : ''}
        <input type="hidden" name="questions[${index}][question_type]" value="${q.question_type || 'multiple_choice'}">
        <input type="hidden" name="questions[${index}][question]" value="${escapeForInput(q.question)}">
        <input type="hidden" name="questions[${index}][explanation]" value="${escapeForInput(q.explanation || '')}">
    `;

            if (q.question_type === 'pgk_kategori') {
                // Pastikan data disimpan sebagai JSON string yang valid
                const statementsJson = typeof q.statements === 'object' ? JSON.stringify(q.statements) : q.statements ||
                    '{}';
                const answersJson = typeof q.correct_answers === 'object' ? JSON.stringify(q.correct_answers) : q
                    .correct_answers || '{}';
                const labelsJson = typeof q.custom_labels === 'object' ? JSON.stringify(q.custom_labels) : q
                    .custom_labels || '{}';

                hiddenInputsHtml += `
            <input type="hidden" name="questions[${index}][statements]" value="${escapeForInput(statementsJson)}">
            <input type="hidden" name="questions[${index}][correct_answers]" value="${escapeForInput(answersJson)}">
            <input type="hidden" name="questions[${index}][custom_labels]" value="${escapeForInput(labelsJson)}">
        `;
            } else if (q.question_type === 'pgk_mcma') {
                ['option_a', 'option_b', 'option_c', 'option_d', 'option_e'].forEach(option => {
                    hiddenInputsHtml +=
                        `<input type="hidden" name="questions[${index}][${option}]" value="${escapeForInput(q[option] || '')}">`;
                });

                const correctAnswersJson = Array.isArray(q.correct_answers) ? JSON.stringify(q.correct_answers) : q
                    .correct_answers || '[]';
                hiddenInputsHtml +=
                    `<input type="hidden" name="questions[${index}][correct_answers]" value="${escapeForInput(correctAnswersJson)}">`;
            } else {
                // Multiple choice
                hiddenInputsHtml += `
            <input type="hidden" name="questions[${index}][option_a]" value="${escapeForInput(q.option_a || '')}">
            <input type="hidden" name="questions[${index}][option_b]" value="${escapeForInput(q.option_b || '')}">
            <input type="hidden" name="questions[${index}][option_c]" value="${escapeForInput(q.option_c || '')}">
            <input type="hidden" name="questions[${index}][option_d]" value="${escapeForInput(q.option_d || '')}">
            <input type="hidden" name="questions[${index}][option_e]" value="${escapeForInput(q.option_e || '')}">
            <input type="hidden" name="questions[${index}][correct_answer]" value="${q.correct_answer || ''}">
        `;
            }

            return hiddenInputsHtml;
        }

        setTimeout(function() {
            ['C', 'D', 'E'].forEach(letter => {
                if (editorInstances[`statement${letter}`]) {
                    editorInstances[`statement${letter}`].model.document.on('change:data', () => {
                        toggleAnswerGroup(letter);
                    });
                }
            });
        }, 2000);

        // Fungsi untuk escape HTML pada hidden inputs
        function escapeForInput(text) {
            if (!text) return '';
            return text.replace(/"/g, '&quot;').replace(/'/g, '&#x27;');
        }

        // Fungsi untuk menampilkan/sembunyikan pembahasan
        function toggleExplanation(index) {
            const explanationEl = $(`#explanation-${index}`);
            explanationEl.toggle();
        }




        // Delete question
        function deleteQuestion(index) {
            Swal.fire({
                title: 'Hapus Soal?',
                text: `Soal nomor ${index + 1} akan dihapus permanen!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Hapus dari array questions
                    questions.splice(index, 1);

                    // Re-render questions
                    renderQuestions();

                    // Save ke database (auto-save)
                    saveDraftToDatabase();

                    // Show success notification
                    showNotification('Soal berhasil dihapus', 'success');

                    // Show success alert
                    Swal.fire({
                        title: 'Terhapus!',
                        text: 'Soal berhasil dihapus.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            });
        }
        // Handle form submit
        $('#mainForm').submit(function(e) {
            if (questions.length === 0) {
                e.preventDefault();
                Swal.fire('Mohon Maaf!', 'Minimal harus ada 1 soal!', 'error');
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

            @if (isset($soal))
                if (!$('input[name="quiz_id"]').length) {
                    $(this).append('<input type="hidden" name="quiz_id" value="{{ $soal->id }}">');
                }
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
                    Swal.fire('Mohon Maaf!', 'Minimal harus ada 1 soal!', 'error');
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

        $(document).ready(function() {
            // Pastikan event handler ter-attach dengan baik
            $(document).on('click', '[onclick^="editQuestion"]', function(e) {
                e.preventDefault();
                const onclick = $(this).attr('onclick');
                const index = onclick.match(/\d+/)[0];
                editQuestion(parseInt(index));
            });

            $(document).on('click', '[onclick^="deleteQuestion"]', function(e) {
                e.preventDefault();
                const onclick = $(this).attr('onclick');
                const index = onclick.match(/\d+/)[0];
                deleteQuestion(parseInt(index));
            });
        });
    </script>
@endpush
