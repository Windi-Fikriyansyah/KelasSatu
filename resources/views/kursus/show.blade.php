@extends('layouts.app')

@section('title', $course->title)

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-primary-50 to-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

            {{-- Notifikasi dengan animasi --}}
            @if (session('success'))
                <div
                    class="bg-gradient-to-r from-green-500 to-green-600 text-white p-4 mb-6 rounded-xl shadow-lg animate-fade-in">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd"></path>
                        </svg>
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div
                    class="bg-gradient-to-r from-red-500 to-red-600 text-white p-4 mb-6 rounded-xl shadow-lg animate-fade-in">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd"></path>
                        </svg>
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            @if (session('info'))
                <div
                    class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-4 mb-6 rounded-xl shadow-lg animate-fade-in">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd"></path>
                        </svg>
                        {{ session('info') }}
                    </div>
                </div>
            @endif

            {{-- Breadcrumb --}}
            <nav class="flex mb-8" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('dashboardUser') }}"
                            class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-primary-100 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z">
                                </path>
                            </svg>
                            Beranda
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <a href="{{ route('course') }}"
                                class="ml-1 text-sm font-medium text-gray-700 hover:text-primary-100 md:ml-2 transition-colors">Kursus</a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <span
                                class="ml-1 text-sm font-medium text-gray-500 md:ml-2 truncate">{{ Str::limit($course->title, 30) }}</span>
                        </div>
                    </li>
                </ol>
            </nav>

            {{-- Main Content --}}
            <div class="bg-white rounded-2xl shadow-2xl overflow-hidden animate-fade-in">
                <div class="lg:grid lg:grid-cols-3 lg:gap-0">

                    {{-- Thumbnail Section --}}
                    <div class="lg:col-span-1 relative">
                        <div class="aspect-video lg:aspect-auto lg:h-full relative overflow-hidden">
                            <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}"
                                class="w-full h-full object-cover transform transition-transform duration-500 hover:scale-105">

                            {{-- Overlay dengan gradient --}}
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-black/30 via-transparent to-transparent lg:hidden">
                            </div>

                            {{-- Badge kategori --}}
                            <div class="absolute top-4 left-4">
                                <span class="course-badge px-3 py-1 rounded-full text-sm font-medium backdrop-blur-sm">
                                    {{ $course->nama_kategori ?? 'Umum' }}
                                </span>
                            </div>

                            {{-- Badge gratis --}}
                            @if ($course->is_free)
                                <div class="absolute top-4 right-4">
                                    <span
                                        class="bg-green-500 text-white px-3 py-1 rounded-full text-sm font-bold backdrop-blur-sm">
                                        GRATIS
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Content Section --}}
                    <div class="lg:col-span-2 p-8 lg:p-12">
                        {{-- Header --}}
                        <div class="mb-8">
                            <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4 leading-tight">
                                {{ $course->title }}
                            </h1>


                        </div>

                        {{-- Description --}}
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">Deskripsi Kursus</h3>
                            <p class="text-gray-700 leading-relaxed">{{ $course->description }}</p>
                        </div>

                        {{-- What you'll learn --}}
                        {{-- What you'll learn --}}
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Yang Akan Anda Pelajari</h3>
                            <div class="grid md:grid-cols-2 gap-3">
                                @php
                                    $features = json_decode($course->features, true) ?? [];
                                @endphp

                                @foreach ($features as $feature)
                                    <div class="flex items-start">
                                        <svg class="w-5 h-5 text-green-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="text-gray-700">{{ $feature }}</span>
                                    </div>
                                @endforeach

                                @if (count($features) === 0)
                                    <div class="text-gray-500">Belum ada fitur yang ditambahkan.</div>
                                @endif
                            </div>
                        </div>


                        {{-- Price and Action Section --}}
                        <div class="bg-gradient-to-r from-primary-50 to-blue-50 rounded-xl p-6 mb-8">
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between">
                                <div class="mb-4 sm:mb-0">
                                    <div class="text-3xl font-bold text-primary-200 mb-1">
                                        @if ($course->is_free)
                                            GRATIS
                                        @else
                                            Rp {{ number_format($course->price, 0, ',', '.') }}
                                        @endif
                                    </div>

                                </div>

                                {{-- Action Button --}}
                                @php
                                    $user = auth()->user();
                                    $hasAccess = DB::table('enrollments')
                                        ->where('user_id', $user->id)
                                        ->where('course_id', $course->id)
                                        ->exists();
                                @endphp

                                <div class="w-full sm:w-auto">
                                    @if ($hasAccess)
                                        <a href="{{ route('kelas.index') }}"
                                            class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 border border-transparent text-base font-medium rounded-xl text-white bg-green-600 hover:bg-green-700 transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-xl">
                                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z">
                                                </path>
                                            </svg>
                                            Masuk ke Kursus
                                        </a>
                                    @elseif($course->is_free)
                                        <form action="{{ route('payment.create', Crypt::encryptString($course->id)) }}"
                                            method="POST" class="w-full sm:w-auto">
                                            @csrf
                                            <button type="submit"
                                                class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 border border-transparent text-base font-medium rounded-xl text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-xl">
                                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                                        clip-rule="evenodd"></path>
                                                </svg>
                                                Dapatkan Kursus Gratis
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('payment.create', Crypt::encryptString($course->id)) }}"
                                            method="POST" class="w-full sm:w-auto">
                                            @csrf
                                            <button type="submit"
                                                class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 border border-transparent text-base font-medium rounded-xl text-white bg-gradient-to-r from-primary-100 to-orange-600 hover:from-orange-600 hover:to-primary-100 transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-xl">
                                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path
                                                        d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z">
                                                    </path>
                                                </svg>
                                                Beli Sekarang
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Course Features --}}
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="text-center p-4 bg-gray-50 rounded-lg">
                                <svg class="w-8 h-8 mx-auto text-primary-100 mb-2" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div class="text-sm font-medium text-gray-900">Sertifikat</div>
                                <div class="text-xs text-gray-600">Resmi</div>
                            </div>
                            <div class="text-center p-4 bg-gray-50 rounded-lg">
                                <svg class="w-8 h-8 mx-auto text-primary-100 mb-2" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <div class="text-sm font-medium text-gray-900">Fleksibel</div>
                                <div class="text-xs text-gray-600">24/7 Akses</div>
                            </div>
                            <div class="text-center p-4 bg-gray-50 rounded-lg">
                                <svg class="w-8 h-8 mx-auto text-primary-100 mb-2" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path
                                        d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z">
                                    </path>
                                </svg>
                                <div class="text-sm font-medium text-gray-900">Komunitas</div>
                                <div class="text-xs text-gray-600">Dukungan</div>
                            </div>
                            <div class="text-center p-4 bg-gray-50 rounded-lg">
                                <svg class="w-8 h-8 mx-auto text-primary-100 mb-2" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <div class="text-sm font-medium text-gray-900">Materi</div>
                                <div class="text-xs text-gray-600">Lengkap</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('style')
        <style>
            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .animate-fade-in {
                animation: fadeIn 0.6s ease-out;
            }

            .course-badge {
                background-color: rgba(235, 99, 29, 0.1);
                color: #eb631d;
                backdrop-filter: blur(10px);
            }
        </style>
    @endpush
@endsection
