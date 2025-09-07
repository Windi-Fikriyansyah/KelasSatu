@extends('template.app')
@section('title', isset($materi) ? 'Edit materi' : 'Tambah materi')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-xxl">
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">{{ isset($materi) ? 'Edit' : 'Tambah' }} materi</h5>
                        <small class="text-muted float-end">Form materi</small>
                    </div>
                    <div class="card-body">
                        <form action="{{ isset($materi) ? route('materi.update', $materi->id) : route('materi.store') }}"
                            method="POST" enctype="multipart/form-data" id="materiForm">
                            @csrf
                            @if (isset($materi))
                                @method('PUT')
                            @endif

                            <!-- Module Selection -->
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="course_id">Course</label>
                                <div class="col-sm-10">
                                    <select id="course_id" name="course_id"
                                        class="form-control select2 @error('course_id') is-invalid @enderror" required>
                                        <option value="">-- Pilih Modul --</option>
                                        @foreach ($kursuss as $kursus)
                                            <option value="{{ $kursus->id }}"
                                                {{ old('course_id', $materi->course_id ?? '') == $kursus->id ? 'selected' : '' }}>
                                                {{ $kursus->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('course_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Type Selection -->
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="type">Tipe Materi</label>
                                <div class="col-sm-10">
                                    <select id="type" name="type"
                                        class="form-control select2 @error('type') is-invalid @enderror" required>
                                        <option value="">-- Pilih Tipe --</option>
                                        <option value="pdf"
                                            {{ old('type', $materi->type ?? '') == 'pdf' ? 'selected' : '' }}>PDF
                                        </option>
                                        <option value="video"
                                            {{ old('type', $materi->type ?? '') == 'video' ? 'selected' : '' }}>Video
                                        </option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Title -->
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="title">Judul materi</label>
                                <div class="col-sm-10">
                                    <input type="text" id="title" name="title"
                                        class="form-control @error('title') is-invalid @enderror"
                                        value="{{ old('title', $materi->title ?? '') }}" required />
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="description">Deskripsi</label>
                                <div class="col-sm-10">
                                    <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror"
                                        rows="4" placeholder="Tuliskan deskripsi materi...">{{ old('description', $materi->description ?? '') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>


                            <!-- Video Upload (FilePond) -->
                            <div class="row mb-3 video-field" style="display: none;">
                                <label class="col-sm-2 col-form-label">Upload Video</label>
                                <div class="col-sm-10">
                                    <div class="mb-2">
                                        <small class="text-muted">
                                            <i class="bi bi-info-circle"></i>
                                            Mendukung format: MP4, AVI, MOV, WMV. Maksimal ukuran: 2GB
                                        </small>
                                    </div>
                                    <!-- Ganti input filepond dengan ini: -->
                                    <input type="file" class="filepond" id="video-upload" name="video_file"
                                        accept="video/*">
                                    <input type="hidden" name="video_file" id="video_file"
                                        value="{{ old('video_file', $materi->video ?? '') }}">
                                    <div class="upload-progress">
                                        <div class="progress">
                                            <div class="progress-bar" role="progressbar" style="width: 0%;"
                                                id="videoProgress">0%</div>
                                        </div>
                                    </div>


                                    @if (isset($materi) && $materi->video)
                                        <div class="mt-2" id="current-video">
                                            <span class="text-success">
                                                <i class="bi bi-check-circle"></i>
                                                Video saat ini tersimpan
                                            </span>
                                            <br>
                                            <small class="text-muted">{{ basename($materi->video) }}</small>
                                        </div>
                                    @endif

                                    @error('video_file')
                                        <div class="text-danger mt-1">
                                            <small>{{ $message }}</small>
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <!-- File Upload (PDF) -->
                            <div class="row mb-3 file-field" style="display: none;">
                                <label class="col-sm-2 col-form-label">Upload PDF</label>
                                <div class="col-sm-10">
                                    <div class="mb-2">
                                        <small class="text-muted">
                                            <i class="bi bi-info-circle"></i>
                                            Hanya file PDF yang diizinkan. Maksimal ukuran: 100MB
                                        </small>
                                    </div>
                                    <!-- Ganti input PDF dengan accept yang lebih spesifik -->
                                    <input type="file" class="filepond" id="pdf-upload" name="pdf_file"
                                        accept="application/pdf,.pdf">
                                    <input type="hidden" name="pdf_file" id="pdf_file"
                                        value="{{ old('pdf_file', $materi->file_pdf ?? '') }}">
                                    <div class="upload-progress">
                                        <div class="progress">
                                            <div class="progress-bar" role="progressbar" style="width: 0%;"
                                                id="pdfProgress">0%</div>
                                        </div>
                                    </div>

                                    @if (isset($materi) && $materi->file_pdf)
                                        <div class="mt-2" id="current-pdf">
                                            <span class="text-success">
                                                <i class="bi bi-check-circle"></i>
                                                File PDF saat ini tersimpan
                                            </span>
                                            <br>
                                            <small class="text-muted">{{ basename($materi->file_pdf) }}</small>
                                        </div>
                                    @endif

                                    @error('pdf_file')
                                        <div class="text-danger mt-1">
                                            <small>{{ $message }}</small>
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Submit -->
                            <div class="row justify-content-end">
                                <div class="col-sm-10">
                                    <button type="submit" class="btn btn-primary" id="submitBtn">
                                        <span class="spinner-border spinner-border-sm d-none" id="submitSpinner"></span>
                                        {{ isset($materi) ? 'Update' : 'Simpan' }}
                                    </button>
                                    <a href="{{ route('materi.index') }}" class="btn btn-secondary">Kembali</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap5-theme@1.3.2/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
    <!-- FilePond CSS -->
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet" />
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css"
        rel="stylesheet" />
    <link href="https://unpkg.com/filepond-plugin-file-poster/dist/filepond-plugin-file-poster.css" rel="stylesheet" />

    <style>
        .filepond--root {
            margin-bottom: 0;
        }

        .filepond--panel-root {
            background-color: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 8px;
        }

        .filepond--drop-label {
            color: #6c757d;
        }

        .video-field,
        .file-field {
            transition: all 0.3s ease;
        }

        .upload-progress {
            margin-top: 10px;
        }

        .progress {
            height: 20px;
            background-color: #f8f9fa;
        }

        .progress-bar {
            transition: width 0.3s ease;
        }
    </style>
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- FilePond JavaScript -->
    <script src="https://unpkg.com/filepond/dist/filepond.min.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.min.js">
    </script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.min.js">
    </script>
    <script src="https://unpkg.com/filepond-plugin-file-poster/dist/filepond-plugin-file-poster.min.js"></script>

    <script>
        // Register FilePond plugins
        FilePond.registerPlugin(
            FilePondPluginFileValidateSize,
            FilePondPluginFileValidateType
        );

        let filePondInstance = null;
        let pdfPondInstance = null;

        // Fungsi toggle content fields
        function toggleContentFields() {
            const type = $('#type').val();

            if (type === 'video') {
                $('.video-field').show();
                $('.file-field').hide();
                $('#file_url').val('');
                initFilePond();
            } else if (type === 'pdf') {
                $('.video-field').hide();
                $('.file-field').show();
                $('#video_file').val('');
                destroyPdfFilePond();
                initPdfFilePond();
            } else {
                $('.video-field').hide();
                $('.file-field').hide();
                destroyFilePond();
            }
        }

        // Initialize FilePond
        function initFilePond() {
            if (filePondInstance) {
                return; // Already initialized
            }

            const inputElement = document.querySelector('#video-upload');
            if (!inputElement) return;

            filePondInstance = FilePond.create(inputElement, {
                labelIdle: 'Drag & Drop video atau <span class="filepond--label-action">Browse</span>',
                labelInvalidField: 'Field contains invalid files',
                labelFileWaitingForSize: 'Waiting for size',
                labelFileSizeNotAvailable: 'Size not available',
                labelFileLoading: 'Loading',
                labelFileLoadError: 'Error during load',
                labelFileProcessing: 'Uploading',
                labelFileProcessingComplete: 'Upload complete',
                labelFileProcessingAborted: 'Upload cancelled',
                labelFileProcessingError: 'Error during upload',
                labelFileProcessingRevertError: 'Error during revert',
                labelFileRemoveError: 'Error during remove',
                labelTapToCancel: 'tap to cancel',
                labelTapToRetry: 'tap to retry',
                labelTapToUndo: 'tap to undo',
                labelButtonRemoveItem: 'Remove',
                labelButtonAbortItemLoad: 'Abort',
                labelButtonRetryItemLoad: 'Retry',
                labelButtonAbortItemProcessing: 'Cancel',
                labelButtonUndoItemProcessing: 'Undo',
                labelButtonRetryItemProcessing: 'Retry',
                labelButtonProcessItem: 'Upload',

                // Server configuration
                server: {
                    process: {
                        url: '{{ route('materi.upload-video-chunk') }}',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        onload: (response) => {
                            console.log('Upload response received:', response);
                            try {
                                const data = JSON.parse(response);
                                if (data.status && data.path) {
                                    $('#video_file').val(data.path);
                                    return data.path;
                                } else {
                                    throw new Error(data.message || 'Upload failed');
                                }
                            } catch (e) {
                                console.error('Response parsing error:', e);
                                throw new Error('Invalid server response');
                            }
                        },
                        onerror: (response) => {
                            console.error('Upload error:', response);
                            return response;
                        }
                    },
                    revert: {
                        url: '{{ route('materi.delete-video-chunk') }}',
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        onload: (response) => {
                            console.log('Delete response:', response);
                            $('#video_file').val('');
                            return response;
                        }
                    }
                },

                // File validation
                acceptedFileTypes: ['video/mp4', 'video/avi', 'video/mov', 'video/wmv', 'video/mkv'],
                maxFileSize: '2048MB',
                maxFiles: 1,

                // Event handlers
                onprocessfilestart: function(file) {
                    console.log('Upload started for:', file.filename);
                    $('#submitBtn').prop('disabled', true);
                    $('#submitSpinner').removeClass('d-none');
                },

                onprocessfile: function(error, file) {
                    if (error) {
                        console.error('Upload error:', error);
                        showAlert('error', 'Upload gagal: ' + error);
                    } else {
                        console.log('Upload successful for:', file.filename);
                        showAlert('success', 'Video berhasil diupload');
                    }
                    $('#submitBtn').prop('disabled', false);
                    $('#submitSpinner').addClass('d-none');
                },

                onprocessfileprogress: function(file, progress) {
                    const progressBar = document.getElementById('videoProgress');
                    if (progressBar) {
                        const percent = Math.round(progress * 100);
                        progressBar.style.width = percent + '%';
                        progressBar.textContent = percent + '%';
                    }
                },

                onremovefile: function(error, file) {
                    if (!error) {
                        $('#video_file').val('');
                        console.log('File removed:', file.filename || 'unknown');
                        const progressBar = document.getElementById('videoProgress');
                        if (progressBar) {
                            progressBar.style.width = '0%';
                            progressBar.textContent = '0%';
                        }
                    }
                },

                onerror: function(error, file, status) {
                    console.error('FilePond error:', error, status);
                    showAlert('error', 'Terjadi kesalahan: ' + error);
                }
            });


        }

        function initPdfFilePond() {
            if (pdfPondInstance) {
                return; // Already initialized
            }

            const inputElement = document.querySelector('#pdf-upload');
            if (!inputElement) return;

            pdfPondInstance = FilePond.create(inputElement, {
                labelIdle: 'Drag & Drop PDF atau <span class="filepond--label-action">Browse</span>',
                labelInvalidField: 'Field contains invalid files',
                labelFileWaitingForSize: 'Waiting for size',
                labelFileSizeNotAvailable: 'Size not available',
                labelFileLoading: 'Loading',
                labelFileLoadError: 'Error during load',
                labelFileProcessing: 'Uploading',
                labelFileProcessingComplete: 'Upload complete',
                labelFileProcessingAborted: 'Upload cancelled',
                labelFileProcessingError: 'Error during upload',
                labelFileProcessingRevertError: 'Error during revert',
                labelFileRemoveError: 'Error during remove',
                labelTapToCancel: 'tap to cancel',
                labelTapToRetry: 'tap to retry',
                labelTapToUndo: 'tap to undo',
                labelButtonRemoveItem: 'Remove',
                labelButtonAbortItemLoad: 'Abort',
                labelButtonRetryItemLoad: 'Retry',
                labelButtonAbortItemProcessing: 'Cancel',
                labelButtonUndoItemProcessing: 'Undo',
                labelButtonRetryItemProcessing: 'Retry',
                labelButtonProcessItem: 'Upload',

                // Server configuration
                server: {
                    process: {
                        url: '{{ route('materi.upload-pdf-chunk') }}',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        onload: (response) => {
                            console.log('PDF upload response received:', response);
                            try {
                                const data = JSON.parse(response);
                                if (data.status && data.path) {
                                    $('#pdf_file').val(data.path);
                                    return data.path;
                                } else {
                                    throw new Error(data.message || 'Upload failed');
                                }
                            } catch (e) {
                                console.error('Response parsing error:', e);
                                throw new Error('Invalid server response');
                            }
                        },
                        onerror: (response) => {
                            console.error('PDF upload error:', response);
                            return response;
                        }
                    },
                    revert: {
                        url: '{{ route('materi.delete-pdf-chunk') }}',
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        onload: (response) => {
                            console.log('PDF delete response:', response);
                            $('#pdf_file').val('');
                            return response;
                        }
                    }
                },

                // File validation
                acceptedFileTypes: ['application/pdf', '.pdf'],
                fileValidateTypeLabelExpectedTypes: 'Expects .pdf',
                maxFileSize: '100MB',
                maxFiles: 1,

                // Event handlers
                onprocessfilestart: function(file) {
                    console.log('PDF upload started for:', file.filename);
                    $('#submitBtn').prop('disabled', true);
                    $('#submitSpinner').removeClass('d-none');
                },

                onprocessfile: function(error, file) {
                    if (error) {
                        console.error('PDF upload error:', error);
                        showAlert('error', 'Upload PDF gagal: ' + error);
                    } else {
                        console.log('PDF upload successful for:', file.filename);
                        showAlert('success', 'PDF berhasil diupload');
                    }
                    $('#submitBtn').prop('disabled', false);
                    $('#submitSpinner').addClass('d-none');
                },

                onprocessfileprogress: function(file, progress) {
                    const progressBar = document.getElementById('pdfProgress');
                    if (progressBar) {
                        const percent = Math.round(progress * 100);
                        progressBar.style.width = percent + '%';
                        progressBar.textContent = percent + '%';
                    }
                },

                onremovefile: function(error, file) {
                    if (!error) {
                        $('#pdf_file').val('');
                        console.log('PDF file removed:', file.filename || 'unknown');
                        const progressBar = document.getElementById('pdfProgress');
                        if (progressBar) {
                            progressBar.style.width = '0%';
                            progressBar.textContent = '0%';
                        }
                    }
                },

                onerror: function(error, file, status) {
                    console.error('PDF FilePond error:', error, status);
                    showAlert('error', 'Terjadi kesalahan: ' + error);
                }
            });
        }
        // Destroy FilePond instance
        function destroyFilePond() {
            if (filePondInstance) {
                try {
                    filePondInstance.destroy();
                    filePondInstance = null;
                    console.log('FilePond instance destroyed');
                } catch (error) {
                    console.error('Error destroying FilePond:', error);
                }
            }
        }

        function destroyPdfFilePond() {
            if (pdfPondInstance) {
                try {
                    pdfPondInstance.destroy();
                    pdfPondInstance = null;
                    console.log('PDF FilePond instance destroyed');
                } catch (error) {
                    console.error('Error destroying PDF FilePond:', error);
                }
            }
        }

        // Show alert messages
        function showAlert(type, message) {
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;

            // Remove existing alerts
            $('.alert').remove();

            // Add new alert
            $('.card-body').prepend(alertHtml);

            // Auto hide after 5 seconds
            setTimeout(function() {
                $('.alert').fadeOut();
            }, 5000);
        }

        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                theme: 'bootstrap-5',
                placeholder: '-- Pilih Opsi --',
                allowClear: true,
                width: '100%'
            });

            // Event handler untuk select tipe materi
            $('#type').change(function() {
                toggleContentFields();
            });

            // Inisialisasi awal
            toggleContentFields();

            // Jika create form (bukan edit), default ke video
            @if (!isset($materi))
                $('#type').val('video').trigger('change');
            @endif

            // Form validation before submit
            $('#materiForm').on('submit', function(e) {
                const type = $('#type').val();
                const videoFile = $('#video_file').val();
                const pdfFile = $('#pdf_file').val();

                if (type === 'video' && !videoFile && !@json(isset($materi) && $materi->video)) {
                    e.preventDefault();
                    showAlert('error', 'Silakan upload video terlebih dahulu');
                    return false;
                }

                if (type === 'pdf' && !pdfFile && !@json(isset($materi) && $materi->file_pdf)) {
                    e.preventDefault();
                    showAlert('error', 'Silakan pilih file PDF terlebih dahulu');
                    return false;
                }

                // Show loading state
                $('#submitBtn').prop('disabled', true);
                $('#submitSpinner').removeClass('d-none');
            });


            // Auto-hide alerts on page load errors
            @if ($errors->any())
                setTimeout(function() {
                    $('.alert').fadeOut();
                }, 10000);
            @endif
        });

        // Cleanup on page unload
        $(window).on('beforeunload', function() {
            destroyFilePond();
        });
    </script>
@endpush
