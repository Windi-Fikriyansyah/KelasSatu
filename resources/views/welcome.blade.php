@extends('layouts.app')
@section('title', 'Home')
@section('content')
    <!-- Hero Section with Slider -->
    <section id="beranda" class="relative gradient-bg h-[85vh] flex items-center">
        <div class="absolute inset-0 overflow-hidden">
            <div class="slider-container relative w-full h-full">
                <!-- Slide 1 -->
                <div class="slide absolute inset-0 transition-opacity duration-1000 opacity-100">
                    <img src="{{ $landingPage->hero_image_1
                        ? asset('storage/' . $landingPage->hero_image_1)
                        : 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80' }}"
                        alt="Students learning" class="w-full h-full object-cover">
                    <!-- Overlay Orange Transparan -->
                    <div class="absolute inset-0 bg-orange-500 bg-opacity-40"></div>
                </div>

                <!-- Slide 2 -->
                <div class="slide absolute inset-0 transition-opacity duration-1000 opacity-0">
                    <img src="{{ asset('storage/' . $landingPage->hero_image_2) ?? 'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80' }}"
                        alt="Online learning" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-orange-500 bg-opacity-40"></div>
                </div>

                <!-- Slide 3 -->
                <div class="slide absolute inset-0 transition-opacity duration-1000 opacity-0">
                    <img src="{{ asset('storage/' . $landingPage->hero_image_3) ?? 'https://images.unsplash.com/photo-1553028826-f4804a6dba3b?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80' }}"
                        alt="Technology learning" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-orange-500 bg-opacity-40"></div>
                </div>
            </div>
        </div>

        <!-- Overlay Orange Transparan -->
        <div class="absolute inset-0 bg-gradient-to-r from-orange-900/70 to-orange-600/60"></div>

        <!-- Teks Hero -->
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="animate-fade-in">
                <h1 class="text-4xl md:text-6xl font-bold text-white drop-shadow-[0_4px_6px_rgba(0,0,0,0.6)] mb-6">
                    {{ $landingPage->hero_title ?? 'Belajar Tanpa Batas' }}
                    <span class="block text-orange-300">{{ $landingPage->hero_subtitle ?? 'Raih Masa Depan' }}</span>
                </h1>
                <p class="text-xl md:text-2xl text-white mb-8 max-w-3xl mx-auto drop-shadow-[0_3px_4px_rgba(0,0,0,0.6)]">
                    {{ $landingPage->hero_description ?? 'Nikmati Kemudahan lolos TES KEMAMPUAN AKADEMIK (TKA) melalui Kelas Premium Prediksi TKA 2025, yang berisi Ringkasan Materi Pembahasan, disusun berdasarkan Kisi-Kisi Ujian TKA Terbaru Tahun 2025, Latihan Soal, Kunci Jawaban, Uraian atas Jawaban dan Try Out Sistem Computer Assisted Test (CAT)' }}
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('login') }}"
                        class="px-8 py-3 rounded-lg text-lg font-semibold bg-orange-500 text-white hover:bg-orange-600 transition-all transform hover:scale-105 shadow-lg">
                        {{ $landingPage->hero_btn_primary ?? 'Mulai Belajar Sekarang' }}
                    </a>
                    <a href="{{ route('course') }}"
                        class="px-8 py-3 rounded-lg text-lg font-semibold border border-white text-white hover:bg-orange-500 hover:border-orange-500 hover:text-white transition-all shadow-lg">
                        {{ $landingPage->hero_btn_secondary ?? 'Jelajahi Kursus' }}
                    </a>
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
                            <div class="w-16 h-16 bg-primary-50 rounded-full flex items-center justify-center mx-auto mb-4">
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
                <img src="{{ asset('image/join.png') }}" alt="How to Join Illustration"
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

@endsection
@push('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
@endpush
@push('js')
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
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
