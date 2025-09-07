@extends('template.app')
@section('title', isset($kategori) ? 'Edit kategori' : 'Tambah kategori')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="row">
            <div class="col-xxl">
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">{{ isset($kategori) ? 'Edit' : 'Tambah' }} kategori</h5>
                        <small class="text-muted float-end">Form kategori</small>
                    </div>
                    <div class="card-body">
                        <form
                            action="{{ isset($kategori) ? route('kategori.update', $kategori->id) : route('kategori.store') }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            @if (isset($kategori))
                                @method('PUT')
                            @endif

                            <!-- Title -->
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="title">Nama kategori</label>
                                <div class="col-sm-10">
                                    <input type="text" id="nama_kategori" name="nama_kategori"
                                        class="form-control @error('nama_kategori') is-invalid @enderror"
                                        value="{{ old('nama_kategori', $kategori->nama_kategori ?? '') }}" required />
                                    @error('nama_kategori')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>


                            <!-- Submit -->
                            <div class="row justify-content-end">
                                <div class="col-sm-10">
                                    <button type="submit"
                                        class="btn btn-primary">{{ isset($kategori) ? 'Update' : 'Simpan' }}</button>
                                    <a href="{{ route('kategori.index') }}" class="btn btn-secondary">Kembali</a>
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
    <script>
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
        const priceInput = document.getElementById('price');
        const priceNumeric = document.getElementById('price_numeric');

        priceInput.addEventListener('input', function(e) {
            // Hapus semua karakter non-digit
            let numericValue = e.target.value.replace(/\D/g, '');

            // Simpan nilai numerik di input hidden
            priceNumeric.value = numericValue ? parseInt(numericValue) : 0;

            // Format tampilan dengan separator ribuan
            if (numericValue) {
                e.target.value = 'Rp ' + new Intl.NumberFormat('id-ID').format(numericValue);
            } else {
                e.target.value = '';
            }
        });

        // Inisialisasi nilai saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            const initialValue = priceNumeric.value;
            if (initialValue && initialValue > 0) {
                priceInput.value = 'Rp ' + new Intl.NumberFormat('id-ID').format(initialValue);
            }
        });

        // Validasi form sebelum submit
        document.querySelector('form').addEventListener('submit', function(e) {
            // Pastikan nilai numerik sudah terisi
            if (!priceNumeric.value || priceNumeric.value == 0) {
                // Jika gratis, set nilai ke 0
                if (document.getElementById('is_free').value === '1') {
                    priceNumeric.value = 0;
                } else {
                    // Jika tidak gratis, tampilkan error
                    e.preventDefault();
                    alert('Harap isi harga dengan benar');
                    priceInput.focus();
                }
            }
        });

        // Toggle price field based on is_free selection
        document.getElementById('is_free').addEventListener('change', function(e) {
            if (e.target.value === '1') {
                priceInput.value = 'Rp 0';
                priceNumeric.value = 0;
                priceInput.setAttribute('readonly', 'readonly');
            } else {
                priceInput.removeAttribute('readonly');
                if (priceInput.value === 'Rp 0') {
                    priceInput.value = '';
                    priceNumeric.value = '';
                }
            }
        });

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            const isFreeSelect = document.getElementById('is_free');
            if (isFreeSelect.value === '1') {
                priceInput.setAttribute('readonly', 'readonly');
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
