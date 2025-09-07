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
            display: flex;
            justify-content: center;
            align-items: center;
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

<body>
    <!-- Background floating shapes -->
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <!-- Form -->
    <div class="glass-effect relative z-10 max-w-md w-full shadow-2xl rounded-2xl p-8 animate-fade-in">
        <h2 class="text-3xl font-extrabold mb-6 text-center gradient-text">Reset Password</h2>
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
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <form method="POST" action="{{ route('password.update_reset') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token ?? '' }}">

            <div class="mb-4">
                <input type="hidden" id="email" name="email" value="{{ old('email', $email ?? '') }}" required
                    class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-orange-400">
            </div>


            <div class="mb-4">
                <label for="password" class="block font-medium text-gray-700">Password Baru</label>
                <input type="password" id="password" name="password" required
                    class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-orange-400">
            </div>

            <div class="mb-6">
                <label for="password_confirmation" class="block font-medium text-gray-700">Konfirmasi Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required
                    class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-orange-400">
            </div>

            <button type="submit"
                class="w-full py-3 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-lg shadow-lg transition transform hover:scale-105">
                Reset Password
            </button>
        </form>
    </div>

    <script>
        // Glow effect saat fokus di email
        const emailInput = document.getElementById('email');
        emailInput.addEventListener('focus', () => {
            emailInput.classList.add('animate-glow');
        });
        emailInput.addEventListener('blur', () => {
            emailInput.classList.remove('animate-glow');
        });
    </script>
</body>

</html>
