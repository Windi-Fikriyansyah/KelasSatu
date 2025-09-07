@extends('template.app')
@section('title', 'admin')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <!-- Basic Layout & Basic with Icons -->
        <div class="row">
            <!-- Basic Layout -->
            <div class="col-xxl">
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">{{ isset($user) ? 'Edit' : 'Tambah' }} Admin</h5>
                        <small class="text-muted float-end">Form Admin</small>
                    </div>
                    <div class="card-body">
                        <form action="{{ isset($user) ? route('admin.update', $user->id) : route('admin.store') }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            @if (isset($user))
                                @method('PUT')
                            @endif

                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="name">Nama Lengkap</label>
                                <div class="col-sm-10">
                                    <input type="text" id="name" name="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name', $user->name ?? '') }}" required />
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>


                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="email">Email</label>
                                <div class="col-sm-10">
                                    <input type="email" id="email" name="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        value="{{ old('email', $user->email ?? '') }}" required />
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="phone">Nomor Telepon</label>
                                <div class="col-sm-10">
                                    <input type="text" id="no_hp" name="no_hp"
                                        class="form-control @error('no_hp') is-invalid @enderror"
                                        value="{{ old('no_hp', $user->no_hp ?? '') }}" />
                                    @error('no_hp')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label"
                                    for="password">{{ isset($user) ? 'Password Baru' : 'Password' }}</label>
                                <div class="col-sm-10">
                                    <input type="password" id="password" name="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        {{ isset($user) ? '' : 'required' }} />
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if (isset($user))
                                        <small class="text-muted">Biarkan kosong jika tidak ingin mengubah password</small>
                                    @endif
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="password_confirmation">Konfirmasi
                                    Password</label>
                                <div class="col-sm-10">
                                    <input type="password" id="password_confirmation" name="password_confirmation"
                                        class="form-control @error('password_confirmation') is-invalid @enderror"
                                        {{ isset($user) ? '' : 'required' }} />
                                    @error('password_confirmation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="role">Role</label>
                                <div class="col-sm-10">
                                    <select id="role" name="role"
                                        class="form-control select2 @error('role') is-invalid @enderror" required>
                                        <option value="">-- Pilih Role --</option>
                                        <option value="owner"
                                            {{ old('role', $user->role ?? '') == 'owner' ? 'selected' : '' }}>Owner
                                        </option>
                                        <option value="admin"
                                            {{ old('role', $user->role ?? '') == 'admin' ? 'selected' : '' }}>Admin
                                        </option>
                                    </select>
                                    @error('role')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>


                            <div class="row justify-content-end">
                                <div class="col-sm-10">
                                    <button type="submit" class="btn btn-primary">
                                        {{ isset($user) ? 'Update' : 'Simpan' }}
                                    </button>
                                    <a href="{{ route('admin.index') }}" class="btn btn-secondary">Kembali</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
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
    </style>
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endpush
