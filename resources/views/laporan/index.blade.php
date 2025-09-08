@extends('template.app')
@section('title', 'Laporan Withdraw')
@section('content')
    <div class="page-heading">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Laporan Transaksi</h3>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Laporan Transaksi</li>
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
                    <h5 class="card-title mb-0">Laporan Transaksi</h5>

                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label for="status_filter" class="form-label fw-bold">Filter Status</label>
                            <select id="status_filter" class="form-select">
                                <option value="">-- Semua --</option>
                                <option value="PAID">PAID</option>
                                <option value="PENDING">PENDING</option>
                                <option value="FAILED">FAILED</option>
                            </select>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0" id="withdraw-table" style="width: 100%">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>User</th>
                                    <th>Nama Course</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Metode Pembayaran</th>
                                    <th>Tanggal</th>
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
                        url: "{{ route('laporan_transaksi.load') }}",
                        type: "POST",
                        data: function(d) {
                            d.status = $('#status_filter').val();
                        }
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'name',
                            name: 's.name'
                        }, // perbaikan → user name dari tabel users
                        {
                            data: 'title',
                            name: 'u.title'
                        }, // perbaikan → course title dari tabel courses
                        {
                            data: 'amount',
                            name: 'w.amount'
                        },
                        {
                            data: 'status',
                            name: 'w.status'
                        },
                        {
                            data: 'payment_channel',
                            name: 'w.payment_channel'
                        },
                        {
                            data: 'created_at',
                            name: 'w.created_at'
                        }
                    ]

                });

                $('#status_filter').change(function() {
                    table.ajax.reload();
                });



            });
        </script>
    @endpush
