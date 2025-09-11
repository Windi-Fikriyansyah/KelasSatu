@extends('template.app')
@section('title', 'Pengaturan Landing Page')
@section('content')
    <div aria-live="polite" aria-atomic="true" class="position-relative">
        <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999">
            @if (session('success'))
                <div class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive"
                    aria-atomic="true" id="successToast">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                            aria-label="Close"></button>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive"
                    aria-atomic="true" id="errorToast">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                            aria-label="Close"></button>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive"
                    aria-atomic="true" id="validationToast">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Terdapat kesalahan dalam pengisian form. Silakan periksa kembali.
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                            aria-label="Close"></button>
                    </div>
                </div>
            @endif
        </div>
    </div>
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-xxl">
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">{{ isset($landing) ? 'Edit' : 'Tambah' }} Pengaturan Landing Page</h5>
                        <small class="text-muted float-end">Kelola konten halaman utama</small>
                    </div>
                    <div class="card-body">
                        <form
                            action="{{ isset($landing) ? route('landing.update', $landing->id) : route('landing.store') }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            @if (isset($landing))
                                @method('PUT')
                            @endif

                            <!-- Nav tabs -->
                            <ul class="nav nav-tabs" id="landingTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="hero-tab" data-bs-toggle="tab"
                                        data-bs-target="#hero" type="button" role="tab">Hero Section</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="about-tab" data-bs-toggle="tab" data-bs-target="#about"
                                        type="button" role="tab">Tentang</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="features-tab" data-bs-toggle="tab"
                                        data-bs-target="#features" type="button" role="tab">Mengapa Memilih</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="stats-tab" data-bs-toggle="tab" data-bs-target="#stats"
                                        type="button" role="tab">Statistik</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="testimonial-tab" data-bs-toggle="tab"
                                        data-bs-target="#testimonial" type="button" role="tab">Testimoni</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="faq-tab" data-bs-toggle="tab" data-bs-target="#faq"
                                        type="button" role="tab">FAQ</button>
                                </li>
                            </ul>

                            <!-- Tab panes -->
                            <div class="tab-content" id="landingTabsContent">

                                <!-- Hero Section Tab -->
                                <div class="tab-pane fade show active" id="hero" role="tabpanel">
                                    <div class="mt-4">
                                        <h6 class="mb-3 text-primary">Hero Section</h6>

                                        <div class="row mb-3">
                                            <label class="col-sm-2 col-form-label">Judul Utama</label>
                                            <div class="col-sm-10">
                                                <input type="text" name="hero_title"
                                                    class="form-control @error('hero_title') is-invalid @enderror"
                                                    value="{{ old('hero_title', $landing->hero_title ?? 'Belajar Tanpa Batas') }}" />
                                                @error('hero_title')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-sm-2 col-form-label">Subjudul</label>
                                            <div class="col-sm-10">
                                                <input type="text" name="hero_subtitle"
                                                    class="form-control @error('hero_subtitle') is-invalid @enderror"
                                                    value="{{ old('hero_subtitle', $landing->hero_subtitle ?? 'Raih Masa Depan') }}" />
                                                @error('hero_subtitle')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-sm-2 col-form-label">Deskripsi</label>
                                            <div class="col-sm-10">
                                                <textarea name="hero_description" rows="4"
                                                    class="form-control @error('hero_description') is-invalid @enderror">{{ old('hero_description', $landing->hero_description ?? 'Nikmati Kemudahan lolos TES KEMAMPUAN AKADEMIK (TKA) melalui Kelas Premium Prediksi TKA 2025...') }}</textarea>
                                                @error('hero_description')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-sm-2 col-form-label">Teks Tombol Utama</label>
                                            <div class="col-sm-10">
                                                <input type="text" name="hero_btn_primary"
                                                    class="form-control @error('hero_btn_primary') is-invalid @enderror"
                                                    value="{{ old('hero_btn_primary', $landing->hero_btn_primary ?? 'Mulai Belajar Sekarang') }}" />
                                                @error('hero_btn_primary')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-sm-2 col-form-label">Teks Tombol Kedua</label>
                                            <div class="col-sm-10">
                                                <input type="text" name="hero_btn_secondary"
                                                    class="form-control @error('hero_btn_secondary') is-invalid @enderror"
                                                    value="{{ old('hero_btn_secondary', $landing->hero_btn_secondary ?? 'Jelajahi Kursus') }}" />
                                                @error('hero_btn_secondary')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Hero Images -->
                                        <h6 class="mb-3 text-primary mt-4">Gambar Slider</h6>

                                        <div class="row mb-3">
                                            <label class="col-sm-2 col-form-label">Gambar 1</label>
                                            <div class="col-sm-10">
                                                <input type="file" name="hero_image_1"
                                                    class="form-control image-upload @error('hero_image_1') is-invalid @enderror"
                                                    accept="image/*" data-preview="hero_image_1_preview" />
                                                <div class="image-preview mt-2" id="hero_image_1_preview">
                                                    @if (isset($landing) && $landing->hero_image_1)
                                                        <img src="{{ asset('storage/' . $landing->hero_image_1) }}"
                                                            class="img-thumbnail" style="max-height: 150px;">
                                                        <small class="text-muted d-block">Current:
                                                            {{ basename($landing->hero_image_1) }}</small>
                                                    @endif
                                                </div>
                                                @error('hero_image_1')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-sm-2 col-form-label">Gambar 2</label>
                                            <div class="col-sm-10">
                                                <input type="file" name="hero_image_2"
                                                    class="form-control image-upload @error('hero_image_2') is-invalid @enderror"
                                                    accept="image/*" data-preview="hero_image_2_preview" />
                                                <div class="image-preview mt-2" id="hero_image_2_preview">
                                                    @if (isset($landing) && $landing->hero_image_2)
                                                        <img src="{{ asset('storage/' . $landing->hero_image_2) }}"
                                                            class="img-thumbnail" style="max-height: 150px;">
                                                        <small class="text-muted d-block">Current:
                                                            {{ basename($landing->hero_image_2) }}</small>
                                                    @endif
                                                </div>
                                                @error('hero_image_2')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-sm-2 col-form-label">Gambar 3</label>
                                            <div class="col-sm-10">
                                                <input type="file" name="hero_image_3"
                                                    class="form-control image-upload @error('hero_image_3') is-invalid @enderror"
                                                    accept="image/*" data-preview="hero_image_3_preview" />
                                                <div class="image-preview mt-2" id="hero_image_3_preview">
                                                    @if (isset($landing) && $landing->hero_image_3)
                                                        <img src="{{ asset('storage/' . $landing->hero_image_3) }}"
                                                            class="img-thumbnail" style="max-height: 150px;">
                                                        <small class="text-muted d-block">Current:
                                                            {{ basename($landing->hero_image_3) }}</small>
                                                    @endif
                                                </div>
                                                @error('hero_image_3')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- About Section Tab -->
                                <div class="tab-pane fade" id="about" role="tabpanel">
                                    <div class="mt-4">
                                        <h6 class="mb-3 text-primary">Tentang Kami</h6>

                                        <div class="row mb-3">
                                            <label class="col-sm-2 col-form-label">Judul</label>
                                            <div class="col-sm-10">
                                                <input type="text" name="about_title"
                                                    class="form-control @error('about_title') is-invalid @enderror"
                                                    value="{{ old('about_title', $landing->about_title ?? 'Selamat Datang di Kelassatu.com') }}" />
                                                @error('about_title')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-sm-2 col-form-label">Paragraf 1</label>
                                            <div class="col-sm-10">
                                                <textarea name="about_paragraph_1" rows="4"
                                                    class="form-control @error('about_paragraph_1') is-invalid @enderror">{{ old('about_paragraph_1', $landing->about_paragraph_1 ?? 'kelassatu.com adalah platform pembelajaran daring...') }}</textarea>
                                                @error('about_paragraph_1')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-sm-2 col-form-label">Paragraf 2</label>
                                            <div class="col-sm-10">
                                                <textarea name="about_paragraph_2" rows="4"
                                                    class="form-control @error('about_paragraph_2') is-invalid @enderror">{{ old('about_paragraph_2', $landing->about_paragraph_2 ?? 'Di sini, setiap orang memiliki kesempatan...') }}</textarea>
                                                @error('about_paragraph_2')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-sm-2 col-form-label">Logo/Gambar</label>
                                            <div class="col-sm-10">
                                                <input type="file" name="about_image"
                                                    class="form-control image-upload @error('about_image') is-invalid @enderror"
                                                    accept="image/*" data-preview="about_image_preview" />
                                                <div class="image-preview mt-2" id="about_image_preview">
                                                    @if (isset($landing) && $landing->about_image)
                                                        <img src="{{ asset('storage/' . $landing->about_image) }}"
                                                            class="img-thumbnail" style="max-height: 150px;">
                                                        <small class="text-muted d-block">Current:
                                                            {{ basename($landing->about_image) }}</small>
                                                    @endif
                                                </div>
                                                @error('about_image')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Features Section Tab -->
                                <div class="tab-pane fade" id="features" role="tabpanel">
                                    <div class="mt-4">
                                        <h6 class="mb-3 text-primary">Mengapa Memilih KelasSatu</h6>

                                        <div class="row mb-3">
                                            <label class="col-sm-2 col-form-label">Judul Utama</label>
                                            <div class="col-sm-10">
                                                <input type="text" name="features_title"
                                                    class="form-control @error('features_title') is-invalid @enderror"
                                                    value="{{ old('features_title', $landing->features_title ?? 'Mengapa Memilih KelasSatu?') }}" />
                                                @error('features_title')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-sm-2 col-form-label">Subjudul</label>
                                            <div class="col-sm-10">
                                                <input type="text" name="features_subtitle"
                                                    class="form-control @error('features_subtitle') is-invalid @enderror"
                                                    value="{{ old('features_subtitle', $landing->features_subtitle ?? 'Kami menyediakan pengalaman belajar terbaik...') }}" />
                                                @error('features_subtitle')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Dynamic Feature Items -->
                                        <div id="feature-container">
                                            @php
                                                $features =
                                                    isset($landing) && isset($landing->features)
                                                        ? $landing->features
                                                        : collect([
                                                            (object) [
                                                                'title' => 'Materi TKA Sesuai Kisi-Kisi Terbaru 2025',
                                                                'description' =>
                                                                    'Belajar tanpa ragu karena semua materi...',
                                                            ],
                                                            (object) [
                                                                'title' => 'Latihan Soal & Pembahasan Mendalam',
                                                                'description' =>
                                                                    'Tidak hanya banyak, tapi soal-soal...',
                                                            ],
                                                        ]);
                                            @endphp

                                            @foreach ($features as $index => $feature)
                                                <div class="feature-item card mb-3">
                                                    <div
                                                        class="card-header d-flex justify-content-between align-items-center">
                                                        <h6 class="mb-0 text-secondary">Fitur {{ $index + 1 }}</h6>
                                                        <button type="button"
                                                            class="btn btn-sm btn-danger remove-feature"
                                                            @if ($index < 2) disabled @endif>
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                    <div class="card-body">
                                                        <input type="hidden" name="features[{{ $index }}][id]"
                                                            value="{{ $feature->id ?? '' }}">
                                                        <div class="mb-3">
                                                            <label class="form-label">Judul</label>
                                                            <input type="text"
                                                                name="features[{{ $index }}][title]"
                                                                class="form-control"
                                                                value="{{ old('features.' . $index . '.title', $feature->title) }}" />
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Deskripsi</label>
                                                            <textarea name="features[{{ $index }}][description]" rows="3" class="form-control">{{ old('features.' . $index . '.description', $feature->description) }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        <div class="row mt-3">
                                            <div class="col-12">
                                                <button type="button" id="add-feature" class="btn btn-success">
                                                    <i class="fas fa-plus"></i> Tambah Fitur
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Stats Section Tab -->
                                <div class="tab-pane fade" id="stats" role="tabpanel">
                                    <div class="mt-4">
                                        <h6 class="mb-3 text-primary">Statistik</h6>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Siswa Aktif (Angka)</label>
                                                    <input type="text" name="stats_students_count"
                                                        class="form-control @error('stats_students_count') is-invalid @enderror"
                                                        value="{{ old('stats_students_count', $landing->stats_students_count ?? '50,000+') }}" />
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Siswa Aktif (Label)</label>
                                                    <input type="text" name="stats_students_label"
                                                        class="form-control @error('stats_students_label') is-invalid @enderror"
                                                        value="{{ old('stats_students_label', $landing->stats_students_label ?? 'Siswa Aktif') }}" />
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Kursus (Angka)</label>
                                                    <input type="text" name="stats_courses_count"
                                                        class="form-control @error('stats_courses_count') is-invalid @enderror"
                                                        value="{{ old('stats_courses_count', $landing->stats_courses_count ?? '200+') }}" />
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Kursus (Label)</label>
                                                    <input type="text" name="stats_courses_label"
                                                        class="form-control @error('stats_courses_label') is-invalid @enderror"
                                                        value="{{ old('stats_courses_label', $landing->stats_courses_label ?? 'Kursus Tersedia') }}" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Tingkat Kepuasan (Angka)</label>
                                                    <input type="text" name="stats_satisfaction_count"
                                                        class="form-control @error('stats_satisfaction_count') is-invalid @enderror"
                                                        value="{{ old('stats_satisfaction_count', $landing->stats_satisfaction_count ?? '95%') }}" />
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Tingkat Kepuasan (Label)</label>
                                                    <input type="text" name="stats_satisfaction_label"
                                                        class="form-control @error('stats_satisfaction_label') is-invalid @enderror"
                                                        value="{{ old('stats_satisfaction_label', $landing->stats_satisfaction_label ?? 'Tingkat Kepuasan') }}" />
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Dukungan (Angka)</label>
                                                    <input type="text" name="stats_support_count"
                                                        class="form-control @error('stats_support_count') is-invalid @enderror"
                                                        value="{{ old('stats_support_count', $landing->stats_support_count ?? '24/7') }}" />
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Dukungan (Label)</label>
                                                    <input type="text" name="stats_support_label"
                                                        class="form-control @error('stats_support_label') is-invalid @enderror"
                                                        value="{{ old('stats_support_label', $landing->stats_support_label ?? 'Dukungan') }}" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Testimonial Section Tab -->
                                <div class="tab-pane fade" id="testimonial" role="tabpanel">
                                    <div class="mt-4">
                                        <h6 class="mb-3 text-primary">Testimoni</h6>

                                        <div class="row mb-3">
                                            <label class="col-sm-2 col-form-label">Judul</label>
                                            <div class="col-sm-10">
                                                <input type="text" name="testimonial_title"
                                                    class="form-control @error('testimonial_title') is-invalid @enderror"
                                                    value="{{ old('testimonial_title', $landing->testimonial_title ?? 'Apa Kata Mereka?') }}" />
                                                @error('testimonial_title')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-sm-2 col-form-label">Subjudul</label>
                                            <div class="col-sm-10">
                                                <input type="text" name="testimonial_subtitle"
                                                    class="form-control @error('testimonial_subtitle') is-invalid @enderror"
                                                    value="{{ old('testimonial_subtitle', $landing->testimonial_subtitle ?? 'Testimoni dari siswa yang telah merasakan...') }}" />
                                                @error('testimonial_subtitle')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Dynamic Testimonial Items -->
                                        <div id="testimonial-container">
                                            @php
                                                $testimonials =
                                                    isset($landing) && isset($landing->testimonials)
                                                        ? $landing->testimonials
                                                        : collect([
                                                            (object) [
                                                                'name' => 'Ahmad Rizki',
                                                                'role' => 'Peserta kelassatu.com 2025',
                                                                'content' => 'Awalnya saya merasa TKA itu sulit...',
                                                            ],
                                                            (object) [
                                                                'name' => 'Budi Santoso',
                                                                'role' => 'Peserta kelassatu.com 2025',
                                                                'content' => 'Sebelum ikut kursus di kelassatu.com...',
                                                            ],
                                                        ]);
                                            @endphp

                                            @foreach ($testimonials as $index => $testimonial)
                                                <div class="testimonial-item card mb-3">
                                                    <div
                                                        class="card-header d-flex justify-content-between align-items-center">
                                                        <h6 class="mb-0 text-secondary">Testimoni {{ $index + 1 }}</h6>
                                                        <button type="button"
                                                            class="btn btn-sm btn-danger remove-testimonial"
                                                            @if ($index < 2) disabled @endif>
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Nama</label>
                                                                    <input type="text"
                                                                        name="testimonials[{{ $index }}][name]"
                                                                        class="form-control"
                                                                        value="{{ old('testimonials.' . $index . '.name', $testimonial->name) }}" />
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Keterangan</label>
                                                                    <input type="text"
                                                                        name="testimonials[{{ $index }}][role]"
                                                                        class="form-control"
                                                                        value="{{ old('testimonials.' . $index . '.role', $testimonial->role) }}" />
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Testimoni</label>
                                                            <textarea name="testimonials[{{ $index }}][content]" rows="4" class="form-control">{{ old('testimonials.' . $index . '.content', $testimonial->content) }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        <div class="row mt-3">
                                            <div class="col-12">
                                                <button type="button" id="add-testimonial" class="btn btn-success">
                                                    <i class="fas fa-plus"></i> Tambah Testimoni
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- FAQ Section Tab -->
                                <div class="tab-pane fade" id="faq" role="tabpanel">
                                    <div class="mt-4">
                                        <h6 class="mb-3 text-primary">FAQ (Frequently Asked Questions)</h6>

                                        <div class="row mb-3">
                                            <label class="col-sm-2 col-form-label">Judul</label>
                                            <div class="col-sm-10">
                                                <input type="text" name="faq_title"
                                                    class="form-control @error('faq_title') is-invalid @enderror"
                                                    value="{{ old('faq_title', $landing->faq_title ?? 'Pertanyaan yang Sering Diajukan') }}" />
                                                @error('faq_title')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-sm-2 col-form-label">Subjudul</label>
                                            <div class="col-sm-10">
                                                <input type="text" name="faq_subtitle"
                                                    class="form-control @error('faq_subtitle') is-invalid @enderror"
                                                    value="{{ old('faq_subtitle', $landing->faq_subtitle ?? 'Temukan jawaban dari pertanyaan umum mengenai KelasSatu') }}" />
                                                @error('faq_subtitle')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Dynamic FAQ Items -->
                                        <div id="faq-container">
                                            @php
                                                $faqs =
                                                    isset($landing) && isset($landing->faqs)
                                                        ? $landing->faqs
                                                        : collect([
                                                            (object) [
                                                                'question' => 'Apa itu KelasSatu?',
                                                                'answer' => 'KelasSatu adalah platform e-learning...',
                                                            ],
                                                            (object) [
                                                                'question' =>
                                                                    'Apakah saya mendapatkan sertifikat setelah menyelesaikan kursus?',
                                                                'answer' => 'Ya, setiap kursus yang diselesaikan...',
                                                            ],
                                                            (object) [
                                                                'question' =>
                                                                    'Apakah saya bisa mengakses kursus seumur hidup?',
                                                                'answer' => 'Ya, setelah membeli kursus...',
                                                            ],
                                                            (object) [
                                                                'question' => 'Bagaimana cara mendaftar kursus?',
                                                                'answer' =>
                                                                    'Anda dapat mendaftar dengan membuat akun...',
                                                            ],
                                                        ]);
                                            @endphp

                                            @foreach ($faqs as $index => $faq)
                                                <div class="faq-item card mb-3">
                                                    <div
                                                        class="card-header d-flex justify-content-between align-items-center">
                                                        <h6 class="mb-0 text-secondary">FAQ {{ $index + 1 }}</h6>
                                                        <button type="button" class="btn btn-sm btn-danger remove-faq"
                                                            @if ($index < 2) disabled @endif>
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="mb-3">
                                                            <label class="form-label">Pertanyaan</label>
                                                            <input type="text"
                                                                name="faqs[{{ $index }}][question]"
                                                                class="form-control"
                                                                value="{{ old('faqs.' . $index . '.question', $faq->question) }}" />
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Jawaban</label>
                                                            <textarea name="faqs[{{ $index }}][answer]" rows="3" class="form-control">{{ old('faqs.' . $index . '.answer', $faq->answer) }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        <div class="row mt-3">
                                            <div class="col-12">
                                                <button type="button" id="add-faq" class="btn btn-success">
                                                    <i class="fas fa-plus"></i> Tambah FAQ
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="row justify-content-end mt-4">
                                <div class="col-sm-10">
                                    <button type="submit" class="btn btn-primary">
                                        {{ isset($landing) ? 'Update' : 'Simpan' }} Pengaturan
                                    </button>
                                    <a href="{{ route('landing.index') }}" class="btn btn-secondary">Kembali</a>
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .right-gap {
            margin-right: 10px
        }

        .select2-container--default .select2-selection--single {
            height: 38px;
            padding-top: 4px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }

        .nav-tabs .nav-link {
            font-weight: 500;
        }

        .nav-tabs .nav-link.active {
            background-color: #007bff;
            border-color: #007bff;
            color: white;
        }

        .tab-content {
            border: 1px solid #dee2e6;
            border-top: 0;
            border-radius: 0 0 0.375rem 0.375rem;
            padding: 1rem;
            background-color: #fff;
        }

        .text-primary {
            color: #007bff !important;
        }

        .text-secondary {
            color: #6c757d !important;
        }

        .testimonial-item {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
        }

        .testimonial-item .card-header {
            background-color: #f8f9fa;
        }

        .faq-item {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
        }

        .faq-item .card-header {
            background-color: #f8f9fa;
        }

        .feature-item {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
        }

        .feature-item .card-header {
            background-color: #f8f9fa;
        }

        .image-preview {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .image-preview img {
            max-height: 150px;
            margin-bottom: 5px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }

        .remove-image {
            margin-top: 5px;
        }

        .toast {
            min-width: 300px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .toast-body {
            padding: 0.75rem;
        }
    </style>
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2();

            // Image preview functionality
            function readURL(input, previewId) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function(e) {
                        $('#' + previewId).html(
                            '<img src="' + e.target.result +
                            '" class="img-thumbnail" style="max-height: 150px;">' +
                            '<button type="button" class="btn btn-sm btn-danger remove-preview mt-2" data-preview="' +
                            previewId + '">' +
                            '<i class="fas fa-times"></i> Hapus Preview' +
                            '</button>'
                        );
                    }

                    reader.readAsDataURL(input.files[0]);
                }
            }

            // Handle image upload and preview
            $('.image-upload').change(function() {
                var previewId = $(this).data('preview');
                readURL(this, previewId);
            });

            // Remove preview image
            $(document).on('click', '.remove-preview', function() {
                var previewId = $(this).data('preview');
                $('#' + previewId).html('');
                $('input[data-preview="' + previewId + '"]').val('');
            });

            // Tab navigation with validation
            $('.nav-tabs button').click(function() {
                var targetTab = $(this).data('bs-target');
                // Optional: Add validation before switching tabs
            });

            // File input preview (optional enhancement)
            $('input[type="file"]').change(function() {
                var input = this;
                var label = $(input).siblings('small');

                if (input.files && input.files[0]) {
                    var fileName = input.files[0].name;
                    if (label.length) {
                        label.text('Selected: ' + fileName);
                    }
                }
            });

            // Dynamic Testimonial Management
            let testimonialCount =
                {{ isset($landing) && isset($landing->testimonials) ? count($landing->testimonials) : 2 }};

            // Add new testimonial
            $('#add-testimonial').click(function() {
                const newIndex = testimonialCount++;
                const newTestimonial = `
                <div class="testimonial-item card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 text-secondary">Testimoni ${newIndex + 1}</h6>
                        <button type="button" class="btn btn-sm btn-danger remove-testimonial">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nama</label>
                                    <input type="text" name="testimonials[${newIndex}][name]"
                                        class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Keterangan</label>
                                    <input type="text" name="testimonials[${newIndex}][role]"
                                        class="form-control" />
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Testimoni</label>
                            <textarea name="testimonials[${newIndex}][content]" rows="4"
                                class="form-control"></textarea>
                        </div>
                    </div>
                </div>
            `;

                $('#testimonial-container').append(newTestimonial);

                // Enable remove buttons if we have more than 2 testimonials
                if (testimonialCount > 2) {
                    $('.remove-testimonial').prop('disabled', false);
                }
            });

            // Remove testimonial
            $(document).on('click', '.remove-testimonial', function() {
                if (testimonialCount > 2) {
                    $(this).closest('.testimonial-item').remove();
                    testimonialCount--;

                    // Renumber testimonials
                    $('.testimonial-item').each(function(index) {
                        $(this).find('.card-header h6').text(`Testimoni ${index + 1}`);

                        // Update input names
                        $(this).find('input, textarea').each(function() {
                            const name = $(this).attr('name');
                            if (name) {
                                const newName = name.replace(/testimonials\[\d+\]/,
                                    `testimonials[${index}]`);
                                $(this).attr('name', newName);
                            }
                        });
                    });

                    // Disable remove button if we have only 2 testimonials
                    if (testimonialCount === 2) {
                        $('.remove-testimonial').prop('disabled', true);
                    }
                }
            });


            // Dynamic FAQ Management
            let faqCount = {{ isset($landing) && isset($landing->faqs) ? count($landing->faqs) : 4 }};

            // Add new FAQ
            $('#add-faq').click(function() {
                const newIndex = faqCount++;
                const newFAQ = `
    <div class="faq-item card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0 text-secondary">FAQ ${newIndex + 1}</h6>
            <button type="button" class="btn btn-sm btn-danger remove-faq">
                <i class="fas fa-trash"></i>
            </button>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">Pertanyaan</label>
                <input type="text" name="faqs[${newIndex}][question]"
                    class="form-control" />
            </div>
            <div class="mb-3">
                <label class="form-label">Jawaban</label>
                <textarea name="faqs[${newIndex}][answer]" rows="3"
                    class="form-control"></textarea>
            </div>
        </div>
    </div>
    `;

                $('#faq-container').append(newFAQ);

                // Enable remove buttons if we have more than 2 FAQs
                if (faqCount > 2) {
                    $('.remove-faq').prop('disabled', false);
                }
            });

            // Remove FAQ
            $(document).on('click', '.remove-faq', function() {
                if (faqCount > 2) {
                    $(this).closest('.faq-item').remove();
                    faqCount--;

                    // Renumber FAQs
                    $('.faq-item').each(function(index) {
                        $(this).find('.card-header h6').text(`FAQ ${index + 1}`);

                        // Update input names
                        $(this).find('input, textarea').each(function() {
                            const name = $(this).attr('name');
                            if (name) {
                                const newName = name.replace(/faqs\[\d+\]/,
                                    `faqs[${index}]`);
                                $(this).attr('name', newName);
                            }
                        });
                    });

                    // Disable remove button if we have only 2 FAQs
                    if (faqCount === 2) {
                        $('.remove-faq').prop('disabled', true);
                    }
                }
            });


            // Dynamic Feature Management
            let featureCount = {{ isset($landing) && isset($landing->features) ? count($landing->features) : 2 }};

            // Add new feature
            $('#add-feature').click(function() {
                const newIndex = featureCount++;
                const newFeature = `
    <div class="feature-item card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0 text-secondary">Fitur ${newIndex + 1}</h6>
            <button type="button" class="btn btn-sm btn-danger remove-feature">
                <i class="fas fa-trash"></i>
            </button>
        </div>
        <div class="card-body">
            <input type="hidden" name="features[${newIndex}][id]" value="">
            <div class="mb-3">
                <label class="form-label">Judul</label>
                <input type="text" name="features[${newIndex}][title]"
                    class="form-control" />
            </div>
            <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea name="features[${newIndex}][description]" rows="3"
                    class="form-control"></textarea>
            </div>
        </div>
    </div>
    `;

                $('#feature-container').append(newFeature);

                // Enable remove buttons if we have more than 2 features
                if (featureCount > 2) {
                    $('.remove-feature').prop('disabled', false);
                }
            });

            // Remove feature
            $(document).on('click', '.remove-feature', function() {
                if (featureCount > 2) {
                    $(this).closest('.feature-item').remove();
                    featureCount--;

                    // Renumber features
                    $('.feature-item').each(function(index) {
                        $(this).find('.card-header h6').text(`Fitur ${index + 1}`);

                        // Update input names
                        $(this).find('input, textarea').each(function() {
                            const name = $(this).attr('name');
                            if (name) {
                                const newName = name.replace(/features\[\d+\]/,
                                    `features[${index}]`);
                                $(this).attr('name', newName);
                            }
                        });
                    });

                    // Disable remove button if we have only 2 features
                    if (featureCount === 2) {
                        $('.remove-feature').prop('disabled', true);
                    }
                }
            });
        });
        @if (session('success'))
            var successToast = new bootstrap.Toast(document.getElementById('successToast'));
            successToast.show();
        @endif

        @if (session('error'))
            var errorToast = new bootstrap.Toast(document.getElementById('errorToast'));
            errorToast.show();
        @endif

        @if ($errors->any())
            var validationToast = new bootstrap.Toast(document.getElementById('validationToast'));
            validationToast.show();
        @endif
    </script>
@endpush
