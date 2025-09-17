@php
    use Illuminate\Support\Str;
@endphp
@extends('layouts.app')
@section('title', 'Course')
@section('content')
    <section id="courses" class="py-16 bg-gradient-to-b from-primary-50 to-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">





            <!-- Daftar Kursus -->
            <!-- Daftar Kursus -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6" id="course-list">
                @if ($courses->isEmpty())
                    <div class="col-span-1 sm:col-span-2 lg:col-span-4 text-center py-10 text-gray-500 text-lg">
                        Tidak ada kelas yang dibeli
                    </div>
                @else
                    @foreach ($courses as $course)
                        <div
                            class="course-card bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl hover:-translate-y-1 transform transition-shadow duration-300 group">
                            <div class="relative overflow-hidden">
                                <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}"
                                    class="w-full h-full object-contain bg-white group-hover:scale-105 transition-transform duration-500">
                                <span
                                    class="absolute top-3 left-3 bg-primary-100 text-white text-xs font-medium px-3 py-1 rounded-full shadow">
                                    {{ $course->nama_kategori ?? 'Umum' }}
                                </span>
                                <div
                                    class="absolute inset-0 bg-black bg-opacity-20 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                </div>
                            </div>
                            <div class="p-5">
                                <h3
                                    class="text-lg font-semibold text-primary-200 mb-2 line-clamp-2 group-hover:text-primary-100 transition-colors">
                                    {{ $course->title }}</h3>
                                <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ $course->description }}</p>

                                @php $features = json_decode($course->features, true); @endphp
                                @if (!empty($features) && count($features) > 0)
                                    <ul class="space-y-1 text-sm text-gray-700 mb-4">
                                        @foreach ($features as $feature)
                                            <li class="flex items-start">
                                                <svg class="w-4 h-4 text-primary-100 mr-2 mt-0.5 flex-shrink-0"
                                                    fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                        clip-rule="evenodd"></path>
                                                </svg>
                                                <span class="text-xs">{{ $feature }}</span>
                                            </li>
                                        @endforeach

                                    </ul>
                                @endif

                                <div class="flex items-center justify-between mt-4 pt-3 border-t border-gray-100">
                                    <span class="text-lg font-bold text-primary-200">
                                        Rp {{ number_format($course->price, 0, ',', '.') }}
                                    </span>

                                    <a href="{{ route('kelas.akses', Str::slug($course->title) . '-' . $course->id) }}"
                                        class="bg-primary-100 hover:bg-primary-200 text-white px-4 py-2 rounded-lg text-sm shadow-md hover:shadow-lg transition-all duration-300 flex items-center">
                                        <span>Akses Kelas</span>
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>

                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>


            <!-- Pagination (jika diperlukan) -->



        </div>
    </section>
@endsection

@push('js')
    <script>
        const buttons = document.querySelectorAll('.tab-btn');
        const cards = document.querySelectorAll('.course-card');

        buttons.forEach(btn => {
            btn.addEventListener('click', () => {
                buttons.forEach(b => {
                    b.classList.remove('active', 'bg-primary-100', 'text-white', 'shadow-lg');
                    b.classList.add('bg-white', 'text-primary-100', 'shadow-sm');
                });

                btn.classList.add('active', 'bg-primary-100', 'text-white', 'shadow-lg');
                btn.classList.remove('bg-white', 'text-primary-100', 'shadow-sm');

                let kategori = btn.dataset.kategori;
                cards.forEach(card => {
                    card.style.display = (kategori === 'all' || card.dataset.kategori ===
                        kategori) ? "block" : "none";
                });
            });
        });
    </script>
@endpush

@push('style')
    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Animasi untuk card saat muncul */
        .course-card {
            animation: fadeIn 0.5s ease-out forwards;
            opacity: 0;
            transform: translateY(10px);
        }

        @keyframes fadeIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Stagger animation untuk card */
        .course-card:nth-child(1) {
            animation-delay: 0.1s;
        }

        .course-card:nth-child(2) {
            animation-delay: 0.2s;
        }

        .course-card:nth-child(3) {
            animation-delay: 0.3s;
        }

        .course-card:nth-child(4) {
            animation-delay: 0.4s;
        }

        .course-card:nth-child(5) {
            animation-delay: 0.5s;
        }

        .course-card:nth-child(6) {
            animation-delay: 0.6s;
        }

        .course-card:nth-child(7) {
            animation-delay: 0.7s;
        }

        .course-card:nth-child(8) {
            animation-delay: 0.8s;
        }
    </style>
@endpush
