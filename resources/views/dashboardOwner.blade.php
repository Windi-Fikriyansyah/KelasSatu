@extends('template.app')
@section('title', 'Dashboard')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row g-4">

            <!-- Jumlah Siswa -->
            <div class="col-lg-3 col-md-6 col-12">
                <div class="card">
                    <div class="card-body d-flex justify-content-between align-items-start">
                        <div class="d-flex align-items-center">
                            <div class="avatar flex-shrink-0 me-3 bg-light-primary rounded p-2">
                                <i class="bx bx-user text-primary fs-3"></i>
                            </div>
                            <div>
                                <span class="fw-semibold d-block mb-1">Jumlah Siswa</span>
                                <h3 class="card-title mb-2">{{ $jumlahSiswa }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Jumlah Guru -->
            <div class="col-lg-3 col-md-6 col-12">
                <div class="card">
                    <div class="card-body d-flex justify-content-between align-items-start">
                        <div class="d-flex align-items-center">
                            <div class="avatar flex-shrink-0 me-3 bg-light-info rounded p-2">
                                <i class="bx bx-chalkboard text-info fs-3"></i>
                            </div>
                            <div>
                                <span class="fw-semibold d-block mb-1">Jumlah Guru</span>
                                <h3 class="card-title mb-2">{{ $jumlahGuru }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Jumlah Transaksi Paid -->
            <div class="col-lg-3 col-md-6 col-12">
                <div class="card">
                    <div class="card-body d-flex justify-content-between align-items-start">
                        <div class="d-flex align-items-center">
                            <div class="avatar flex-shrink-0 me-3 bg-light-danger rounded p-2">
                                <i class="bx bx-credit-card text-danger fs-3"></i>
                            </div>
                            <div>
                                <span class="fw-semibold d-block mb-1">Transaksi Paid</span>
                                <h3 class="card-title mb-2">{{ $jumlahTransaksiPaid }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Amount -->
            <div class="col-lg-3 col-md-6 col-12">
                <div class="card">
                    <div class="card-body d-flex justify-content-between align-items-start">
                        <div class="d-flex align-items-center">
                            <div class="avatar flex-shrink-0 me-3 bg-light-success rounded p-2">
                                <i class="bx bx-wallet text-success fs-3"></i>
                            </div>
                            <div>
                                <span class="fw-semibold d-block mb-1">Total Amount</span>
                                <h3 class="card-title mb-2">
                                    <small class="text-muted" style="font-size: 18px;">Rp</small>
                                    <span style="font-size: 18px;">
                                        {{ number_format($totalAmount, 0, ',', '.') }}
                                    </span>
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
