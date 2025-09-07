@extends('template.app')
@section('title', isset($course) ? 'Edit Course' : 'Tambah Course')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="row">
            <div class="col-xxl">
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">{{ isset($course) ? 'Edit' : 'Tambah' }} Course</h5>
                        <small class="text-muted float-end">Form Course</small>
                    </div>
                    <div class="card-body">
                        <form action="{{ isset($course) ? route('kursus.update', $course->id) : route('kursus.store') }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            @if (isset($course))
                                @method('PUT')
                            @endif

                            <!-- Title -->
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="title">Judul Course</label>
                                <div class="col-sm-10">
                                    <input type="text" id="title" name="title"
                                        class="form-control @error('title') is-invalid @enderror"
                                        value="{{ old('title', $course->title ?? '') }}" required />
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="id_kategori">Kategori</label>
                                <div class="col-sm-10">
                                    <select id="id_kategori" name="id_kategori"
                                        class="form-control select2 @error('id_kategori') is-invalid @enderror" required>
                                        <option value="">-- Pilih Kategori --</option>
                                        @foreach ($kategoris as $kategori)
                                            <option value="{{ $kategori->id }}"
                                                {{ old('id_kategori', $course->id_kategori ?? '') == $kategori->id ? 'selected' : '' }}>
                                                {{ $kategori->nama_kategori }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_kategori')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="description">Deskripsi</label>
                                <div class="col-sm-10">
                                    <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror"
                                        rows="4" required>{{ old('description', $course->description ?? '') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Thumbnail -->
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="thumbnail">Thumbnail</label>
                                <div class="col-sm-10">
                                    <input type="file" id="thumbnail" name="thumbnail"
                                        class="form-control @error('thumbnail') is-invalid @enderror"
                                        {{ isset($course) ? '' : 'required' }} accept="image/*">

                                    @if (isset($course) && $course->thumbnail)
                                        <div class="mt-2">
                                            <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="Thumbnail"
                                                class="img-thumbnail" style="max-height: 150px;">
                                            <small class="d-block text-muted">Thumbnail saat ini</small>
                                        </div>
                                    @endif

                                    <div id="thumbnail-preview" class="mt-2 d-none">
                                        <img id="preview-image" src="#" alt="Preview" class="img-thumbnail"
                                            style="max-height: 150px;">
                                        <small class="d-block text-muted">Preview thumbnail baru</small>
                                    </div>

                                    @error('thumbnail')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Price -->
                            <!-- Price -->
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="price">Harga</label>
                                <div class="col-sm-10">
                                    <input type="text" id="price_display" name="price_display"
                                        class="form-control @error('price') is-invalid @enderror"
                                        value="{{ old('price_display', isset($course->price) ? 'Rp ' . number_format($course->price, 0, ',', '.') : '') }}"
                                        required />
                                    <input type="hidden" id="price" name="price"
                                        value="{{ old('price', $course->price ?? 0) }}">
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <!-- Is Free -->
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="is_free">Gratis?</label>
                                <div class="col-sm-10">
                                    <select id="is_free" name="is_free"
                                        class="form-control @error('is_free') is-invalid @enderror" required>
                                        <option value="">-- Pilih --</option>
                                        <option value="1"
                                            {{ old('is_free', $course->is_free ?? '') == 1 ? 'selected' : '' }}>Ya</option>
                                        <option value="0"
                                            {{ old('is_free', $course->is_free ?? '') == 0 ? 'selected' : '' }}>Tidak
                                        </option>
                                    </select>
                                    @error('is_free')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="status">Status</label>
                                <div class="col-sm-10">
                                    <select id="status" name="status"
                                        class="form-control @error('status') is-invalid @enderror" required>
                                        <option value="">-- Pilih Status --</option>
                                        <option value="active"
                                            {{ old('status', $course->status ?? '') == 'active' ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="inactive"
                                            {{ old('status', $course->status ?? '') == 'inactive' ? 'selected' : '' }}>
                                            Inactive</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Access Type -->
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="access_type">Tipe Akses</label>
                                <div class="col-sm-10">
                                    <select id="access_type" name="access_type"
                                        class="form-control @error('access_type') is-invalid @enderror" required>
                                        <option value="">-- Pilih Akses --</option>
                                        <option value="lifetime"
                                            {{ old('access_type', $course->access_type ?? '') == 'lifetime' ? 'selected' : '' }}>
                                            Selamanya
                                        </option>
                                        <option value="subscription"
                                            {{ old('access_type', $course->access_type ?? '') == 'subscription' ? 'selected' : '' }}>
                                            Langganan
                                        </option>
                                    </select>
                                    @error('access_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Subscription Duration -->
                            <div class="row mb-3 subscription-duration d-none">
                                <label class="col-sm-2 col-form-label" for="subscription_duration">Durasi Langganan
                                    (hari)</label>
                                <div class="col-sm-10">
                                    <input type="number" id="subscription_duration" name="subscription_duration"
                                        class="form-control @error('subscription_duration') is-invalid @enderror"
                                        value="{{ old('subscription_duration', $course->subscription_duration ?? '') }}"
                                        min="1" placeholder="Contoh: 30">
                                    @error('subscription_duration')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Features -->
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label">Fitur-Fitur</label>
                                <div class="col-sm-10">
                                    <div id="features-wrapper">
                                        @if (isset($course) && $course->features)
                                            @foreach (json_decode($course->features, true) as $index => $feature)
                                                <div class="input-group mb-2">
                                                    <input type="text" name="features[]" class="form-control"
                                                        value="{{ $feature }}" placeholder="Masukkan fitur">
                                                    @if ($loop->last)
                                                        <button type="button" class="btn btn-success add-feature">
                                                            <i class="bx bx-plus"></i>
                                                        </button>
                                                    @else
                                                        <button type="button" class="btn btn-danger remove-feature">
                                                            <i class="bx bx-trash"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="input-group mb-2">
                                                <input type="text" name="features[]" class="form-control"
                                                    placeholder="Masukkan fitur">
                                                <button type="button" class="btn btn-success add-feature"><i
                                                        class="bx bx-plus"></i></button>
                                            </div>
                                        @endif


                                    </div>
                                </div>
                            </div>


                            <!-- Submit -->
                            <div class="row justify-content-end">
                                <div class="col-sm-10">
                                    <button type="submit"
                                        class="btn btn-primary">{{ isset($course) ? 'Update' : 'Simpan' }}</button>
                                    <a href="{{ route('kursus.index') }}" class="btn btn-secondary">Kembali</a>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        // Inisialisasi Select2
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap-5',
                placeholder: '-- Pilih Course --',
                allowClear: true,
                width: '100%'
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const wrapper = document.getElementById('features-wrapper');

            wrapper.addEventListener('click', function(e) {
                if (e.target.closest('.add-feature')) {
                    e.preventDefault();

                    // ganti tombol + di baris sebelumnya menjadi tombol hapus
                    const lastGroup = wrapper.querySelector('.input-group:last-child .add-feature');
                    if (lastGroup) {
                        lastGroup.outerHTML = `
                    <button type="button" class="btn btn-danger remove-feature">
                        <i class="bx bx-trash"></i>
                    </button>
                `;
                    }

                    // buat input baru dengan tombol +
                    let newInput = document.createElement('div');
                    newInput.classList.add('input-group', 'mb-2');
                    newInput.innerHTML = `
                <input type="text" name="features[]" class="form-control" placeholder="Masukkan fitur">
                <button type="button" class="btn btn-success add-feature"><i class="bx bx-plus"></i></button>
            `;
                    wrapper.appendChild(newInput);
                }

                if (e.target.closest('.remove-feature')) {
                    e.preventDefault();
                    e.target.closest('.input-group').remove();

                    // pastikan selalu ada tombol + di baris terakhir
                    const groups = wrapper.querySelectorAll('.input-group');
                    if (groups.length > 0) {
                        groups.forEach((group, index) => {
                            const btnAdd = group.querySelector('.add-feature');
                            const btnRemove = group.querySelector('.remove-feature');

                            if (index === groups.length - 1) {
                                if (!btnAdd) {
                                    if (btnRemove) btnRemove.outerHTML = `
                                <button type="button" class="btn btn-success add-feature">
                                    <i class="bx bx-plus"></i>
                                </button>
                            `;
                                }
                            } else {
                                if (!btnRemove) {
                                    if (btnAdd) btnAdd.outerHTML = `
                                <button type="button" class="btn btn-danger remove-feature">
                                    <i class="bx bx-trash"></i>
                                </button>
                            `;
                                }
                            }
                        });
                    }
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const accessType = document.getElementById('access_type');
            const subscriptionField = document.querySelector('.subscription-duration');

            function toggleDuration() {
                if (accessType.value === 'subscription') {
                    subscriptionField.classList.remove('d-none');
                } else {
                    subscriptionField.classList.add('d-none');
                    document.getElementById('subscription_duration').value = '';
                }
            }

            accessType.addEventListener('change', toggleDuration);
            toggleDuration(); // jalankan saat pertama kali
        });
    </script>

    <script>
        // Format currency input
        const priceDisplay = document.getElementById('price_display');
        const priceInput = document.getElementById('price');

        priceDisplay.addEventListener('input', function(e) {
            // Hapus semua karakter non-digit
            let numericValue = e.target.value.replace(/\D/g, '');

            // Simpan nilai numerik di input hidden
            priceInput.value = numericValue ? parseInt(numericValue) : 0;

            // Format tampilan dengan separator ribuan
            if (numericValue) {
                e.target.value = 'Rp ' + new Intl.NumberFormat('id-ID').format(numericValue);
            } else {
                e.target.value = '';
            }
        });

        // Inisialisasi nilai saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            const initialValue = priceInput.value;
            if (initialValue && initialValue > 0) {
                priceDisplay.value = 'Rp ' + new Intl.NumberFormat('id-ID').format(initialValue);
            }
        });

        // Toggle price field based on is_free selection
        document.getElementById('is_free').addEventListener('change', function(e) {
            if (e.target.value === '1') {
                priceDisplay.value = 'Rp 0';
                priceInput.value = 0;
                priceDisplay.setAttribute('readonly', 'readonly');
            } else {
                priceDisplay.removeAttribute('readonly');
                if (priceDisplay.value === 'Rp 0') {
                    priceDisplay.value = '';
                    priceInput.value = '';
                }
            }
        });

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            const isFreeSelect = document.getElementById('is_free');
            if (isFreeSelect.value === '1') {
                priceDisplay.setAttribute('readonly', 'readonly');
            }
        });
        // Preview thumbnail
        document.getElementById('thumbnail').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview-image').src = e.target.result;
                    document.getElementById('thumbnail-preview').classList.remove('d-none');
                }
                reader.readAsDataURL(file);
            } else {
                document.getElementById('thumbnail-preview').classList.add('d-none');
            }
        });
    </script>
@endpush
