<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>KelasSatu - Platform E-Course Terbaik</title>
    <link rel="icon" type="image/png" href="{{ asset('image/logo.png') }}">
    <link rel="shortcut icon" href="{{ asset('image/logo.png') }}" type="image/x-icon">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f7f7f7',
                            100: '#eb631d',
                            200: '#254768',
                        },
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in',
                        'slide-in': 'slideIn 0.5s ease-out'
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
            }

            to {
                transform: translateX(0);
            }
        }

        /* Custom styles untuk warna yang diberikan */
        .bg-primary-50 {
            background-color: #f7f7f7;
        }

        .bg-primary-100 {
            background-color: #eb631d;
        }

        .bg-primary-200 {
            background-color: #254768;
        }

        .bg-primary-500 {
            background-color: #eb631d;
        }

        .text-primary-100 {
            color: #eb631d;
        }

        .text-primary-500 {
            color: white;
        }

        .text-primary-200 {
            color: #254768;
        }

        .border-primary-100 {
            border-color: #eb631d;
        }

        .hover\:bg-primary-100:hover {
            background-color: #eb631d;
        }

        .hover\:text-primary-100:hover {
            color: #eb631d;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #254768 0%, #1a334d 100%);
        }

        .btn-primary {
            background-color: #eb631d;
            color: white;
        }

        .btn-primary:hover {
            background-color: #d45615;
        }

        .btn-outline {
            border: 2px solid #eb631d;
            color: #eb631d;
        }

        .btn-outline:hover {
            background-color: #eb631d;
            color: white;
        }

        .course-badge {
            background-color: rgba(235, 99, 29, 0.1);
            color: #eb631d;
        }

        /* Styles for testimonial section */
        .testimonial-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .testimonial-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .testimonial-avatar {
            width: 60px;
            height: 60px;
            object-fit: cover;
        }

        .quote-icon {
            color: rgba(235, 99, 29, 0.2);
        }
    </style>
    @stack('style')
</head>

