@extends('template.app')
@section('title', 'Tryout')
@section('content')
    <div class="page-heading">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Tryout</h3>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Tryout</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="page-content">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                <i class="bx bx-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card radius-10">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">Daftar Kursus</h5>

                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle mb-0" id="latihan-table" style="width: 100%">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th>Thumbnail</th>
                                <th>Judul</th>
                                <th>Kategori</th>
                                <th>Tipe Akses</th>
                                <th>Status</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <style>
        .status-badge {
            padding: 0.35em 0.65em;
            font-size: 0.75em;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.25rem;
        }

        .status-active {
            color: #fff;
            background-color: #198754;
        }

        .status-inactive {
            color: #fff;
            background-color: #dc3545;
        }

        .thumbnail-img {
            width: 60px;
            height: 40px;
            object-fit: cover;
            border-radius: 4px;
        }
    </style>
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            const table = $('#latihan-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('tryout.load') }}",
                    type: "POST"
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'thumbnail',
                        name: 'thumbnail',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            if (data) {
                                return '<img src="{{ asset('storage') }}/' + data +
                                    '" class="thumbnail-img" alt="Thumbnail">';
                            } else {
                                return '<img src="{{ asset('assets/images/default-thumbnail.jpg') }}" class="thumbnail-img" alt="Default Thumbnail">';
                            }
                        }
                    },
                    {
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'nama_kategori',
                        name: 'kategori.nama_kategori'
                    },
                    {
                        data: 'access_type',
                        name: 'access_type',
                        render: function(data, type, row) {
                            if (data === 'lifetime') {
                                return 'Selamanya';
                            } else if (data === 'subscription') {
                                return 'Langganan';
                            } else {
                                return '-';
                            }
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        render: function(data, type, row) {
                            if (data === 'active') {
                                return '<span class="status-badge status-active">Aktif</span>';
                            } else {
                                return '<span class="status-badge status-inactive">Tidak Aktif</span>';
                            }
                        }
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // Hapus latihan
            $(document).on('click', '.delete-btn', function() {
                const id = $(this).data('id');
                const url = "{{ route('tryout.destroy', ':id') }}".replace(':id', id);

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data latihan akan dihapus permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            type: 'DELETE',
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Sukses',
                                        text: response.message
                                    });
                                    table.ajax.reload();
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: xhr.responseJSON?.message ||
                                        'Terjadi kesalahan'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
