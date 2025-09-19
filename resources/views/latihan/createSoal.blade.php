@extends('template.app')
@section('title', 'Tambah Soal')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Tambah Soal untuk "{{ $course->title }}"</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('latihan.storeSoal') }}" method="POST">
                    @csrf
                    <input type="hidden" name="course_id" value="{{ $course->id }}">

                    <!-- Pertanyaan -->
                    <!-- Pertanyaan -->
                    <div class="mb-3">
                        <label class="form-label">Pertanyaan *</label>
                        <textarea id="question" name="question" class="editor"></textarea>
                    </div>

                    <!-- Pilihan Jawaban -->
                    <div class="mb-3">
                        <label class="form-label">Pilihan Jawaban *</label>
                        @foreach (['A', 'B', 'C', 'D', 'E'] as $opt)
                            <div class="mb-2">
                                <label class="form-label">Jawaban {{ $opt }}</label>
                                <textarea id="option_{{ strtolower($opt) }}" name="option_{{ strtolower($opt) }}" class="editor"></textarea>
                            </div>
                        @endforeach
                    </div>

                    <!-- Jawaban Benar -->
                    <div class="mb-3">
                        <label class="form-label">Jawaban Benar *</label>
                        <select name="correct_answer" class="form-select" required>
                            <option value="">-- Pilih Jawaban Benar --</option>
                            @foreach (['A', 'B', 'C', 'D', 'E'] as $opt)
                                <option value="{{ $opt }}">{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Pembahasan -->
                    <div class="mb-3">
                        <label class="form-label">Pembahasan</label>
                        <textarea id="explanation" name="explanation" class="editor"></textarea>
                    </div>

                    <div class="d-flex justify-content-end">
                        <a href="{{ route('latihan.index', $course->id) }}" class="btn btn-secondary me-2">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan Soal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        /* Semua editor */
        .ck-editor__editable {
            min-height: 120px;
        }

        /* Pertanyaan lebih besar */
        #question~.ck-editor .ck-editor__editable {
            min-height: 300px !important;
        }

        /* Pembahasan sedang */
        #explanation~.ck-editor .ck-editor__editable {
            min-height: 250px !important;
        }

        /* Pilihan jawaban */
        #option_a~.ck-editor .ck-editor__editable,
        #option_b~.ck-editor .ck-editor__editable,
        #option_c~.ck-editor .ck-editor__editable,
        #option_d~.ck-editor .ck-editor__editable,
        #option_e~.ck-editor .ck-editor__editable {
            min-height: 150px !important;
        }
    </style>
@endpush

@push('js')
    <script src="https://cdn.ckeditor.com/ckeditor5/41.0.0/classic/ckeditor.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>

    <script>
        document.querySelector("form").addEventListener("submit", function(e) {
            let editors = ['question', 'option_a', 'option_b', 'option_c', 'option_d', 'option_e'];
            let valid = true;

            editors.forEach(id => {
                let editorData = ClassicEditor.instances && ClassicEditor.instances[id] ?
                    ClassicEditor.instances[id].getData().trim() :
                    document.querySelector('#' + id).value.trim();

                if (!editorData) {
                    alert("Field " + id.replace('_', ' ') + " wajib diisi!");
                    valid = false;
                }
            });

            if (!valid) {
                e.preventDefault();
            }
        });
    </script>

    <script>
        function initCKEditor(id) {
            ClassicEditor.create(document.querySelector('#' + id), {
                ckfinder: {
                    uploadUrl: "{{ route('latihan.ckeditor.upload') }}?_token={{ csrf_token() }}"
                },
                toolbar: {
                    items: [
                        'heading', '|',
                        'bold', 'italic', 'link', '|',
                        'bulletedList', 'numberedList', '|',
                        'insertTable', 'imageUpload', '|',
                        'undo', 'redo'
                    ]
                },
                table: {
                    contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells']
                }
            }).catch(error => console.error(error));
        }

        // Init semua editor
        ['question', 'explanation', 'option_a', 'option_b', 'option_c', 'option_d', 'option_e']
        .forEach(id => initCKEditor(id));
    </script>
@endpush
