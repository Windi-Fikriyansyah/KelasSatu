@php
    use Illuminate\Support\Str;
@endphp
@extends('layouts.app')
@section('title', 'Riwayat Nilai - ')

@section('content')
    <section class="py-8 bg-gradient-to-b from-primary-50 to-white min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header Course Info -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8 p-6">
                <div class="md:flex md:justify-between md:items-start">
                    <!-- Tombol Kembali ke Kelas -->
                    <div class="mt-4 md:mt-0 md:ml-4 flex-shrink-0">
                        <a href="javascript:history.back()"
                            class="inline-flex items-center bg-primary-100 hover:bg-primary-200 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            <i class="fa-solid fa-arrow-left mr-2"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>

            <!-- Tabel Riwayat Nilai Latihan -->
            <div id="riwayat-latihan" class="bg-white rounded-xl shadow-lg overflow-hidden mb-8 p-6">
                <h3 class="text-xl font-semibold text-primary-200 mb-4">Riwayat Nilai</h3>
                <div class="overflow-x-auto">
                    <table id="table-latihan" class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nama
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tanggal
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nilai
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Waktu Pengerjaan
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($riwayats as $riwayat)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $riwayat->quiz_title }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-500">
                                            {{ \Carbon\Carbon::parse($riwayat->created_at)->format('d M Y H:i') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ round($riwayat->score) }}</div>

                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ gmdate('H:i:s', $riwayat->duration) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div class="flex gap-2">
                                            <a href="{{ $riwayat->quiz_type === 'tryout'
                                                ? url('/kelas/tryout/' . $riwayat->id . '/hasilriwayat')
                                                : url('/kelas/latihan/' . $riwayat->id . '/hasilriwayat') }}"
                                                class="px-3 py-1 bg-blue-500 text-white rounded-lg text-sm hover:bg-blue-600 flex items-center">
                                                <i class="fa-solid fa-book-open mr-1"></i> Lihat Penjelasan
                                            </a>
                                            <button type="button"
                                                class="px-3 py-1 bg-green-500 text-white rounded-lg text-sm hover:bg-green-600 flex items-center btn-kerjakan-ulang"
                                                data-quiz-id="{{ base64_encode($riwayat->quiz_id) }}"
                                                data-quiz-title="{{ $riwayat->quiz_title }}"
                                                data-quiz-type="{{ $riwayat->quiz_type }}"
                                                data-quiz-url="{{ $riwayat->quiz_type === 'tryout' ? url('kelas/tryout/' . base64_encode($riwayat->quiz_id)) : url('kelas/latihan/' . base64_encode($riwayat->quiz_id)) }}"
                                                data-bs-toggle="modal" data-bs-target="#kerjakanUlangModal">
                                                <i class="fa-solid fa-redo mr-1"></i> Kerjakan Ulang
                                            </button>

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                        Belum ada riwayat nilai latihan
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Kerjakan Ulang -->
        <div class="modal fade" id="kerjakanUlangModal" tabindex="-1" aria-labelledby="kerjakanUlangLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: #10b981; color: white;">
                        <h5 class="modal-title" id="kerjakanUlangLabel">Kerjakan Ulang</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Kamu akan mulai mengerjakan soal <span id="modalQuizTitle" class="font-weight-bold"></span>.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <a href="" class="btn btn-success">Kerjakan</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('style')
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css"
        href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
@endpush

@push('js')
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


    <script>
        $(document).ready(function() {
            // Handler untuk tombol Kerjakan Ulang
            $(document).on('click', '.btn-kerjakan-ulang', function() {
                let quizTitle = $(this).data('quiz-title');
                let quizUrl = $(this).data('quiz-url');

                $('#kerjakanUlangLabel').text(`Kerjakan Ulang ${quizTitle}`);
                $('#modalQuizTitle').text(quizTitle);

                $('#kerjakanUlangModal .btn-success').attr('href', quizUrl);
            });

        });
    </script>
@endpush
