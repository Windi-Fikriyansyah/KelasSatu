@extends('layouts.app')
@section('title', $course->title)
@section('content')
    <section class="py-20 bg-primary-50 min-h-screen">
        <div class="max-w-5xl mx-auto px-4">
            <div class="grid lg:grid-cols-3 gap-8">
                <!-- Course Details - Left Side -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">


                        <!-- Course Content -->
                        <div class="p-6">
                            <div class="mb-4">
                                <h1 class="text-3xl font-bold text-primary-200 mb-2">{{ $course->title }}</h1>
                                <div class="flex items-center gap-4 text-sm text-gray-600 mb-4">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                                        </svg>
                                        Status: <span
                                            class="text-green-600 font-medium ml-1 capitalize">{{ $course->status }}</span>
                                    </span>
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                                        </svg>
                                        Kategori: {{ $course->nama_kategori }}
                                    </span>
                                </div>
                            </div>

                            <!-- Course Description -->
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-primary-200 mb-3">Deskripsi Kursus</h3>
                                <p class="text-gray-700 leading-relaxed">{{ $course->description }}</p>
                            </div>

                            <!-- Course Features -->
                            @php $features = json_decode($course->features, true); @endphp
                            @if (!empty($features))
                                <div class="mb-6">
                                    <h3 class="text-lg font-semibold text-primary-200 mb-3">Yang Akan Anda Dapatkan</h3>
                                    <ul class="space-y-2">
                                        @foreach ($features as $feature)
                                            <li class="flex items-start">
                                                <svg class="w-5 h-5 text-green-500 mr-3 mt-0.5 flex-shrink-0"
                                                    fill="currentColor" viewBox="0 0 24 24">
                                                    <path
                                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                                </svg>
                                                <span class="text-gray-700">{{ $feature }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- Course Duration -->
                            @if ($course->subscription_duration)
                                <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                                    <h3 class="text-lg font-semibold text-primary-200 mb-2">Durasi Akses</h3>
                                    <p class="text-gray-700">{{ $course->subscription_duration }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Purchase Summary - Right Side -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-24">
                        <div class="text-center mb-6">
                            <h2 class="text-2xl font-bold text-primary-200 mb-2">Ringkasan Pembelian</h2>
                            <div class="w-12 h-1 bg-primary-100 mx-auto rounded"></div>
                        </div>

                        <!-- Course Info Summary -->
                        <div class="space-y-4 mb-6">
                            <div class="flex justify-between items-start">
                                <span class="text-gray-600 text-sm">Nama Kursus:</span>
                                <span
                                    class="text-right text-sm font-medium text-gray-800 max-w-[60%]">{{ $course->title }}</span>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 text-sm">Tipe Akses:</span>
                                <span
                                    class="text-sm font-medium text-primary-200 capitalize">{{ $course->access_type ?? 'Lifetime' }}</span>
                            </div>

                            @if ($course->subscription_duration)
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 text-sm">Durasi:</span>
                                    <span
                                        class="text-sm font-medium text-gray-800">{{ $course->subscription_duration }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="mb-6">
                            <label for="referral_code" class="block text-gray-700 font-medium mb-2">Kode Referral
                                (Opsional)</label>
                            <input type="text" name="referral_code" id="referral_code"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary-200"
                                placeholder="Masukkan kode referral">
                        </div>

                        @if ($course->mapel === 'pilihan')
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-primary-200 mb-2">Pilih Modul (max
                                    {{ $course->limit }})</h3>

                                <!-- Dropdown -->
                                <div class="relative">
                                    <button type="button" id="moduleDropdownBtn"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2 text-left focus:outline-none focus:ring-2 focus:ring-primary-200">
                                        Pilih Modul
                                        <span class="float-right">▼</span>
                                    </button>
                                    <div id="moduleDropdownMenu"
                                        class="absolute z-10 mt-1 w-full bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto hidden">
                                        @foreach ($modules as $module)
                                            <label
                                                class="flex items-center gap-2 px-4 py-2 hover:bg-gray-100 cursor-pointer">
                                                <input type="checkbox" name="modules[]" value="{{ $module->id }}"
                                                    class="module-checkbox">
                                                <span>{{ $module->title }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Input hidden untuk menyimpan modul yang dipilih -->
                                <input type="hidden" name="selected_modules" id="selected_modules">
                            </div>
                        @elseif($course->mapel === 'wajib')
                            <input type="hidden" name="modules[]" value="all">
                            <div class="mb-6 p-4 bg-green-50 rounded-lg">
                                <p class="text-sm text-green-700">Semua modul sudah otomatis termasuk dalam kursus ini.</p>
                            </div>
                        @endif
                        <!-- Pricing Section -->
                        <div class="border-t border-gray-200 pt-4 mb-6">
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Harga Kursus:</span>
                                    @if ($course->is_free)
                                        <span class="text-lg font-bold text-green-600">GRATIS</span>
                                    @else
                                        <span class="text-lg font-semibold text-gray-800">Rp
                                            {{ number_format($course->price, 0, ',', '.') }}</span>
                                    @endif
                                </div>

                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Biaya Admin:</span>
                                    <span class="text-gray-800">Rp 0</span>
                                </div>

                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Diskon:</span>
                                    <span class="text-green-600">- Rp 0</span>
                                </div>
                            </div>
                        </div>

                        <!-- Total Section -->
                        <div class="border-t-2 border-primary-100 pt-4 mb-6">
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-semibold text-gray-800">Total Pembayaran:</span>
                                @if ($course->is_free)
                                    <span class="text-xl font-bold text-green-600">GRATIS</span>
                                @else
                                    <span class="text-xl font-bold text-primary-100">Rp
                                        {{ number_format($course->price, 0, ',', '.') }}</span>
                                @endif
                            </div>
                        </div>

                        <!-- Action Button -->
                        <div class="space-y-3">
                            @if ($course->is_free)
                                <a href="{{ route('course.checkout', Crypt::encryptString($course->id)) }}"
                                    class="w-full btn-primary px-6 py-4 rounded-xl text-white font-semibold text-center block transition-all duration-300 transform hover:scale-105">
                                    Dapatkan Gratis Sekarang
                                </a>
                            @else
                                <a href="{{ route('payment.index', Crypt::encryptString($course->id)) }}" id="pay-now-btn"
                                    class="w-full btn-primary px-6 py-4 rounded-xl text-white font-semibold text-center block transition-all duration-300 transform hover:scale-105">
                                    Bayar Sekarang
                                </a>
                            @endif


                        </div>

                        <!-- Security Badge -->
                        <div class="mt-6 p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center justify-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z" />
                                </svg>
                                Pembayaran 100% Aman
                            </div>
                        </div>

                        <!-- Course Stats -->
                        <div class="mt-6 grid grid-cols-2 gap-4 text-center">
                            <div class="p-3 bg-primary-50 rounded-lg">
                                <div class="text-lg font-bold text-primary-200">
                                    {{ $course->access_type === 'lifetime' ? '∞' : '30' }}</div>
                                <div class="text-xs text-gray-600">Hari Akses</div>
                            </div>
                            <div class="p-3 bg-primary-50 rounded-lg">
                                <div class="text-lg font-bold text-primary-200">24/7</div>
                                <div class="text-xs text-gray-600">Support</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Toast Container -->
    <div id="toast-container" class="fixed top-5 right-5 z-50 space-y-2"></div>

@endsection

@push('style')
    <style>
        .sticky {
            position: sticky;
        }

        .hover\:scale-105:hover {
            transform: scale(1.05);
        }

        @keyframes slide-in {
            0% {
                transform: translateX(100%);
                opacity: 0;
            }

            100% {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .animate-slide-in {
            animation: slide-in 0.3s ease forwards;
        }
    </style>
@endpush
@push('js')
    <script>
        function showToast(message, type = 'error') {
            const container = document.getElementById('toast-container');
            if (!container) return;

            const toast = document.createElement('div');
            toast.className = `min-w-[250px] max-w-sm px-4 py-3 rounded-lg shadow-lg text-white font-medium flex items-center justify-between
        ${type === 'error' ? 'bg-red-500' : 'bg-green-500'} animate-slide-in`;
            toast.innerHTML = `
        <span>${message}</span>
        <button class="ml-4 font-bold" onclick="this.parentElement.remove()">✕</button>
    `;

            container.appendChild(toast);

            // Auto remove after 3 seconds
            setTimeout(() => {
                toast.remove();
            }, 3000);
        }

        document.addEventListener("DOMContentLoaded", function() {
            const dropdownBtn = document.getElementById("moduleDropdownBtn");
            const dropdownMenu = document.getElementById("moduleDropdownMenu");
            const checkboxes = document.querySelectorAll(".module-checkbox");
            const maxLimit = {{ $course->limit ?? 0 }};
            const payBtn = document.getElementById("pay-now-btn");

            // Toggle dropdown
            if (dropdownBtn && dropdownMenu) {
                dropdownBtn.addEventListener("click", () => {
                    dropdownMenu.classList.toggle("hidden");
                });
            }

            // Limit checkbox selection
            function updateCheckboxes() {
                const checkedCount = document.querySelectorAll(".module-checkbox:checked").length;
                checkboxes.forEach(cb => {
                    if (!cb.checked) cb.disabled = checkedCount >= maxLimit;
                });

                // Update button text
                if (dropdownBtn) {
                    if (checkedCount > 0) {
                        dropdownBtn.innerHTML = `${checkedCount} modul dipilih <span class="float-right">▼</span>`;
                    } else {
                        dropdownBtn.innerHTML = `Pilih Modul <span class="float-right">▼</span>`;
                    }
                }
            }

            checkboxes.forEach(cb => cb.addEventListener("change", updateCheckboxes));

            // Close dropdown if clicked outside
            document.addEventListener("click", function(e) {
                if (dropdownBtn && dropdownMenu && !dropdownBtn.contains(e.target) && !dropdownMenu
                    .contains(e.target)) {
                    dropdownMenu.classList.add("hidden");
                }
            });

            // Handle payment button click
            if (payBtn) {
                payBtn.addEventListener("click", function(e) {
                    e.preventDefault();

                    // Cek apakah ini course dengan mapel "pilihan" dan belum ada modul yang dipilih
                    const courseMapel = "{{ $course->mapel }}";
                    if (courseMapel === "pilihan") {
                        const selectedModules = [];
                        document.querySelectorAll(".module-checkbox:checked").forEach(checkbox => {
                            selectedModules.push(checkbox.value);
                        });

                        if (selectedModules.length === 0) {
                            showToast("Silakan pilih minimal 1 modul terlebih dahulu!", "error");
                            return;
                        }


                        // Set nilai input hidden
                        document.getElementById("selected_modules").value = JSON.stringify(selectedModules);
                    }

                    let referralCode = document.getElementById("referral_code").value;
                    let baseUrl = payBtn.getAttribute("href");
                    let urlParams = new URLSearchParams();

                    if (referralCode) {
                        urlParams.append('referral_code', referralCode);
                    }

                    // Tambahkan selected_modules ke URL
                    if (courseMapel === "pilihan") {
                        const selectedModulesValue = document.getElementById("selected_modules").value;
                        if (selectedModulesValue) {
                            urlParams.append('selected_modules', selectedModulesValue);
                        }
                    }

                    const finalUrl = urlParams.toString() ? baseUrl + "?" + urlParams.toString() : baseUrl;
                    window.location.href = finalUrl;
                });
            }
        });
    </script>
@endpush