<body class="bg-primary-50">
    <!-- Navigation -->
    <!-- Navigation -->
    <!-- Ganti bagian navigasi yang ada (baris sekitar 91-154) dengan kode ini: -->
    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-start items-center h-16">

                <!-- Mobile menu button - dipindah ke kiri -->
                <div class="md:hidden">
                    <button
                        class="text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-primary-100"
                        onclick="toggleMobileMenu()">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>

                <div class="flex-shrink-0 mx-auto md:mx-0">
                    @auth
                        <a href="{{ route('dashboardUser') }}">
                            <img src="{{ asset('image/logo.png') }}" alt="KelasSatu" class="h-16 w-auto">
                        </a>
                    @else
                        <a href="/">
                            <img src="{{ asset('image/logo.png') }}" alt="KelasSatu" class="h-16 w-auto">
                        </a>
                    @endauth
                </div>



                <!-- Desktop Menu -->
                <!-- Desktop Menu (pindah ke kiri dengan font lebih besar) -->
                <div class="hidden md:flex items-center space-x-8 ml-8 text-base font-medium"
                    style="font-family: Calibri, sans-serif;">

                    @auth
                        <a href="{{ route('dashboardUser') }}"
                            class="text-gray-600 hover:text-primary-100 px-3 py-2 text-lg font-medium transition-colors">Beranda</a>
                        <a href="{{ route('course') }}"
                            class="text-gray-600 hover:text-primary-100 px-3 py-2 text-lg font-medium transition-colors">Beli
                            Kelas</a>
                        <a href="{{ route('kelas.index') }}"
                            class="text-gray-600 hover:text-primary-100 px-3 py-2 text-lg font-medium transition-colors">Kelas
                            Saya</a>
                        <a href="{{ route('account.index') }}"
                            class="text-gray-600 hover:text-primary-100 px-3 py-2 text-lg font-medium transition-colors">Pengaturan</a>
                        <a href="{{ route('history.index') }}"
                            class="text-gray-600 hover:text-primary-100 px-3 py-2 text-lg font-medium transition-colors">Transaksi</a>
                    @else
                        <a href="#beranda"
                            class="text-gray-600 hover:text-primary-100 px-3 py-2 text-lg font-medium transition-colors">Beranda</a>
                        <a href="#courses"
                            class="text-gray-600 hover:text-primary-100 px-3 py-2 text-lg font-medium transition-colors">Kursus</a>
                        <a href="#testimoni"
                            class="text-gray-600 hover:text-primary-100 px-3 py-2 text-lg font-medium transition-colors">Testimoni</a>
                        <a href="#faq"
                            class="text-gray-600 hover:text-primary-100 px-3 py-2 text-lg font-medium transition-colors">Pertanyaan
                            Umum</a>
                        <a href="#about"
                            class="text-gray-600 hover:text-primary-100 px-3 py-2 text-lg font-medium transition-colors">Tentang
                            Kami</a>
                    @endauth

                </div>

                <!-- Right Side - User menu atau Login button -->
                <div class="flex items-center space-x-4 ml-auto">
                    @auth
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none">
                                <svg class="w-8 h-8 text-gray-700" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                </svg>
                                <span class="text-gray-700 font-medium hidden sm:inline">{{ Auth::user()->name }}</span>
                            </button>

                            <!-- Dropdown -->
                            <div x-show="open" @click.away="open = false"
                                class="absolute right-0 mt-2 w-40 bg-white border rounded-lg shadow-lg py-2 z-50">
                                {{-- <a href="{{ route('account.index') }}"
                                    class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Settings</a> --}}

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">Logout</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="hidden md:flex items-center space-x-2">
                            <a href="{{ route('login') }}"
                                class="px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-300
                  border-2 border-primary-100 text-primary-100 hover:bg-primary-100 hover:text-white shadow-sm">
                                Masuk
                            </a>
                            <a href="{{ route('register.create') }}"
                                class="px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-300
                  bg-primary-100 text-white hover:bg-[#d45615] shadow-md">
                                Daftar
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>

        <!-- Mobile menu - tidak ada perubahan -->
        <div id="mobile-menu" class="md:hidden hidden bg-white border-t">
            <div class="px-2 pt-2 pb-3 space-y-1">
                @auth
                    <a href="/" class="block px-3 py-2 text-gray-900 font-medium">Beranda</a>
                    <a href="{{ route('course') }}" class="block px-3 py-2 text-gray-600 hover:text-primary-100">Beli
                        Kelas</a>
                    <a href="{{ route('kelas.index') }}" class="block px-3 py-2 text-gray-600 hover:text-primary-100">Kelas
                        Saya</a>
                    <a href="{{ route('account.index') }}"
                        class="block px-3 py-2 text-gray-600 hover:text-primary-100">Pengaturan</a>
                    <a href="{{ route('history.index') }}"
                        class="block px-3 py-2 text-gray-600 hover:text-primary-100">Transaksi</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full text-left px-3 py-2 text-gray-600 hover:text-primary-100">Logout</button>
                    </form>
                @else
                    <a href="#beranda" class="block px-3 py-2 text-gray-900 font-medium">Beranda</a>
                    <a href="#courses" class="block px-3 py-2 text-gray-600 hover:text-primary-100">Kursus</a>
                    <a href="#testimoni" class="block px-3 py-2 text-gray-600 hover:text-primary-100">Testimoni</a>
                    <a href="#faq" class="block px-3 py-2 text-gray-600 hover:text-primary-100">Pertanyaan Umum</a>
                    <a href="#about" class="block px-3 py-2 text-gray-600 hover:text-primary-100">Tentang Kami</a>
                    <a href="{{ route('login') }}"
                        class="block w-full text-center px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-300
              border-2 border-primary-100 text-primary-100 hover:bg-primary-100 hover:text-white shadow-sm">
                        Masuk
                    </a>
                    <a href="{{ route('register.create') }}"
                        class="block w-full text-center px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-300
              bg-primary-100 text-white hover:bg-[#d45615] shadow-md">
                        Daftar
                    </a>
                @endauth

            </div>
        </div>
    </nav>

    @yield('content')





    <!-- Footer -->
    <!-- Footer -->
    <!-- Footer -->
    <footer class="bg-primary-500 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- <div class="mb-12 w-full md:w-1/2">
                <h4 class="text-lg font-semibold mb-4">Lokasi Kami</h4>
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3101.4602199841984!2d109.3700997!3d-0.015600800000000001!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e1d59003d47d307%3A0x48068b786fb05b41!2sSegar%20Dingin%20Office!5e1!3m2!1sid!2sid!4v1758106665595!5m2!1sid!2sid"
                    width="100%" height="250" style="border:0; border-radius: 12px;" allowfullscreen=""
                    loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                </iframe>
                <div class="flex items-center mt-3 text-primary-500">
                    <i class="fas fa-map-marker-alt mr-2 text-white"></i>
                    <span>Siantan Hulu, Kec. Pontianak Utara, Kota Pontianak, Kalimantan Barat 78242</span>
                </div>
            </div> --}}

            <!-- Grid menu footer -->
            <div class="grid md:grid-cols-4 gap-8">

                <div>
                    <h3 class="text-2xl font-bold text-primary-500 mb-4">
                        {{ $landingFooter->footer_title ?? 'KelasSatu' }}
                    </h3>
                    <p class="text-primary-500 mb-4">
                        {{ $landingFooter->footer_description ?? 'Platform e-learning terdepan untuk masa depan yang lebih cerah.' }}
                    </p>
                    {{-- @if ($landingFooter->footer_address)
                        <p class="text-primary-500"><i class="fas fa-map-marker-alt mr-2"></i>
                            {{ $landingFooter->footer_address }}</p>
                    @endif --}}
                </div>

                <div>
                    <h4 class="text-lg font-semibold mb-4">Platform</h4>
                    <ul class="space-y-2 text-primary-500">
                        <li><a href="#about" class="hover:text-white transition-colors">Tentang Kami</a></li>
                        <li><a href="#courses" class="hover:text-white transition-colors">Kursus</a></li>
                        <li><a href="#testimoni" class="hover:text-white transition-colors">Testimoni</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-lg font-semibold mb-4">Dukungan</h4>
                    <ul class="space-y-2 text-primary-500">
                        <li><a href="#faq" class="hover:text-white transition-colors">Pertanyaan Umum</a></li>
                        <li><a href="#how-to-join" class="hover:text-white transition-colors">How To Join</a></li>
                        <li><a href="#why" class="hover:text-white transition-colors">Why Kelassatu.com?</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-lg font-semibold mb-4">Perusahaan</h4>
                    <ul class="space-y-2 text-primary-500">
                        <li><a href="#" class="hover:text-white transition-colors">Hubungi Kami</a></li>
                    </ul>
                    <div class="flex space-x-4 mt-4">
                        @if ($landingFooter->footer_whatsapp)
                            <a href="{{ $landingFooter->footer_whatsapp }}" target="_blank"
                                class="hover:text-white transition-colors">
                                <i class="fab fa-whatsapp text-2xl"></i>
                            </a>
                        @endif

                        @if ($landingFooter->footer_instagram)
                            <a href="{{ $landingFooter->footer_instagram }}" target="_blank"
                                class="hover:text-white transition-colors">
                                <i class="fab fa-instagram text-2xl"></i>
                            </a>
                        @endif

                        @if ($landingFooter->footer_tiktok)
                            <a href="{{ $landingFooter->footer_tiktok }}" target="_blank"
                                class="hover:text-white transition-colors">
                                <i class="fab fa-tiktok text-2xl"></i>
                            </a>
                        @endif

                        {{-- @if ($landingFooter->footer_facebook)
                            <a href="{{ $landingFooter->footer_facebook }}" target="_blank"
                                class="hover:text-white transition-colors">
                                <i class="fab fa-facebook text-2xl"></i>
                            </a>
                        @endif --}}
                    </div>
                </div>

            </div>
            <!-- Copyright -->
            <div class="border-t border-blue-800 mt-8 pt-8 text-center text-primary-500">
                <p>{{ $landingFooter->footer_copyright ?? '&copy; 2025 PT KELAS SATU INDONESIA. Seluruh hak cipta dilindungi.' }}
                </p>
            </div>
        </div>
    </footer>



    <script src="//unpkg.com/alpinejs" defer></script>

    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
    <script>
        // Image slider functionality
        let currentSlideIndex = 0;
        const slides = document.querySelectorAll('.slide');
        const dots = document.querySelectorAll('.slider-dot');

        function toggleFaq(id) {
            const answer = document.getElementById('answer-' + id);
            const icon = document.getElementById('icon-' + id);
            if (answer.classList.contains('hidden')) {
                answer.classList.remove('hidden');
                icon.classList.add('rotate-45'); // Ubah ikon jadi tanda minus
            } else {
                answer.classList.add('hidden');
                icon.classList.remove('rotate-45');
            }
        }

        function showSlide(index) {
            slides.forEach((slide, i) => {
                slide.style.opacity = i === index ? '1' : '0';
            });

            dots.forEach((dot, i) => {
                dot.style.opacity = i === index ? '1' : '0.5';
            });
        }

        function nextSlide() {
            currentSlideIndex = (currentSlideIndex + 1) % slides.length;
            showSlide(currentSlideIndex);
        }

        function currentSlide(index) {
            currentSlideIndex = index - 1;
            showSlide(currentSlideIndex);
        }

        // Auto-advance slides
        setInterval(nextSlide, 5000);

        // Mobile menu toggle
        function toggleMobileMenu() {
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.toggle('hidden');
        }

        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
    @stack('js')
</body>

</html>
