@extends('layouts.app')

@section('title', $course->title)

@section('content')
    <div class="min-h-screen bg-gray-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

            {{-- Notifikasi --}}
            @foreach (['success', 'error', 'info'] as $msg)
                @if (session($msg))
                    @php
                        $colors = ['success' => 'green', 'error' => 'red', 'info' => 'blue'];
                    @endphp
                    <div
                        class="bg-{{ $colors[$msg] }}-100 border-l-4 border-{{ $colors[$msg] }}-500 text-{{ $colors[$msg] }}-700 p-4 mb-6 rounded shadow">
                        {{ session($msg) }}
                    </div>
                @endif
            @endforeach

            {{-- Breadcrumb --}}
            <nav class="flex mb-6 text-gray-600 text-sm" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-2">
                    <li>
                        <a href="{{ route('dashboardUser') }}" class="hover:text-gray-900 transition">Beranda</a>
                    </li>
                    <li>/</li>
                    <li>
                        <a href="{{ route('course') }}" class="hover:text-gray-900 transition">Kursus</a>
                    </li>
                    <li>/</li>
                    <li class="text-gray-400">{{ Str::limit($course->title, 30) }}</li>
                </ol>
            </nav>

            {{-- Main Content --}}
            <div class="bg-white rounded-2xl shadow p-8 animate-fade-in">

                {{-- Header --}}
                <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $course->title }}</h1>

                {{-- Deskripsi --}}
                <section class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Deskripsi Kursus</h3>
                    <p class="text-gray-700 leading-relaxed">{{ $course->description }}</p>
                </section>

                {{-- Yang Akan Dipelajari --}}
                <section class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Yang Akan Anda Pelajari</h3>
                    <div class="grid md:grid-cols-2 gap-3">
                        @php $features = json_decode($course->features, true) ?? []; @endphp
                        @forelse($features as $feature)
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-green-500 mr-3 mt-1 flex-shrink-0" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-gray-700">{{ $feature }}</span>
                            </div>
                        @empty
                            <div class="text-gray-500 col-span-2">Belum ada fitur yang ditambahkan.</div>
                        @endforelse
                    </div>
                </section>

                {{-- Harga & Aksi --}}
                <div
                    class="bg-gray-50 rounded-xl p-6 mb-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="text-2xl font-bold text-gray-900">
                        @if ($course->is_free)
                            GRATIS
                        @else
                            Rp {{ number_format($course->price, 0, ',', '.') }}
                        @endif
                    </div>

                    <div class="w-full sm:w-auto">
                        @php
                            $user = auth()->user();
                            $hasAccess = DB::table('enrollments')
                                ->where('user_id', $user->id)
                                ->where('course_id', $course->id)
                                ->exists();
                        @endphp

                        @if ($hasAccess)
                            <a href="{{ route('kelas.index') }}" class="btn-primary w-full sm:w-auto">Masuk ke Kursus</a>
                        @elseif($course->is_free)
                            <form action="{{ route('payment.index', Crypt::encryptString($course->id)) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-primary w-full sm:w-auto">Dapatkan Kursus Gratis</button>
                            </form>
                        @else
                            <a href="{{ route('payment.index', Crypt::encryptString($course->id)) }}"
                                class="btn-primary w-full sm:w-auto">Beli Sekarang</a>
                        @endif
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

            .btn-primary {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                background-color: #3b82f6;
                color: white;
                padding: 0.75rem 1.5rem;
                border-radius: 0.75rem;
                font-weight: 500;
                transition: all 0.2s;
            }

            .btn-primary:hover {
                background-color: #2563eb;
                transform: scale(1.05);
            }
        </style>
    @endpush
@endsection
