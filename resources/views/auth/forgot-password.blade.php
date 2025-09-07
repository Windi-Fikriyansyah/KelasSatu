<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lupa Password - Modern Form</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    animation: {
                        'float': 'float 3s ease-in-out infinite',
                        'glow': 'glow 2s ease-in-out infinite alternate',
                        'slide-up': 'slide-up 0.6s ease-out',
                        'fade-in': 'fade-in 0.8s ease-out',
                        'bounce-in': 'bounce-in 0.6s ease-out'
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': {
                                transform: 'translateY(0px)'
                            },
                            '50%': {
                                transform: 'translateY(-10px)'
                            }
                        },
                        glow: {
                            '0%': {
                                boxShadow: '0 0 20px rgba(168, 85, 247, 0.4)'
                            },
                            '100%': {
                                boxShadow: '0 0 30px rgba(168, 85, 247, 0.8)'
                            }
                        },
                        'slide-up': {
                            '0%': {
                                transform: 'translateY(30px)',
                                opacity: '0'
                            },
                            '100%': {
                                transform: 'translateY(0)',
                                opacity: '1'
                            }
                        },
                        'fade-in': {
                            '0%': {
                                opacity: '0'
                            },
                            '100%': {
                                opacity: '1'
                            }
                        },
                        'bounce-in': {
                            '0%': {
                                transform: 'scale(0.3)',
                                opacity: '0'
                            },
                            '50%': {
                                transform: 'scale(1.05)'
                            },
                            '70%': {
                                transform: 'scale(0.9)'
                            },
                            '100%': {
                                transform: 'scale(1)',
                                opacity: '1'
                            }
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .gradient-text {
            background: linear-gradient(45deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .floating-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 0;
        }

        .shape {
            position: absolute;
            border-radius: 50%;
            opacity: 0.1;
            animation: float 6s ease-in-out infinite;
        }

        .shape:nth-child(1) {
            width: 80px;
            height: 80px;
            background: #fff;
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }

        .shape:nth-child(2) {
            width: 120px;
            height: 120px;
            background: #a855f7;
            top: 20%;
            right: 10%;
            animation-delay: 2s;
        }

        .shape:nth-child(3) {
            width: 60px;
            height: 60px;
            background: #06b6d4;
            bottom: 20%;
            left: 15%;
            animation-delay: 4s;
        }

        .shape:nth-child(4) {
            width: 100px;
            height: 100px;
            background: #f59e0b;
            bottom: 30%;
            right: 20%;
            animation-delay: 1s;
        }
    </style>
</head>

<body class="flex items-center justify-center min-h-screen p-4">
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <div class="max-w-md w-full mx-auto glass-effect shadow-2xl rounded-3xl p-8 relative z-10 animate-slide-up">
        <!-- Icon Header -->
        <div class="text-center mb-8 animate-fade-in">
            <div
                class="w-20 h-20 mx-auto mb-4 bg-gradient-to-r from-purple-500 to-blue-500 rounded-full flex items-center justify-center animate-glow">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <h2 class="text-3xl font-bold gradient-text mb-2">Lupa Password?</h2>
            <p class="text-gray-600 text-sm">Jangan khawatir, kami akan mengirimkan link reset ke email Anda</p>
        </div>

        <!-- Success Message (Custom Indonesian) -->
        @if (session('status'))
            <div
                class="p-4 mb-6 text-green-800 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-400 rounded-xl animate-bounce-in shadow-lg">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-green-500 animate-pulse" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-bold text-green-800">Email Berhasil Dikirim! ✨</h3>
                        <div class="mt-2 text-sm text-green-700">
                            <p>Kami telah mengirimkan link untuk mereset password ke email Anda. Silakan periksa kotak
                                masuk dan ikuti petunjuk yang diberikan.</p>
                            <p class="mt-1 text-xs text-green-600">💡 <strong>Tips:</strong> Jika tidak ada di inbox,
                                cek folder spam atau promosi</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Error Messages (Custom Indonesian) -->
        @if ($errors->any())
            <div
                class="p-4 mb-6 text-red-800 bg-gradient-to-r from-red-50 to-pink-50 border-l-4 border-red-400 rounded-xl animate-bounce-in shadow-lg">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-bold text-red-800">Ups, Ada Masalah! ⚠️</h3>
                        <div class="mt-2 text-sm text-red-700">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                            <p class="mt-1 text-xs text-red-600">💡 Pastikan email yang dimasukkan sudah benar dan
                                terdaftar di sistem kami</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Form -->
        <form id="forgot-password-form" method="POST" action="{{ route('password.forget') }}" class="space-y-6">
            @csrf
            <div class="relative">
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                    Alamat Email
                </label>
                <div class="relative group">
                    <input type="email" name="email" id="email" required placeholder="contoh@email.com"
                        class="w-full pl-10 pr-4 py-4 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all duration-200 bg-white/80 backdrop-blur-sm hover:bg-white/90"
                        value="{{ old('email') }}">
                    <div class="absolute left-3 top-1/2 transform -translate-y-1/2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>

            <button type="submit"
                class="group w-full py-4 px-6 bg-gradient-to-r from-purple-600 to-blue-600 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 relative overflow-hidden">
                <span class="relative z-10">Kirim Link Reset Password</span>
                <div
                    class="absolute inset-0 bg-gradient-to-r from-purple-700 to-blue-700 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                </div>
            </button>
        </form>

        <!-- Back to Login Link -->
        <div class="mt-8 text-center">
            <p class="text-gray-600 text-sm mb-3">Sudah ingat password Anda?</p>
            <a href="{{ route('login') }}"
                class="text-purple-600 hover:text-purple-700 font-semibold text-sm hover:underline transition-all duration-200 inline-flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Login
            </a>
        </div>

        <!-- Decorative Elements -->
        <div
            class="absolute -top-4 -right-4 w-8 h-8 bg-gradient-to-r from-pink-400 to-purple-400 rounded-full opacity-60 animate-float">
        </div>
        <div class="absolute -bottom-4 -left-4 w-6 h-6 bg-gradient-to-r from-blue-400 to-cyan-400 rounded-full opacity-60 animate-float"
            style="animation-delay: 1s;"></div>
    </div>

    <script>
        // Add floating animation to form on scroll
        window.addEventListener('scroll', () => {
            const form = document.querySelector('.glass-effect');
            const scrolled = window.pageYOffset;
            form.style.transform = `translateY(${scrolled * 0.1}px)`;
        });

        // Add some interactive effects
        document.getElementById('email').addEventListener('focus', function() {
            this.parentElement.classList.add('animate-glow');
        });

        document.getElementById('email').addEventListener('blur', function() {
            this.parentElement.classList.remove('animate-glow');
        });

        // Auto hide success/error messages after 10 seconds
        const successMessage = document.querySelector('.bg-gradient-to-r.from-green-50');
        const errorMessage = document.querySelector('.bg-gradient-to-r.from-red-50');

        if (successMessage) {
            setTimeout(() => {
                successMessage.style.opacity = '0';
                setTimeout(() => successMessage.remove(), 300);
            }, 10000);
        }

        if (errorMessage) {
            setTimeout(() => {
                errorMessage.style.opacity = '0';
                setTimeout(() => errorMessage.remove(), 300);
            }, 15000);
        }
    </script>
</body>

</html>
