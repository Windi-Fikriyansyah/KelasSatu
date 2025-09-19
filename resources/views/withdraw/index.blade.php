@extends('template.app')
@section('title', 'Laporan Withdraw')
@section('content')
    <div class="page-heading">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Laporan Withdraw</h3>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Laporan Withdraw</li>
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
                    <h5 class="card-title mb-0">Laporan Withdraw</h5>

                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle mb-0" id="withdraw-table" style="width: 100%">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th>User</th>
                                <th>Nama Rek</th>
                                <th>Bank</th>
                                <th>No Rek</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th width="20%">Aksi</th>
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

            const table = $('#withdraw-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('withdraw.load') }}",
                    type: "POST"
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'user_name',
                        name: 'user_name'
                    },
                    {
                        data: 'nama_pemilik',
                        name: 'b.nama_pemilik'
                    },
                    {
                        data: 'bank_name',
                        name: 'bank_name'
                    },
                    {
                        data: 'no_rekening',
                        name: 'b.no_rekening'
                    },
                    {
                        data: 'amount',
                        name: 'amount'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });


            $(document).on('click', '.approve-btn', function(e) {
                e.preventDefault();
                const $btn = $(this);
                const url = $btn.data('url') || $btn.attr('data-url');
                if (!url) {
                    console.error('approve: data-url tidak ditemukan pada tombol');
                    return;
                }

                Swal.fire({
                    title: 'Konfirmasi Approve',
                    text: 'Apakah Anda yakin ingin approve withdraw ini?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Approve',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#28a745'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $btn.prop('disabled', true);
                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: {}, // CSRF sudah ter-setup via $.ajaxSetup
                            success: function(res) {
                                if (res.success) {
                                    Swal.fire('Sukses', res.message ||
                                        'Withdraw di-approve', 'success');
                                    // reload datatable tanpa reset halaman
                                    try {
                                        table.ajax.reload(null, false);
                                    } catch (err) {
                                        location.reload();
                                    }
                                } else {
                                    Swal.fire('Gagal', res.message || 'Gagal approve',
                                        'error');
                                }
                            },
                            error: function(xhr) {
                                const msg = xhr.responseJSON?.message ||
                                    'Terjadi kesalahan pada server';
                                Swal.fire('Error', msg, 'error');
                                console.error(xhr);
                            },
                            complete: function() {
                                $btn.prop('disabled', false);
                            }
                        });
                    }
                });
            });

            // REJECT
            $(document).on('click', '.reject-btn', function(e) {
                e.preventDefault();
                const $btn = $(this);
                const url = $btn.data('url') || $btn.attr('data-url');
                if (!url) {
                    console.error('reject: data-url tidak ditemukan pada tombol');
                    return;
                }

                Swal.fire({
                    title: 'Konfirmasi Reject',
                    text: 'Apakah Anda yakin ingin menolak withdraw ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Tolak',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#d33'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $btn.prop('disabled', true);
                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: {}, // jika ingin mengirim reason/notes bisa ditambahkan di sini
                            success: function(res) {
                                if (res.success) {
                                    Swal.fire('Sukses', res.message ||
                                        'Withdraw ditolak', 'success');
                                    try {
                                        table.ajax.reload(null, false);
                                    } catch (err) {
                                        location.reload();
                                    }
                                } else {
                                    Swal.fire('Gagal', res.message || 'Gagal menolak',
                                        'error');
                                }
                            },
                            error: function(xhr) {
                                const msg = xhr.responseJSON?.message ||
                                    'Terjadi kesalahan pada server';
                                Swal.fire('Error', msg, 'error');
                                console.error(xhr);
                            },
                            complete: function() {
                                $btn.prop('disabled', false);
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
