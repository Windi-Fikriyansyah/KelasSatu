<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>KelasSatu - Platform E-Course Terbaik</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.PNG') }}">
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

        .text-primary-100 {
            color: #eb631d;
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
            <div class="flex justify-between items-center h-16">
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

                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        @auth
                            <a href="{{ route('dashboardUser') }}">
                                <img src="{{ asset('image/logo.png') }}" alt="KelasSatu" class="h-12 w-auto">
                            </a>
                        @else
                            <a href="/">
                                <img src="{{ asset('image/logo.png') }}" alt="KelasSatu" class="h-12 w-auto">
                            </a>
                        @endauth
                    </div>
                </div>


                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    @auth
                        <a href="{{ route('dashboardUser') }}"
                            class="text-gray-900 hover:text-primary-100 px-3 py-2 text-sm font-medium transition-colors">Beranda</a>
                        <a href="{{ route('course') }}"
                            class="text-gray-900 hover:text-primary-100 px-3 py-2 text-sm font-medium transition-colors">Beli
                            Kelas</a>
                        <a href="{{ route('kelas.index') }}"
                            class="text-gray-900 hover:text-primary-100 px-3 py-2 text-sm font-medium transition-colors">Kelas
                            Saya</a>
                    @else
                        <a href="/"
                            class="text-gray-900 hover:text-primary-100 px-3 py-2 text-sm font-medium transition-colors">Beranda</a>
                        <a href="{{ route('course') }}"
                            class="text-gray-600 hover:text-primary-100 px-3 py-2 text-sm font-medium transition-colors">Kursus</a>
                        <a href="#about"
                            class="text-gray-600 hover:text-primary-100 px-3 py-2 text-sm font-medium transition-colors">Tentang</a>
                        <a href="#contact"
                            class="text-gray-600 hover:text-primary-100 px-3 py-2 text-sm font-medium transition-colors">Kontak</a>
                    @endauth
                </div>

                <!-- Right Side - User menu atau Login button -->
                <div class="flex items-center space-x-4">
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
                                <a href="{{ route('account.index') }}"
                                    class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Settings</a>
                                <a href="{{ route('history.index') }}"
                                    class="block px-4 py-2 text-gray-700 hover:bg-gray-100">History
                                    Transaksi</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">Logout</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}"
                            class="btn-primary px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            Masuk
                        </a>
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
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full text-left px-3 py-2 text-gray-600 hover:text-primary-100">Logout</button>
                    </form>
                @else
                    <a href="/" class="block px-3 py-2 text-gray-900 font-medium">Beranda</a>
                    <a href="{{ route('course') }}" class="block px-3 py-2 text-gray-600 hover:text-primary-100">Kursus</a>
                    <a href="#about" class="block px-3 py-2 text-gray-600 hover:text-primary-100">Tentang</a>
                    <a href="#contact" class="block px-3 py-2 text-gray-600 hover:text-primary-100">Kontak</a>
                    <a href="{{ route('login') }}" class="block px-3 py-2 text-gray-600 hover:text-primary-100">Masuk</a>
                @endauth
            </div>
        </div>
    </nav>

    @yield('content')





    <!-- Footer -->
    <footer class="bg-primary-200 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-2xl font-bold text-primary-100 mb-4">KelasSatu</h3>
                    <p class="text-blue-100 mb-4">Platform e-learning terdepan untuk masa depan yang lebih cerah.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-blue-100 hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z" />
                            </svg>
                        </a>
                        <a href="#" class="text-blue-100 hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.20-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z" />
                            </svg>
                        </a>
                    </div>
                </div>

                <div>
                    <h4 class="text-lg font-semibold mb-4">Platform</h4>
                    <ul class="space-y-2 text-blue-100">
                        <li><a href="#" class="hover:text-white transition-colors">Kursus</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Instruktur</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Sertifikasi</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Komunitas</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-lg font-semibold mb-4">Dukungan</h4>
                    <ul class="space-y-2 text-blue-100">
                        <li><a href="#" class="hover:text-white transition-colors">Bantuan</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">FAQ</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Kontak</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Status</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-lg font-semibold mb-4">Perusahaan</h4>
                    <ul class="space-y-2 text-blue-100">
                        <li><a href="#" class="hover:text-white transition-colors">Tentang Kami</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Karir</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Blog</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Privacy</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-blue-800 mt-8 pt-8 text-center text-blue-100">
                <p>&copy; 2024 KelasSatu. Seluruh hak cipta dilindungi.</p>
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
