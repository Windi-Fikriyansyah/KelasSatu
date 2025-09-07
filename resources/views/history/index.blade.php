@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8 sm:py-12" x-data="{ tab: 'all' }">
        <!-- Header Section -->
        <div class="mb-8 sm:mb-12">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-4xl font-bold text-primary-200 mb-2">History Transaksi</h1>
                    <p class="text-sm sm:text-base text-gray-600">Kelola dan pantau semua transaksi kursus Anda</p>
                </div>
                <div class="w-full sm:w-auto">
                    <div class="bg-white rounded-xl p-3 sm:p-4 shadow-sm border w-full sm:w-auto">
                        <div class="flex items-center justify-center sm:justify-start space-x-2 text-gray-600">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                </path>
                            </svg>
                            <span class="text-sm font-medium">Total: {{ count($transactions) }} transaksi</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="mb-6 sm:mb-8 overflow-x-auto">
            <div class="bg-white rounded-xl p-1 sm:p-2 shadow-sm border inline-flex space-x-1 min-w-max">
                @php
                    $tabs = [
                        [
                            'key' => 'all',
                            'label' => 'Semua',
                            'color' => 'primary-100',
                            'icon' => 'M19 11H5m14-7l2 8-2 8M5 4l-2 8 2 8',
                        ],
                        ['key' => 'PAID', 'label' => 'Berhasil', 'color' => 'green-500', 'icon' => 'M5 13l4 4L19 7'],
                        [
                            'key' => 'PENDING',
                            'label' => 'Menunggu',
                            'color' => 'yellow-500',
                            'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                        ],
                        ['key' => 'FAILED', 'label' => 'Gagal', 'color' => 'red-500', 'icon' => 'M6 18L18 6M6 6l12 12'],
                    ];
                @endphp
                @foreach ($tabs as $t)
                    <button
                        :class="tab === '{{ $t['key'] }}' ? 'bg-{{ $t['color'] }} text-white shadow-md' :
                            'text-gray-600 hover:text-gray-900 hover:bg-gray-50'"
                        class="px-3 py-2 sm:px-6 sm:py-3 rounded-lg font-semibold text-sm sm:text-base transition-all duration-200 ease-in-out transform hover:scale-105 whitespace-nowrap"
                        @click="tab='{{ $t['key'] }}'">
                        <span class="flex items-center space-x-1 sm:space-x-2">
                            <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="{{ $t['icon'] }}"></path>
                            </svg>
                            <span>{{ $t['label'] }}</span>
                        </span>
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Transaction Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 md:gap-8">
            @foreach ($transactions as $transaction)
                <div x-show="tab==='all' || tab==='{{ $transaction->status }}'"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    class="bg-white rounded-2xl shadow-lg hover:shadow-xl p-4 sm:p-6 border transform transition-all duration-300 hover:-translate-y-1 relative overflow-hidden group break-words">

                    <!-- Status Indicator -->
                    <div
                        class="absolute top-0 right-0 w-16 h-16 sm:w-20 sm:h-20 transform rotate-45 translate-x-6 -translate-y-6 sm:translate-x-8 sm:-translate-y-8
                {{ $transaction->status === 'PAID' ? 'bg-green-500' : ($transaction->status === 'PENDING' ? 'bg-yellow-500' : 'bg-red-500') }}
                opacity-10 group-hover:opacity-20 transition-opacity">
                    </div>

                    <!-- Header -->
                    <div class="flex items-start justify-between mb-3 sm:mb-4 flex-wrap">
                        <div class="flex items-center space-x-2 sm:space-x-3 max-w-[70%]">
                            <div
                                class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-primary-100 bg-opacity-10 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-primary-100" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                    </path>
                                </svg>
                            </div>
                            <div class="truncate">
                                <h3
                                    class="text-base sm:text-lg font-bold text-gray-800 group-hover:text-primary-200 transition-colors break-words">
                                    {{ $transaction->title }}
                                </h3>
                                <p class="text-xs sm:text-sm text-gray-500 truncate">
                                    {{ \Carbon\Carbon::parse($transaction->created_at)->format('d M Y, H:i') }}
                                </p>
                            </div>
                        </div>

                        <!-- Status Badge -->
                        <span
                            class="px-2 py-1 sm:px-3 sm:py-1 rounded-full text-xs font-bold tracking-wide mt-2 sm:mt-0
                        {{ $transaction->status === 'PAID'
                            ? 'bg-green-100 text-green-800 border border-green-200'
                            : ($transaction->status === 'PENDING'
                                ? 'bg-yellow-100 text-yellow-800 border border-yellow-200'
                                : 'bg-red-100 text-red-800 border border-red-200') }}">
                            {{ $transaction->status === 'PAID' ? 'BERHASIL' : ($transaction->status === 'PENDING' ? 'MENUNGGU' : 'GAGAL') }}
                        </span>
                    </div>

                    <!-- Transaction Details -->
                    <div class="space-y-2 sm:space-y-3">
                        <div class="flex items-center justify-between p-2 sm:p-3 bg-gray-50 rounded-lg break-words">
                            <div class="flex items-center space-x-1 sm:space-x-2">
                                <svg class="w-3 h-3 sm:w-4 sm:h-4 text-gray-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                                </svg>
                                <span class="text-xs sm:text-sm font-medium text-gray-600">Invoice ID</span>
                            </div>
                            <span
                                class="text-xs sm:text-sm font-mono text-gray-800 bg-white px-1 sm:px-2 py-0.5 sm:py-1 rounded truncate max-w-[100px] sm:max-w-none">{{ $transaction->invoice_id }}</span>
                        </div>

                        <div class="flex items-center justify-between p-2 sm:p-3 bg-gray-50 rounded-lg break-words">
                            <div class="flex items-center space-x-1 sm:space-x-2">
                                <svg class="w-3 h-3 sm:w-4 sm:h-4 text-gray-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                    </path>
                                </svg>
                                <span class="text-xs sm:text-sm font-medium text-gray-600">Total Pembayaran</span>
                            </div>
                            <span class="text-base sm:text-lg font-bold text-primary-100 whitespace-nowrap">
                                Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <!-- Action Button -->
                    @if ($transaction->status === 'PENDING')
                        <div class="mt-4 sm:mt-6 pt-3 sm:pt-4 border-t border-gray-100">
                            <a href="{{ route('payment.index', ['encryptedCourseId' => Crypt::encryptString($transaction->course_id)]) }}"
                                class="w-full bg-primary-100 hover:bg-orange-600 text-white font-semibold py-2 sm:py-3 px-3 sm:px-4 rounded-lg transition-all duration-200 transform hover:scale-105 flex items-center justify-center space-x-1 sm:space-x-2 text-sm sm:text-base break-words">
                                <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                                    </path>
                                </svg>
                                <span>Lanjutkan Pembayaran</span>
                            </a>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Empty State -->
        @if ($transactions->isEmpty())
            <div class="text-center py-12 sm:py-16">
                <div class="max-w-md mx-auto">
                    <div
                        class="w-16 h-16 sm:w-24 sm:h-24 mx-auto mb-6 sm:mb-8 bg-gray-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 sm:w-12 sm:h-12 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V9a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-bold text-gray-800 mb-3 sm:mb-4">Belum Ada Transaksi</h3>
                    <p class="text-sm sm:text-base text-gray-600 mb-6 sm:mb-8 leading-relaxed">
                        Anda belum memiliki riwayat transaksi. Mulai jelajahi kursus-kursus menarik dan beli kelas pertama
                        Anda!
                    </p>
                    <a href="{{ route('course') }}"
                        class="inline-flex items-center space-x-1 sm:space-x-2 bg-primary-100 hover:bg-orange-600 text-white font-semibold py-2 sm:py-3 px-5 sm:px-8 rounded-lg transition-all duration-200 transform hover:scale-105 text-sm sm:text-base break-words">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                            </path>
                        </svg>
                        <span>Jelajahi Kursus</span>
                    </a>
                </div>
            </div>
        @endif
    </div>
@endsection
