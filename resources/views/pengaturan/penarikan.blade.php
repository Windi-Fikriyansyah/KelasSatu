@extends('layouts.app')

@section('title', 'Penarikan Saldo')

@section('content')
    <div class="min-h-screen bg-gray-50 py-4 sm:py-8">
        <div class="max-w-6xl mx-auto px-3 sm:px-4 lg:px-8">

            <!-- Header Section -->
            <div class="mb-6 sm:mb-8">
                <div class="flex items-center mb-4">
                    <a href="{{ route('account.index') }}"
                        class="mr-4 p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                            </path>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-light text-gray-900">Penarikan Saldo</h1>
                        <p class="text-sm sm:text-base text-gray-600">Tarik saldo Anda ke rekening bank</p>
                    </div>
                </div>
            </div>

            <!-- Saldo Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
                <div class="p-4 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 1.343-3 3h6c0-1.657-1.343-3-3-3zm0 0V5m0 6v6m0 6a9 9 0 100-18 9 9 0 000 18z" />
                                </svg>
                            </div>
                            <h2 class="text-lg font-semibold text-gray-900">Saldo Tersedia</h2>
                        </div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <p class="text-2xl sm:text-3xl font-bold text-green-600">
                            Rp {{ number_format(Auth::user()->balance ?? 0, 0, ',', '.') }}
                        </p>
                        <p class="text-sm text-gray-600 mt-1">Minimal penarikan Rp 250.000</p>
                    </div>
                </div>
            </div>

            <!-- Withdrawal Form -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-4 sm:p-8">
                    <div class="flex items-center mb-6">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                        </div>
                        <h2 class="text-xl font-semibold text-gray-900">Form Penarikan</h2>
                    </div>

                    <form action="{{ route('account.withdrawalProcess') }}" method="POST" id="withdrawalForm"
                        class="space-y-6">
                        @csrf

                        <!-- Bank Selection -->
                        <div>
                            <label for="bank_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Pilih Rekening Bank <span class="text-red-500">*</span>
                            </label>
                            @if (isset($userBanks) && count($userBanks) > 0)
                                <select name="bank_id" id="bank_id" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-white">
                                    <option value="">Pilih rekening bank</option>
                                    @foreach ($userBanks as $bank)
                                        <option value="{{ $bank->id }}"
                                            {{ old('bank_id') == $bank->id ? 'selected' : '' }}>
                                            {{ $bank->nama_bank }} - {{ $bank->no_rekening }} ({{ $bank->nama_pemilik }})
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
                                    <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                        </path>
                                    </svg>
                                    <p class="text-gray-600 mb-3">Belum ada rekening bank terdaftar</p>
                                    <a href="{{ route('account.bank') }}"
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Tambah Rekening
                                    </a>
                                </div>
                            @endif
                        </div>

                        <!-- Amount Input -->
                        @if (isset($userBanks) && count($userBanks) > 0)
                            <div>
                                <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                                    Jumlah Penarikan <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">Rp</span>
                                    </div>
                                    <input type="text" name="amount_display" id="amount_display"
                                        value="{{ old('amount') ? number_format(old('amount'), 0, ',', '.') : '' }}"
                                        placeholder="250.000"
                                        class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg
                       focus:ring-2 focus:ring-blue-500 focus:border-transparent
                       transition-all duration-200 bg-gray-50 focus:bg-white">

                                    <!-- hidden input untuk dikirim ke backend -->
                                    <input type="hidden" name="amount" id="amount" value="{{ old('amount') }}">
                                </div>
                                <p class="text-xs text-gray-600 mt-1">
                                    Minimal Rp 250.000 - Maksimal Rp
                                    {{ number_format(Auth::user()->balance ?? 0, 0, ',', '.') }}
                                </p>
                            </div>

                            <!-- Quick Amount Buttons -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Cepat</label>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                    @php
                                        $balance = Auth::user()->balance ?? 0;
                                        $quickAmounts = [250000, 500000];
                                    @endphp
                                    @foreach ($quickAmounts as $quickAmount)
                                        @if ($balance >= $quickAmount)
                                            <button type="button" onclick="setAmount({{ $quickAmount }})"
                                                class="quick-amount-btn px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200">
                                                {{ number_format($quickAmount / 1000, 0) }}K
                                            </button>
                                        @endif
                                    @endforeach
                                    @if ($balance > 500000)
                                        <button type="button" onclick="setAmount({{ $balance }})"
                                            class="quick-amount-btn px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200">
                                            Semua
                                        </button>
                                    @endif
                                </div>
                            </div>

                            <!-- Notes -->
                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Catatan
                                    (Opsional)</label>
                                <textarea name="notes" id="notes" rows="3"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-gray-50 focus:bg-white resize-none"
                                    placeholder="Tambahkan catatan untuk penarikan ini...">{{ old('notes') }}</textarea>
                            </div>

                            <!-- Terms -->
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-blue-800 mb-2">Informasi Penting:</h3>
                                        <ul class="text-sm text-blue-700 space-y-1">
                                            <li>• Proses penarikan akan diverifikasi dalam 1-2 hari kerja</li>
                                            <li>• Dana akan masuk ke rekening dalam 2-3 hari kerja setelah verifikasi</li>
                                            <li>• Pastikan data rekening bank sudah benar dan aktif</li>
                                            <li>• Tidak ada biaya admin untuk penarikan</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex justify-end pt-4">
                                <button type="submit" id="submitBtn"
                                    class="w-full sm:w-auto px-8 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors duration-200 disabled:bg-gray-400 disabled:cursor-not-allowed">
                                    <span class="flex items-center justify-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                                            </path>
                                        </svg>
                                        Ajukan Penarikan
                                    </span>
                                </button>
                            </div>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Recent Withdrawals -->
            @if (isset($recentWithdrawals) && count($recentWithdrawals) > 0)
                <div class="mt-8 bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-4 sm:p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Riwayat Penarikan Terbaru</h3>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @foreach ($recentWithdrawals->take(5) as $withdrawal)
                            <div class="p-4 sm:p-6">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            @if ($withdrawal->status == 'pending')
                                                <div
                                                    class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-yellow-600" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </div>
                                            @elseif($withdrawal->status == 'approved')
                                                <div
                                                    class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-green-600" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </div>
                                            @else
                                                <div
                                                    class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-red-600" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <p class="text-sm font-medium text-gray-900">
                                                Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                {{ $withdrawal->nama_bank }} - {{ $withdrawal->no_rekening }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if ($withdrawal->status == 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($withdrawal->status == 'approved') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800 @endif">
                                            {{ ucfirst($withdrawal->status) }}
                                        </span>
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ \Carbon\Carbon::parse($withdrawal->created_at)->format('d M Y, H:i') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
@push('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <style>
        .quick-amount-btn:hover {
            background-color: #f3f4f6;
            border-color: #2563eb;
        }

        .quick-amount-btn.active {
            background-color: #2563eb;
            color: white;
            border-color: #2563eb;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
        }

        @media (max-width: 640px) {

            input,
            select,
            textarea,
            button {
                min-height: 44px;
            }
        }
    </style>
@endpush

@push('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        const amountDisplay = document.getElementById('amount_display');
        const amountHidden = document.getElementById('amount');

        function formatRupiah(angka) {
            return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        function unformatRupiah(angka) {
            return angka.replace(/\./g, '');
        }

        // Format saat ketik
        amountDisplay?.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, ''); // hanya angka
            if (value) {
                amountDisplay.value = formatRupiah(value);
                amountHidden.value = value; // simpan angka asli ke hidden input
            } else {
                amountDisplay.value = '';
                amountHidden.value = '';
            }
        });

        // Quick Amount Button
        function setAmount(amount) {
            amountDisplay.value = formatRupiah(amount);
            amountHidden.value = amount;

            // Update active state
            document.querySelectorAll('.quick-amount-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');
        }

        // Validasi submit
        document.getElementById('withdrawalForm')?.addEventListener('submit', function(e) {
            const amount = parseInt(amountHidden.value);
            const balance = {{ Auth::user()->balance ?? 0 }};

            if (!amount || amount < 250000) {
                e.preventDefault();
                toastr.error("Minimal penarikan Rp 250.000");
                return false;
            }

            if (amount > balance) {
                e.preventDefault();
                toastr.error("Saldo tidak mencukupi untuk penarikan ini");
                return false;
            }

            toastr.success("Permintaan penarikan diproses...");
        });

        // Auto show pesan dari session (jika ada)
        @if (session('success'))
            toastr.success("{{ session('success') }}");
        @endif
        @if (session('error'))
            toastr.error("{{ session('error') }}");
        @endif
    </script>
@endpush
