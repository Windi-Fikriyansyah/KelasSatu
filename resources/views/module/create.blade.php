@extends('template.app')
@section('title', isset($module) ? 'Edit Module' : 'Tambah Module')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="row">
            <div class="col-xxl">
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">{{ isset($module) ? 'Edit' : 'Tambah' }} Module</h5>
                        <small class="text-muted float-end">Form Module</small>
                    </div>
                    <div class="card-body">
                        <form action="{{ isset($module) ? route('module.update', $module->id) : route('module.store') }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            @if (isset($module))
                                @method('PUT')
                            @endif

                            <!-- Course Selection -->
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="course_id">Course</label>
                                <div class="col-sm-10">
                                    <select id="course_id" name="course_id"
                                        class="form-control select2 @error('course_id') is-invalid @enderror" required>
                                        <option value="">-- Pilih Course --</option>
                                        @foreach ($courses as $course)
                                            <option value="{{ $course->id }}"
                                                {{ old('course_id', $module->course_id ?? '') == $course->id ? 'selected' : '' }}>
                                                {{ $course->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('course_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Title -->
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="title">Judul Module</label>
                                <div class="col-sm-10">
                                    <input type="text" id="title" name="title"
                                        class="form-control @error('title') is-invalid @enderror"
                                        value="{{ old('title', $module->title ?? '') }}" required />
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
                                        rows="4" required>{{ old('description', $module->description ?? '') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Order -->
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="order">Urutan</label>
                                <div class="col-sm-10">
                                    <input type="number" id="order" name="order"
                                        class="form-control @error('order') is-invalid @enderror"
                                        value="{{ old('order', $module->order ?? 0) }}" required />
                                    @error('order')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Submit -->
                            <div class="row justify-content-end">
                                <div class="col-sm-10">
                                    <button type="submit"
                                        class="btn btn-primary">{{ isset($module) ? 'Update' : 'Simpan' }}</button>
                                    <a href="{{ route('module.index') }}" class="btn btn-secondary">Kembali</a>
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
@endpush

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
@endpush
