@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <section class="py-10 bg-primary-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Welcome Card -->
            <div class="bg-white rounded-xl shadow-md p-8 mb-10">
                <h1 class="text-3xl md:text-4xl font-bold text-primary-200 mb-4">
                    Selamat Datang, {{ Auth::user()->name }}!
                </h1>
                <p class="text-gray-700 text-lg">
                    Semoga harimu menyenangkan dan selamat belajar di KelasSatu.
                </p>
            </div>

            <!-- Courses Section -->
            <div class="mb-10">
                <!-- Heading -->
                <div class="text-center mb-10">
                    <h2 class="text-3xl md:text-4xl font-extrabold text-primary-200 mb-4 tracking-tight">
                        Temukan Kursus Terbaik
                    </h2>
                    <p class="text-lg md:text-xl text-gray-600 max-w-2xl mx-auto">
                        Tingkatkan skill Anda dengan pilihan kursus populer dari berbagai kategori.
                    </p>
                </div>

                <!-- Tabs Kategori -->
                <div class="flex flex-wrap justify-center gap-3 mb-8">
                    <button
                        class="tab-btn px-5 py-2 rounded-full border border-primary-100 text-primary-100 bg-white shadow-sm hover:shadow-lg hover:bg-primary-100 hover:text-white transition duration-300 ease-in-out active"
                        data-kategori="all">
                        Semua
                    </button>
                    @foreach ($kategori as $kat)
                        <button
                            class="tab-btn px-5 py-2 rounded-full border border-primary-100 text-primary-100 bg-white shadow-sm hover:shadow-lg hover:bg-primary-100 hover:text-white transition duration-300 ease-in-out"
                            data-kategori="{{ $kat->id }}">
                            {{ $kat->nama_kategori }}
                        </button>
                    @endforeach
                </div>

                <!-- Daftar Kursus -->
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6" id="course-list">
                    @foreach ($courses as $course)
                        <div class="course-card bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl hover:-translate-y-1 transform transition duration-300"
                            data-kategori="{{ $course->kategori_id }}">
                            <div class="relative">
                                <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}"
                                    class="w-full h-48 object-cover">
                                <span
                                    class="absolute top-3 left-3 bg-primary-100 text-white text-xs font-medium px-3 py-1 rounded-full shadow">
                                    {{ $course->nama_kategori ?? 'Umum' }}
                                </span>
                            </div>
                            <div class="p-5">
                                <h3 class="text-lg font-semibold text-primary-200 mb-2 line-clamp-2">{{ $course->title }}
                                </h3>
                                <p class="text-gray-600 text-sm mb-3 line-clamp-3">{{ $course->description }}</p>

                                @php $features = json_decode($course->features, true); @endphp
                                @if (!empty($features))
                                    <ul class="space-y-1 text-sm text-gray-700 mb-4">
                                        @foreach ($features as $feature)
                                            <li class="flex items-start">
                                                <span class="text-primary-100 mr-2">✔</span> {{ $feature }}
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif

                                <div class="flex items-center justify-between mt-4">
                                    <span class="text-xl font-bold text-primary-200">
                                        Rp {{ number_format($course->price, 0, ',', '.') }}
                                    </span>
                                    <a href="{{ route('course.detail', Str::slug($course->title) . '-' . $course->id) }}"
                                        class="bg-primary-100 hover:bg-primary-200 text-white px-4 py-2 rounded-lg text-sm shadow transition">
                                        Beli Sekarang
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
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
    </style>
@endpush
