@extends('layouts.app')
@section('title', 'Home')
@section('content')
    <!-- Hero Section with Slider -->
    <section class="relative gradient-bg min-h-screen flex items-center">
        <div class="absolute inset-0 overflow-hidden">
            <div class="slider-container relative w-full h-full">
                <!-- Slide 1 -->
                <div class="slide absolute inset-0 transition-opacity duration-1000 opacity-100">
                    <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80"
                        alt="Students learning" class="w-full h-full object-cover opacity-20">
                </div>

                <!-- Slide 2 -->
                <div class="slide absolute inset-0 transition-opacity duration-1000 opacity-0">
                    <img src="https://images.unsplash.com/photo-1434030216411-0b793f4b4173?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80"
                        alt="Online learning" class="w-full h-full object-cover opacity-20">
                </div>

                <!-- Slide 3 -->
                <div class="slide absolute inset-0 transition-opacity duration-1000 opacity-0">
                    <img src="https://images.unsplash.com/photo-1553028826-f4804a6dba3b?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80"
                        alt="Technology learning" class="w-full h-full object-cover opacity-20">
                </div>
            </div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="animate-fade-in">
                <h1 class="text-4xl md:text-6xl font-bold text-white mb-6">
                    Belajar Tanpa Batas
                    <span class="block text-primary-100">Raih Masa Depan</span>
                </h1>
                <p class="text-xl md:text-2xl text-gray-200 mb-8 max-w-3xl mx-auto">
                    Nikmati Kemudahan Belajar Olimpiade Sains Nasional (OSN) melalui Rekaman Pembelajaran yang berisi
                    Puluhan Materi berbentuk Video dalam durasi Puluhan Jam dari Kelas Satu
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('login') }}"
                        class="btn-primary px-8 py-3 rounded-lg text-lg font-semibold transition-all transform hover:scale-105">
                        Mulai Belajar Sekarang
                    </a>
                    <button class="btn-outline px-8 py-3 rounded-lg text-lg font-semibold hover:text-white transition-all">
                        Jelajahi Kursus
                    </button>
                </div>
            </div>
        </div>

        <!-- Slider Navigation -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 flex space-x-2">
            <button class="slider-dot w-3 h-3 rounded-full bg-white opacity-100" onclick="currentSlide(1)"></button>
            <button class="slider-dot w-3 h-3 rounded-full bg-white opacity-50" onclick="currentSlide(2)"></button>
            <button class="slider-dot w-3 h-3 rounded-full bg-white opacity-50" onclick="currentSlide(3)"></button>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-primary-200 mb-4">Mengapa Memilih KelasSatu?</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">Kami menyediakan pengalaman belajar terbaik dengan
                    fitur-fitur unggulan</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center p-6 rounded-xl hover:shadow-lg transition-shadow">
                    <div class="w-16 h-16 bg-primary-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-primary-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-primary-200 mb-2">Kurikulum Terstruktur</h3>
                    <p class="text-gray-600">Materi pembelajaran yang disusun sistematis untuk memaksimalkan pemahaman
                    </p>
                </div>

                <div class="text-center p-6 rounded-xl hover:shadow-lg transition-shadow">
                    <div class="w-16 h-16 bg-primary-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-primary-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-primary-200 mb-2">Sertifikat Resmi</h3>
                    <p class="text-gray-600">Dapatkan sertifikat yang diakui industri setelah menyelesaikan kursus</p>
                </div>

                <div class="text-center p-6 rounded-xl hover:shadow-lg transition-shadow">
                    <div class="w-16 h-16 bg-primary-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-primary-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-primary-200 mb-2">Mentoring 24/7</h3>
                    <p class="text-gray-600">Bimbingan langsung dari instruktur berpengalaman kapan saja dibutuhkan</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Popular Courses Section -->
    <section id="courses" class="py-20 bg-primary-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-primary-200 mb-4">Kursus Populer</h2>
                <p class="text-xl text-gray-600">Pilih dari berbagai kursus yang paling diminati</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6" id="course-list">
                @foreach ($courses as $course)
                    <div
                        class="course-card bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl hover:-translate-y-1 transform transition-shadow duration-300 group">
                        <div class="relative overflow-hidden">
                            <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}"
                                class="w-full h-44 object-cover group-hover:scale-105 transition-transform duration-300">
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
                                    @foreach (array_slice($features, 0, 2) as $feature)
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
                                    @if (count($features) > 2)
                                        <li class="text-xs text-gray-500">+{{ count($features) - 2 }} fitur lainnya</li>
                                    @endif
                                </ul>
                            @endif

                            <div class="flex items-center justify-between mt-4 pt-3 border-t border-gray-100">
                                <span class="text-lg font-bold text-primary-200">
                                    Rp {{ number_format($course->price, 0, ',', '.') }}
                                </span>


                                <a href="{{ route('course.detail', Str::slug($course->title) . '-' . $course->id) }}"
                                    class="bg-primary-100 hover:bg-primary-200 text-white px-4 py-2 rounded-lg text-sm shadow-md hover:shadow-lg transition-all duration-300 flex items-center">
                                    <span>Beli Sekarang</span>
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>

                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-20 bg-primary-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8 text-center text-white">
                <div>
                    <div class="text-4xl font-bold mb-2">50,000+</div>
                    <div class="text-blue-100">Siswa Aktif</div>
                </div>
                <div>
                    <div class="text-4xl font-bold mb-2">200+</div>
                    <div class="text-blue-100">Kursus Tersedia</div>
                </div>
                <div>
                    <div class="text-4xl font-bold mb-2">95%</div>
                    <div class="text-blue-100">Tingkat Kepuasan</div>
                </div>
                <div>
                    <div class="text-4xl font-bold mb-2">24/7</div>
                    <div class="text-blue-100">Dukungan</div>
                </div>
            </div>
        </div>
    </section>

    <section id="how-to-join" class="py-20 bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-2 gap-12 items-center">

            <!-- Lottie Animation -->
            <div class="flex justify-center">
                <lottie-player src="https://assets1.lottiefiles.com/packages/lf20_vyLwnL.json" background="transparent"
                    speed="1" style="width:100%; max-width:420px; height:auto;" loop autoplay>
                </lottie-player>
            </div>

            <!-- How to Join Steps -->
            <div>
                <h2 class="text-3xl md:text-4xl font-bold text-primary-200 mb-6">How to Join?</h2>
                <p class="text-lg text-gray-600 mb-8">Ikuti langkah mudah berikut untuk mulai belajar di <span
                        class="font-semibold text-primary-200">KelasSatu</span>:</p>
                <ol class="space-y-6">
                    <li class="flex items-start">
                        <span
                            class="flex items-center justify-center w-10 aspect-square rounded-full bg-primary-200 text-white font-bold tabular-nums mr-4">
                            1
                        </span>

                        <p class="text-gray-700"><strong>Buat Akun:</strong> Daftar gratis dengan email atau akun
                            Google.</p>
                    </li>
                    <li class="flex items-start">
                        <span
                            class="flex items-center justify-center w-10 aspect-square rounded-full bg-primary-200 text-white font-bold tabular-nums mr-4">
                            2
                        </span>

                        <p class="text-gray-700"><strong>Pilih Kursus:</strong> Jelajahi kursus sesuai kebutuhan dan
                            minat Anda.</p>
                    </li>
                    <li class="flex items-start">
                        <span
                            class="flex items-center justify-center w-10 aspect-square rounded-full bg-primary-200 text-white font-bold tabular-nums mr-4">
                            3
                        </span>

                        <p class="text-gray-700"><strong>Lakukan Pembayaran:</strong> Amankan kursus pilihan dengan
                            metode pembayaran mudah.</p>
                    </li>
                    <li class="flex items-start">
                        <span
                            class="flex items-center justify-center w-10 aspect-square rounded-full bg-primary-200 text-white font-bold tabular-nums mr-4">
                            4
                        </span>

                        <p class="text-gray-700"><strong>Mulai Belajar:</strong> Akses materi kapan saja dan nikmati
                            pengalaman belajar interaktif.</p>
                    </li>
                </ol>
            </div>
        </div>
    </section>


    <!-- Testimonial Section -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-primary-200 mb-4">Apa Kata Mereka?</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">Testimoni dari siswa yang telah merasakan pengalaman
                    belajar di KelasSatu</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Testimonial 1 -->
                <div class="testimonial-card bg-primary-50 p-6 rounded-xl">
                    <div class="flex items-center mb-4">
                        <div class="flex-shrink-0">
                            <img src="{{ asset('image/user.png') }}" alt="Ahmad Rizki"
                                class="testimonial-avatar rounded-full">
                        </div>
                        <div class="ml-4">
                            <h4 class="font-semibold text-primary-200">Ahmad Rizki</h4>
                            <p class="text-sm text-gray-600">UI/UX Designer</p>
                        </div>
                    </div>
                    <div class="relative">
                        <svg class="w-8 h-8 quote-icon absolute -left-2 -top-2" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
                        </svg>
                        <p class="text-gray-700 relative z-10">
                            "Kursus UI/UX Design di KelasSatu sangat membantu karir saya. Materi yang disampaikan mudah
                            dipahami dan mentor selalu siap membantu."
                        </p>
                    </div>
                    <div class="flex mt-4">
                        <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                            <path
                                d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                        </svg>
                        <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                            <path
                                d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                        </svg>
                        <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                            <path
                                d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                        </svg>
                        <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                            <path
                                d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                        </svg>
                        <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                            <path
                                d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                        </svg>
                    </div>
                </div>

                <!-- Testimonial 2 -->
                <div class="testimonial-card bg-primary-50 p-6 rounded-xl">
                    <div class="flex items-center mb-4">
                        <div class="flex-shrink-0">
                            <img src="{{ asset('image/user.png') }}" alt="Sari Dewi"
                                class="testimonial-avatar rounded-full">
                        </div>
                        <div class="ml-4">
                            <h4 class="font-semibold text-primary-200">Sari Dewi</h4>
                            <p class="text-sm text-gray-600">Data Analyst</p>
                        </div>
                    </div>
                    <div class="relative">
                        <svg class="w-8 h-8 quote-icon absolute -left-2 -top-2" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
                        </svg>
                        <p class="text-gray-700 relative z-10">
                            "Sebagai pemula di bidang data science, saya sangat terbantu dengan kursus di KelasSatu.
                            Penjelasannya step by step dan projectnya relevan dengan industri."
                        </p>
                    </div>
                    <div class="flex mt-4">
                        <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                            <path
                                d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                        </svg>
                        <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                            <path
                                d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                        </svg>
                        <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                            <path
                                d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                        </svg>
                        <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                            <path
                                d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                        </svg>
                        <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                            <path
                                d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                        </svg>
                    </div>
                </div>

                <!-- Testimonial 3 -->
                <div class="testimonial-card bg-primary-50 p-6 rounded-xl">
                    <div class="flex items-center mb-4">
                        <div class="flex-shrink-0">
                            <img src="{{ asset('image/user.png') }}" alt="Budi Santoso"
                                class="testimonial-avatar rounded-full">
                        </div>
                        <div class="ml-4">
                            <h4 class="font-semibold text-primary-200">Budi Santoso</h4>
                            <p class="text-sm text-gray-600">Fullstack Developer</p>
                        </div>
                    </div>
                    <div class="relative">
                        <svg class="w-8 h-8 quote-icon absolute -left-2 -top-2" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
                        </svg>
                        <p class="text-gray-700 relative z-10">
                            "Setelah menyelesaikan kursus Fullstack Development, skill coding saya meningkat drastis.
                            Sekarang saya lebih percaya diri mengerjakan project kompleks."
                        </p>
                    </div>
                    <div class="flex mt-4">
                        <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                            <path
                                d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                        </svg>
                        <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                            <path
                                d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                        </svg>
                        <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                            <path
                                d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                        </svg>
                        <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                            <path
                                d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                        </svg>
                        <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                            <path
                                d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Testimonial Navigation -->
            <div class="flex justify-center mt-12 space-x-2">
                <button class="w-3 h-3 rounded-full bg-primary-100"></button>
                <button class="w-3 h-3 rounded-full bg-gray-300"></button>
                <button class="w-3 h-3 rounded-full bg-gray-300"></button>
            </div>
        </div>
    </section>

    <section id="faq" class="py-20 bg-primary-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-primary-200 mb-4">Pertanyaan yang Sering Diajukan</h2>
                <p class="text-lg text-gray-600">Temukan jawaban dari pertanyaan umum mengenai KelasSatu</p>
            </div>

            <div class="space-y-4">
                <!-- FAQ Item 1 -->
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <button onclick="toggleFaq(1)"
                        class="w-full flex justify-between items-center p-4 text-left font-medium text-primary-200 focus:outline-none">
                        Apa itu KelasSatu?
                        <svg id="icon-1" class="w-5 h-5 transform transition-transform duration-300" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                            </path>
                        </svg>
                    </button>
                    <div id="answer-1" class="hidden p-4 text-gray-600 bg-white">
                        KelasSatu adalah platform e-learning yang menyediakan berbagai kursus online dengan mentor
                        profesional.
                    </div>
                </div>

                <!-- FAQ Item 2 -->
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <button onclick="toggleFaq(2)"
                        class="w-full flex justify-between items-center p-4 text-left font-medium text-primary-200 focus:outline-none">
                        Apakah saya mendapatkan sertifikat setelah menyelesaikan kursus?
                        <svg id="icon-2" class="w-5 h-5 transform transition-transform duration-300" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                            </path>
                        </svg>
                    </button>
                    <div id="answer-2" class="hidden p-4 text-gray-600 bg-white">
                        Ya, setiap kursus yang diselesaikan akan memberikan sertifikat resmi yang dapat digunakan untuk
                        keperluan profesional.
                    </div>
                </div>

                <!-- FAQ Item 3 -->
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <button onclick="toggleFaq(3)"
                        class="w-full flex justify-between items-center p-4 text-left font-medium text-primary-200 focus:outline-none">
                        Apakah saya bisa mengakses kursus seumur hidup?
                        <svg id="icon-3" class="w-5 h-5 transform transition-transform duration-300" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                            </path>
                        </svg>
                    </button>
                    <div id="answer-3" class="hidden p-4 text-gray-600 bg-white">
                        Ya, setelah membeli kursus, Anda dapat mengakses materi kursus tersebut kapan saja tanpa batasan
                        waktu.
                    </div>
                </div>

                <!-- FAQ Item 4 -->
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <button onclick="toggleFaq(4)"
                        class="w-full flex justify-between items-center p-4 text-left font-medium text-primary-200 focus:outline-none">
                        Bagaimana cara mendaftar kursus?
                        <svg id="icon-4" class="w-5 h-5 transform transition-transform duration-300" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                            </path>
                        </svg>
                    </button>
                    <div id="answer-4" class="hidden p-4 text-gray-600 bg-white">
                        Anda dapat mendaftar dengan membuat akun, lalu pilih kursus yang diinginkan dan lakukan
                        pembayaran sesuai instruksi.
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
@push('js')
@endpush
