@extends('layouts.app')
@section('title', 'Home')
@section('content')

    <!-- Enhanced Hero Section -->
    <section id="beranda"
        class="relative bg-gradient-to-br from-primary-200 via-primary-100 to-primary-100 min-h-screen flex items-center overflow-hidden">
        <!-- Background Pattern/Overlay -->
        <div class="absolute inset-0 bg-gradient-to-br from-primary-200/20 via-primary-100/10 to-primary-100/20"></div>
        <div class="absolute inset-0 bg-black/10"></div>

        <!-- Animated Background Elements -->
        <div class="absolute top-20 left-10 w-32 h-32 bg-primary-100/10 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute bottom-20 right-10 w-48 h-48 bg-primary-200/10 rounded-full blur-3xl animate-pulse delay-1000">
        </div>
        <div class="absolute top-1/2 left-1/3 w-24 h-24 bg-primary-100/10 rounded-full blur-2xl animate-pulse delay-2000">
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full relative z-10 py-16">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">

                <!-- Hero Text Content -->
                <div class="relative animate-fade-in text-left space-y-8 order-2 lg:order-1">
                    <!-- Badge/Tag -->
                    <div
                        class="inline-flex items-center px-4 py-2 rounded-full bg-white/20 border border-white/30 backdrop-blur-sm">
                        <span class="w-2 h-2 bg-white rounded-full mr-2 animate-pulse"></span>
                        <span class="text-white text-sm font-medium">Platform Pembelajaran #1 di Indonesia</span>
                    </div>

                    <!-- Main Title -->
                    <div class="space-y-4">
                        <h1
                            class="text-4xl sm:text-5xl md:text-6xl lg:text-5xl xl:text-6xl font-bold text-white leading-tight tracking-tight">
                            <span class="block animate-fade-in-up">
                                {{ $landingPage->hero_title ?? 'Belajar Tanpa Batas' }}
                            </span>
                            <span
                                class="block text-transparent bg-gradient-to-r from-white to-orange-100 bg-clip-text animate-fade-in-up delay-200 mt-2">
                                {{ $landingPage->hero_subtitle ?? 'Raih Masa Depan' }}
                            </span>
                        </h1>
                    </div>

                    <!-- Description -->
                    <p class="text-lg sm:text-xl text-white/90 max-w-2xl leading-relaxed animate-fade-in-up delay-300">
                        {{ $landingPage->hero_description ?? 'Nikmati Kemudahan lolos TES KEMAMPUAN AKADEMIK (TKA) melalui Kelas Premium Prediksi TKA 2025 dengan metode pembelajaran inovatif dan mentor berpengalaman.' }}
                    </p>



                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 animate-fade-in-up delay-500">
                        <a href="{{ route('login') }}"
                            class="group relative px-8 py-4 rounded-2xl text-lg font-semibold bg-primary-100 text-white hover:bg-primary-200 transition-all duration-300 transform hover:scale-105 hover:-translate-y-1 shadow-xl hover:shadow-2xl shadow-primary-100/25 text-center overflow-hidden">
                            <span class="relative z-10 flex items-center justify-center">
                                {{ $landingPage->hero_btn_primary ?? 'Mulai Belajar Sekarang' }}
                                <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform duration-300"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                </svg>
                            </span>
                            <div
                                class="absolute inset-0 bg-primary-200 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            </div>
                        </a>

                        <a href="#courses"
                            class="group px-8 py-4 rounded-2xl text-lg font-semibold border-2 border-white/30 text-white hover:bg-white/10 hover:border-white/50 transition-all duration-300 backdrop-blur-sm shadow-lg text-center">
                            <span class="flex items-center justify-center">
                                {{ $landingPage->hero_btn_secondary ?? 'Jelajahi Kursus' }}
                                <svg class="w-5 h-5 ml-2 group-hover:rotate-45 transition-transform duration-300"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                                    </path>
                                </svg>
                            </span>
                        </a>
                    </div>
                </div>

                <!-- Hero Image/Slider Section -->
                <!-- Hero Image/Slider Section -->
                <div class="relative animate-fade-in-up delay-300 order-1 lg:order-2 mb-8 lg:mb-0">
                    <div class="relative max-w-lg mx-auto w-full">
                        <!-- Main Image Container -->
                        <div
                            class="relative rounded-3xl overflow-hidden shadow-2xl backdrop-blur-sm bg-white/5 border border-white/20">

                            <!-- Image Slider -->
                            <div class="image-slider relative">
                                <div class="slide active relative">
                                    <img src="{{ $landingPage->hero_image_1
                                        ? asset('storage/' . $landingPage->hero_image_1)
                                        : 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=1200&q=80' }}"
                                        alt="Students learning"
                                        class="w-full h-auto object-contain transition-all duration-700">

                                    <!-- Overlay Gradient -->
                                    <div
                                        class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent">
                                    </div>


                                </div>

                                <div class="slide relative">
                                    <img src="{{ $landingPage->hero_image_2 ? asset('storage/' . $landingPage->hero_image_2) : 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=1200&q=80' }}"
                                        alt="Online learning"
                                        class="w-full h-full object-cover transition-all duration-700">


                                    <div
                                        class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent">
                                    </div>

                                </div>

                                <div class="slide relative">
                                    <img src="{{ $landingPage->hero_image_3 ? asset('storage/' . $landingPage->hero_image_3) : 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=1200&q=80' }}"
                                        alt="Online learning"
                                        class="w-full h-full object-cover transition-all duration-700">


                                    <div
                                        class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent">
                                    </div>

                                </div>
                            </div>

                            <!-- Navigation Dots -->
                            <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-3">
                                <button
                                    class="slider-dot active w-3 h-3 rounded-full bg-white transition-all duration-300 hover:scale-125"
                                    onclick="currentSlide(1)"></button>
                                <button
                                    class="slider-dot w-3 h-3 rounded-full bg-white/50 transition-all duration-300 hover:scale-125"
                                    onclick="currentSlide(2)"></button>
                                <button
                                    class="slider-dot w-3 h-3 rounded-full bg-white/50 transition-all duration-300 hover:scale-125"
                                    onclick="currentSlide(3)"></button>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>

        <!-- Scroll Indicator -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
            <div class="w-6 h-10 border-2 border-white/30 rounded-full flex justify-center">
                <div class="w-1 h-3 bg-white rounded-full mt-2 animate-pulse"></div>
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
                                class="text-lg font-semibold leading-none text-primary-200 mb-2 group-hover:text-primary-100 transition-colors">
                                {{ $course->title }}
                            </h3>
                            <p class="text-gray-600 text-sm mb-3">
                                {{ Str::limit($course->description, 100) }}
                            </p>

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

    <section id="about" class="py-20 bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-2 gap-12 items-stretch">
            <!-- Illustration Image -->
            <div class="flex">
                <img src="{{ asset('storage/' . $landingPage->about_image ?? 'image/logo.png') }}"
                    alt="How to Join Illustration" class="w-full h-full object-contain rounded-xl shadow-md">
            </div>

            <!-- How to Join Steps -->
            <div class="flex flex-col justify-center">
                <h2 class="text-3xl md:text-3xl font-bold text-primary-200 mb-6">
                    {{ $landingPage->about_title ?? 'Selamat Datang di Kelassatu.com' }}
                </h2>
                <div class="space-y-1.5">
                    <p class="text-lg text-gray-600">
                        {{ $landingPage->about_paragraph_1 ?? 'kelassatu.com adalah platform pembelajaran daring yang menyatukan siswa, mahasiswa, calon guru, guru, hingga para pencari kerja dalam satu ruang belajar yang inklusif. Dengan semangat "satu kelas untuk semua", kelassatu.com menembus batas jenjang pendidikan dan mempertemukan lintas generasi untuk saling berbagi ilmu, pengalaman, dan inspirasi.' }}
                    </p>
                    <p class="text-lg text-gray-600">
                        {{ $landingPage->about_paragraph_2 ?? 'Di sini, setiap orang memiliki kesempatan yang sama untuk berkembang—dalam pengetahuan, keterampilan, maupun karakter—melalui pembelajaran yang interaktif, kolaboratif, dan relevan dengan kebutuhan zaman.' }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section id="why" class="py-5 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-5">
                <h2 class="text-3xl md:text-4xl font-bold text-primary-200 mb-4">
                    {{ $landingPage->features_title ?? 'Mengapa Memilih KelasSatu?' }}</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    {{ $landingPage->features_subtitle ?? 'Kami menyediakan pengalaman belajar terbaik dengan fitur-fitur unggulan' }}
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                @foreach ($featurespage as $feature)
                    @if (is_object($feature) && isset($feature->title))
                        <div class="text-center p-6 rounded-xl hover:shadow-lg transition-shadow">
                            <div
                                class="w-16 h-16 bg-primary-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-primary-200" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v12m0-12c-1.2-.8-2.8-1.3-4.5-1.3S4.2 5.2 3 6v12c1.2-.8 2.8-1.3 4.5-1.3S10.8 17.2 12 18m0-12c1.2-.8 2.8-1.3 4.5-1.3S19.8 5.2 21 6v12c-1.2-.8-2.8-1.3-4.5-1.3S13.2 17.2 12 18" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-primary-200 mb-2">{{ $feature->title }}</h3>
                            <p class="text-gray-600">{{ $feature->description }}</p>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-20 bg-primary-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8 text-center text-white">
                <div>
                    <div class="text-4xl font-bold mb-2">{{ $landingPage->stats_students_count ?? '50,000+' }}</div>
                    <div class="text-primary-500">{{ $landingPage->stats_students_label ?? 'Siswa Aktif' }}</div>
                </div>
                <div>
                    <div class="text-4xl font-bold mb-2">{{ $landingPage->stats_courses_count ?? '200+' }}</div>
                    <div class="text-primary-500">{{ $landingPage->stats_courses_label ?? 'Kursus Tersedia' }}</div>
                </div>
                <div>
                    <div class="text-4xl font-bold mb-2">{{ $landingPage->stats_satisfaction_count ?? '95%' }}</div>
                    <div class="text-primary-500">{{ $landingPage->stats_satisfaction_label ?? 'Tingkat Kepuasan' }}</div>
                </div>
                <div>
                    <div class="text-4xl font-bold mb-2">{{ $landingPage->stats_support_count ?? '24/7' }}</div>
                    <div class="text-primary-500">{{ $landingPage->stats_support_label ?? 'Dukungan' }}</div>
                </div>
            </div>
        </div>
    </section>

    <section id="how-to-join" class="py-20 bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <!-- Illustration Image -->
            <div class="flex justify-center">
                <img src="{{ asset('image/join1.jpg') }}" alt="How to Join Illustration"
                    class="w-full max-w-sm md:max-w-md lg:max-w-lg h-auto rounded-xl shadow-md">
            </div>

            <!-- How to Join Steps -->
            <div>
                <h2 class="text-3xl md:text-4xl font-bold text-primary-200 mb-6">How to Join?</h2>
                <p class="text-lg text-gray-600 mb-8">Ikuti langkah mudah berikut untuk mulai belajar di <span
                        class="font-semibold text-primary-200">KelasSatu</span>:</p>
                <ol class="space-y-6">
                    <li class="flex items-start">
                        <span
                            class="flex items-center justify-center w-10 h-10 rounded-full bg-primary-200 text-white font-bold text-lg mr-4 flex-shrink-0">
                            1
                        </span>
                        <p class="text-gray-700 pt-2"><strong>Buat Akun:</strong> Daftar gratis dengan email atau akun
                            Google.</p>
                    </li>
                    <li class="flex items-start">
                        <span
                            class="flex items-center justify-center w-10 h-10 rounded-full bg-primary-200 text-white font-bold text-lg mr-4 flex-shrink-0">
                            2
                        </span>
                        <p class="text-gray-700 pt-2"><strong>Pilih Kursus:</strong> Jelajahi kursus sesuai kebutuhan dan
                            minat Anda.</p>
                    </li>
                    <li class="flex items-start">
                        <span
                            class="flex items-center justify-center w-10 h-10 rounded-full bg-primary-200 text-white font-bold text-lg mr-4 flex-shrink-0">
                            3
                        </span>
                        <p class="text-gray-700 pt-2"><strong>Lakukan Pembayaran:</strong> Amankan kursus pilihan dengan
                            metode pembayaran mudah.</p>
                    </li>
                    <li class="flex items-start">
                        <span
                            class="flex items-center justify-center w-10 h-10 rounded-full bg-primary-200 text-white font-bold text-lg mr-4 flex-shrink-0">
                            4
                        </span>
                        <p class="text-gray-700 pt-2"><strong>Mulai Belajar:</strong> Akses materi kapan saja dan nikmati
                            pengalaman belajar interaktif.</p>
                    </li>
                </ol>
            </div>
        </div>
    </section>

    <!-- Testimonial Section -->
    <!-- Testimonial Section -->
    <section id="testimoni" class="py-5 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10">
                <h2 class="text-3xl md:text-4xl font-bold text-primary-200 mb-4">
                    {{ $landingPage->testimonial_title ?? 'Apa Kata Mereka?' }}
                </h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    {{ $landingPage->testimonial_subtitle ?? 'Testimoni dari siswa yang telah merasakan pengalaman belajar di KelasSatu' }}
                </p>
            </div>

            <!-- Swiper -->
            <div class="swiper testimonial-swiper">
                <div class="swiper-wrapper">
                    @foreach ($testimonials as $testimonial)
                        <div class="swiper-slide">
                            <div class="testimonial-card bg-primary-50 p-6 rounded-xl">
                                <div class="flex items-center mb-4">
                                    <div class="flex-shrink-0">
                                        <img src="{{ asset('image/user.png') }}" alt="{{ $testimonial->name }}"
                                            class="testimonial-avatar rounded-full">
                                    </div>
                                    <div class="ml-4">
                                        <h4 class="font-semibold text-primary-200">{{ $testimonial->name }}</h4>
                                        <p class="text-sm text-gray-600">{{ $testimonial->role }}</p>
                                    </div>
                                </div>
                                <div class="relative">
                                    <svg class="w-8 h-8 quote-icon absolute -left-2 -top-2" fill="currentColor"
                                        viewBox="0 0 24 24">
                                        <path
                                            d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
                                    </svg>
                                    <p class="text-gray-700 relative z-10">
                                        "{{ $testimonial->content }}"
                                    </p>
                                </div>
                                <div class="flex mt-4">
                                    @for ($i = 0; $i < 5; $i++)
                                        <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                            <path
                                                d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                        </svg>
                                    @endfor
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="swiper-pagination mt-6"></div>
            </div>
        </div>
    </section>


    <section id="faq" class="py-20 bg-primary-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-primary-200 mb-4">
                    {{ $landingPage->faq_title ?? 'Pertanyaan yang Sering Diajukan' }}</h2>
                <p class="text-lg text-gray-600">
                    {{ $landingPage->faq_subtitle ?? 'Temukan jawaban dari pertanyaan umum mengenai KelasSatu' }}</p>
            </div>

            <div class="space-y-4">
                @foreach ($faqs as $index => $faq)
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <button onclick="toggleFaq({{ $index + 1 }})"
                            class="w-full flex justify-between items-center p-4 text-left font-medium text-primary-200 focus:outline-none">
                            {{ $faq->question }}
                            <svg id="icon-{{ $index + 1 }}"
                                class="w-5 h-5 transform transition-transform duration-300" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                                </path>
                            </svg>
                        </button>
                        <div id="answer-{{ $index + 1 }}" class="hidden p-4 text-gray-600 bg-white">
                            {{ $faq->answer }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    <!-- Floating WhatsApp Button -->

    <div id="whatsapp-fab" class="fixed bottom-6 right-6 z-50">
        <a href="https://wa.me/628991111901" target="_blank"
            class="flex items-center justify-center w-14 h-14 bg-green-500 hover:bg-green-600 text-white rounded-full shadow-lg hover:shadow-xl transform hover:scale-110 transition-all duration-300 group">
            <!-- WhatsApp Icon -->
            <svg class="w-7 h-7 group-hover:scale-110 transition-transform duration-200" fill="currentColor"
                viewBox="0 0 24 24">
                <path
                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488" />
            </svg>

            <!-- Pulse Animation -->
            <div class="absolute inset-0 bg-green-500 rounded-full animate-ping opacity-20"></div>
        </a>

        <!-- Tooltip (Optional) -->
        <div
            class="absolute right-16 top-1/2 transform -translate-y-1/2 bg-gray-800 text-white text-sm px-3 py-2 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap pointer-events-none">
            Chat via WhatsApp
            <div
                class="absolute top-1/2 right-0 transform translate-x-full -translate-y-1/2 border-4 border-transparent border-l-gray-800">
            </div>
        </div>
    </div>


@endsection
@push('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
    <style>
        /* Enhanced Animations */
        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fade-in-up {
            from {
                opacity: 0;
                transform: translateY(40px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fade-in 1s ease-out;
        }

        .animate-fade-in-up {
            animation: fade-in-up 0.8s ease-out;
        }

        .delay-200 {
            animation-delay: 0.2s;
        }

        .delay-300 {
            animation-delay: 0.3s;
        }

        .delay-400 {
            animation-delay: 0.4s;
        }

        .delay-500 {
            animation-delay: 0.5s;
        }

        .delay-1000 {
            animation-delay: 1s;
        }

        .delay-2000 {
            animation-delay: 2s;
        }

        /* Image Slider Styles */
        .image-slider .slide {
            display: none;
        }

        .image-slider .slide.active {
            display: block;
        }

        .slider-dot.active {
            background-color: white;
            transform: scale(1.2);
        }

        /* Gradient Background */
        .gradient-bg {
            background: linear-gradient(135deg,
                    #1e3a8a 0%,
                    #3730a3 25%,
                    #7c2d12 50%,
                    #ea580c 75%,
                    #f59e0b 100%);
        }

        /* Glass Effect */
        .backdrop-blur-sm {
            backdrop-filter: blur(8px);
        }

        /* Enhanced Button Hover Effects */
        .group:hover .group-hover\:translate-x-1 {
            transform: translateX(0.25rem);
        }

        .group:hover .group-hover\:rotate-45 {
            transform: rotate(45deg);
        }

        /* Text Gradient */
        .bg-clip-text {
            -webkit-background-clip: text;
            background-clip: text;
        }

        /* Responsive Improvements */
        @media (max-width: 768px) {
            .animate-fade-in-up {
                animation-delay: 0s;
            }

            .delay-200,
            .delay-300,
            .delay-400,
            .delay-500 {
                animation-delay: 0s;
            }
        }

        #whatsapp-fab a:hover {
            box-shadow: 0 8px 25px rgba(34, 197, 94, 0.4);
        }

        /* Responsive untuk mobile */
        @media (max-width: 768px) {
            #whatsapp-fab {
                bottom: 20px;
                right: 20px;
            }

            #whatsapp-fab a {
                width: 56px;
                height: 56px;
            }

            #whatsapp-fab svg {
                width: 28px;
                height: 28px;
            }
        }

        /* Hide tooltip on mobile */
        @media (max-width: 640px) {
            #whatsapp-fab .absolute.right-16 {
                display: none;
            }
        }
    </style>
@endpush
@push('js')
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script>
        let slideIndex = 0;
        showSlides();

        function showSlides() {
            let slides = document.querySelectorAll(".image-slider .slide");
            let dots = document.querySelectorAll(".slider-dot");
            slides.forEach((s, i) => {
                s.style.display = "none";
                dots[i].classList.remove("active", "bg-white");
                dots[i].classList.add("bg-white/50");
            });
            slideIndex++;
            if (slideIndex > slides.length) {
                slideIndex = 1
            }
            slides[slideIndex - 1].style.display = "block";
            dots[slideIndex - 1].classList.add("active", "bg-white");
            dots[slideIndex - 1].classList.remove("bg-white/50");
            setTimeout(showSlides, 5000); // ganti slide tiap 5 detik
        }

        function currentSlide(n) {
            slideIndex = n - 1; // reset ke index tertentu
            showSlides();
        }
    </script>

    <script>
        const testimonialSwiper = new Swiper('.testimonial-swiper', {
            loop: true,
            spaceBetween: 20,
            slidesPerView: 1,
            breakpoints: {
                640: {
                    slidesPerView: 1
                },
                768: {
                    slidesPerView: 2
                },
                1024: {
                    slidesPerView: 3
                },
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
        });

        function toggleFaq(index) {
            const answer = document.getElementById('answer-' + index);
            const icon = document.getElementById('icon-' + index);

            answer.classList.toggle('hidden');
            icon.classList.toggle('rotate-45');
        }
    </script>
@endpush
