<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>GMF | Reliability Report</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- External Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs" defer></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css">
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>

    <style>
        /* Custom Animations */
        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg) scale(1); }
            25% { transform: translateY(-20px) rotate(5deg) scale(1.05); }
            50% { transform: translateY(0) rotate(0deg) scale(1); }
            75% { transform: translateY(20px) rotate(-5deg) scale(0.95); }
        }

        @keyframes bar-bounce {
            0%, 100% { transform: scaleY(0.5); opacity: 0.5; }
            50% { transform: scaleY(1.2); opacity: 1; }
        }

        @keyframes slide-in-left {
            0% { transform: translateX(-100%) scale(0.95); opacity: 0; }
            100% { transform: translateX(0) scale(1); opacity: 1; }
        }

        @keyframes slide-in-right {
            0% { transform: translateX(100%) scale(0.95); opacity: 0; }
            100% { transform: translateX(0) scale(1); opacity: 1; }
        }

        @keyframes fade-up {
            0% { transform: translateY(40px) scale(0.95); opacity: 0; }
            100% { transform: translateY(0) scale(1); opacity: 1; }
        }

        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(126, 187, 26, 0.3); }
            50% { box-shadow: 0 0 40px rgba(126, 187, 26, 0.6); }
        }

        .bg-circle {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(45deg, rgba(34, 197, 94, 0.3), rgba(15, 8, 38, 0.1));
            filter: blur(30px);
            animation: float 20s infinite ease-in-out;
        }

        .bg-circle:nth-child(1) {
            width: 500px; height: 500px;
            top: -100px; right: -100px;
            animation-delay: 0s;
        }

        .bg-circle:nth-child(2) {
            width: 300px; height: 300px;
            bottom: -50px; left: 10%;
            animation-delay: -5s;
        }

        .bg-circle:nth-child(3) {
            width: 200px; height: 200px;
            bottom: 30%; right: 20%;
            animation-delay: -10s;
        }

        .animate-loader-bar {
            animation: bar-bounce 1s infinite ease-in-out;
        }

        .animate-slide-in-left {
            animation: slide-in-left 0.8s cubic-bezier(0.4, 2, 0.6, 1) forwards;
        }

        .animate-slide-in-right {
            animation: slide-in-right 0.8s cubic-bezier(0.4, 2, 0.6, 1) forwards;
        }

        .animate-fade-up {
            animation: fade-up 0.8s cubic-bezier(0.4, 2, 0.6, 1) forwards;
        }

        .animate-pulse-glow {
            animation: pulse-glow 2s infinite;
        }

        /* Bullet Navigation */
        .custom-bullet {
            @apply inline-block w-2 h-2 rounded-full bg-white opacity-50 mx-1 cursor-pointer transition-all duration-300;
        }

        .custom-bullet.active {
            @apply bg-green-500 opacity-100 w-3 h-3 -translate-y-0.5;
        }

        /* Page Load Animations */
        .page-element {
            opacity: 0;
            transform: translateY(40px) scale(0.95);
        }

        .page-element.animate-in {
            animation: fade-up 0.8s cubic-bezier(0.4, 2, 0.6, 1) forwards;
        }

        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        .delay-400 { animation-delay: 0.4s; }
        .delay-500 { animation-delay: 0.5s; }
    </style>
</head>

<body class="bg-[#112955] text-white font-sans min-h-screen overflow-hidden relative" x-data="{ showLogin: false }">
    <!-- Animated Background Circles -->
    <div class="absolute inset-0 overflow-hidden z-0">
        <div class="bg-circle"></div>
        <div class="bg-circle"></div>
        <div class="bg-circle"></div>
    </div>

    <!-- Login Loader -->
    <div id="page-loader" class="fixed inset-0 z-50 hidden flex-col items-center justify-center bg-white bg-opacity-80 transition-all duration-500 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl p-8 border border-gray-200">
            <div class="flex space-x-3 mb-6 justify-center">
                <div class="w-4 h-16 bg-gradient-to-t from-blue-800 to-blue-500 rounded-full animate-loader-bar"></div>
                <div class="w-4 h-16 bg-gradient-to-t from-blue-700 to-blue-400 rounded-full animate-loader-bar delay-150"></div>
                <div class="w-4 h-16 bg-gradient-to-t from-green-600 to-green-400 rounded-full animate-loader-bar delay-300"></div>
            </div>
            <span id="loader-text" class="text-lg font-semibold text-gray-700 block text-center">Logging in...</span>
            <div class="mt-4 w-48 h-1 bg-gray-200 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-blue-600 to-green-500 rounded-full animate-pulse"></div>
            </div>
        </div>
    </div>

    <!-- Main Container -->
    <div class="flex flex-col md:flex-row items-center justify-between min-h-screen relative z-10 container mx-auto px-4 md:px-12 py-8">
        <!-- Left Content -->
        <div class="w-full md:w-1/2 flex flex-col justify-center min-h-[500px] p-6 md:p-12">
            <!-- Logo -->
            <div class="mb-10 flex justify-center md:justify-start page-element delay-100">
                <img
                    src="{{ asset('images/gmfwhite.png') }}"
                    alt="Logo GMF"
                    class="h-20 w-auto max-w-full hover:scale-110 transition-transform duration-300 drop-shadow-2xl"
                >
            </div>

            <!-- Content Container -->
            <div class="relative min-h-[400px]">
                <!-- Welcome Section -->
                <div
                    x-show="!showLogin"
                    x-transition:enter="transition-all transform duration-700 ease-out"
                    x-transition:enter-start="opacity-0 translate-x-12 scale-95"
                    x-transition:enter-end="opacity-100 translate-x-0 scale-100"
                    x-transition:leave="transition-all transform duration-500 ease-in"
                    x-transition:leave-start="opacity-100 translate-x-0 scale-100"
                    x-transition:leave-end="opacity-0 -translate-x-12 scale-95"
                    class="space-y-6 max-w-lg absolute top-0 left-0 w-full"
                >
                    <div class="space-y-6 page-element delay-200">
                        <h4 class="text-sm uppercase tracking-wider text-gray-300 font-semibold">Selamat Datang di</h4>
                        <h1 class="text-5xl font-light leading-tight bg-gradient-to-r from-white to-gray-200 bg-clip-text text-transparent">
                            Reliability Report
                        </h1>
                        <p class="text-gray-300 max-w-md text-lg leading-relaxed">
                            Platform laporan keandalan pesawat yang membantu memantau performa operasional dan mendukung pengambilan keputusan berbasis data.
                        </p>

                        <button
                            @click="showLogin = true"
                            class="group relative bg-[#7EBB1A] hover:bg-[#8DC63F] text-white font-semibold px-8 py-4 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 transform overflow-hidden animate-pulse-glow"
                        >
                            <span class="relative z-10 flex items-center space-x-2">
                                <span>MASUK</span>
                                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                </svg>
                            </span>
                            <div class="absolute inset-0 bg-gradient-to-r from-[#8DC63F] to-[#7EBB1A] opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        </button>

                        <p class="text-xs text-gray-400 mt-10 font-medium">
                            © {{ date('Y') }} PT Garuda Maintenance Facility AeroAsia Tbk
                        </p>
                    </div>
                </div>

                <!-- Login Section -->
                <div
                    x-show="showLogin"
                    x-transition:enter="transition-all transform duration-700 ease-out"
                    x-transition:enter-start="opacity-0 translate-x-12 scale-95"
                    x-transition:enter-end="opacity-100 translate-x-0 scale-100"
                    x-transition:leave="transition-all transform duration-500 ease-in"
                    x-transition:leave-start="opacity-100 translate-x-0 scale-100"
                    x-transition:leave-end="opacity-0 -translate-x-12 scale-95"
                    class="w-full max-w-lg absolute top-0 left-0"
                >
                    <div class="space-y-8">
                        <h2 class="text-4xl font-light leading-tight mb-8 bg-gradient-to-r from-white to-gray-200 bg-clip-text text-transparent">
                            Sign in to your account
                        </h2>

                        <div class="w-full max-w-md">
                            <form method="POST" action="{{ route('login') }}" class="space-y-6" id="login-form">
                                @csrf

                                <!-- Email Field -->
                                <div class="space-y-2">
                                    <label for="email" class="block text-sm text-white font-medium">Email</label>
                                    <input
                                        id="email"
                                        class="w-full px-4 py-3 rounded-xl border border-gray-600 bg-white/90 text-gray-900 placeholder-gray-500 focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-500/20 transition-all duration-300 hover:bg-white focus:-translate-y-0.5 shadow-lg"
                                        type="email"
                                        name="email"
                                        value="{{ old('email') }}"
                                        placeholder="Enter your email address"
                                        required
                                        autofocus
                                        autocomplete="username"
                                    />
                                    @error('email')
                                        <p class="text-red-400 text-xs mt-1 font-medium">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Password Field -->
                                <div class="space-y-2">
                                    <label for="password" class="block text-sm text-white font-medium">Password</label>
                                    <input
                                        id="password"
                                        class="w-full px-4 py-3 rounded-xl border border-gray-600 bg-white/90 text-gray-900 placeholder-gray-500 focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-500/20 transition-all duration-300 hover:bg-white focus:-translate-y-0.5 shadow-lg"
                                        type="password"
                                        name="password"
                                        placeholder="Enter your password"
                                        required
                                        autocomplete="current-password"
                                    />
                                    @error('password')
                                        <p class="text-red-400 text-xs mt-1 font-medium">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Remember Me & Forgot Password -->
                                <div class="flex items-center justify-between text-sm">
                                    <div class="flex items-center space-x-2">
                                        <input
                                            id="remember_me"
                                            type="checkbox"
                                            class="w-4 h-4 rounded border-gray-300 text-green-500 shadow-sm focus:ring-green-500 focus:ring-2"
                                            name="remember"
                                        >
                                        <label for="remember_me" class="text-gray-300 font-medium">Remember me</label>
                                    </div>
                                    @if (Route::has('password.request'))
                                        <a class="text-gray-400 hover:text-white font-medium underline transition-colors duration-200" href="{{ route('password.request') }}">
                                            Forgot password?
                                        </a>
                                    @endif
                                </div>

                                <!-- Login Button -->
                                <div>
                                    <button
                                        type="submit"
                                        class="group w-full bg-[#7EBB1A] hover:bg-[#8DC63F] text-white font-semibold px-6 py-4 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 transform relative overflow-hidden min-h-[48px] flex items-center justify-center"
                                        id="login-btn"
                                    >
                                        <span class="relative z-10 flex items-center space-x-2">
                                            <span>LOG IN</span>
                                        </span>
                                        <div class="absolute inset-0 bg-gradient-to-r from-[#8DC63F] to-[#7EBB1A] opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                    </button>
                                </div>

                                <!-- Register Link -->
                                <div class="text-left">
                                    <p class="text-gray-300">
                                        Belum punya akun?
                                        <a
                                            href="{{ route('register') }}"
                                            class="text-[#7EBB1A] hover:text-[#8DC63F] font-semibold hover:underline transition-all duration-200 ml-1"
                                        >
                                            Daftar di sini
                                        </a>
                                    </p>
                                </div>

                                <!-- Back Button -->
                                <div class="mt-6">
                                    <button
                                        @click="showLogin = false"
                                        type="button"
                                        class="group text-[#7EBB1A] hover:text-[#8DC63F] font-semibold transition-colors duration-200 flex items-center space-x-2"
                                    >
                                        <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                        </svg>
                                        <span>Kembali ke halaman awal</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Carousel -->
        <div class="hidden md:flex md:w-1/2 h-full bg-[#112955]/10 backdrop-blur-sm px-4 md:px-12 py-8 items-center flex-col page-element delay-400">
            <div class="swiper mySwiper w-full h-full">
                <div class="swiper-wrapper">
                    <!-- Slide 1 -->
                    <div class="swiper-slide flex flex-col items-center justify-center text-center px-10">
                        <div class="w-full max-w-lg space-y-6">
                            <div class="h-72 flex items-center justify-center mb-6">
                                <img src="{{ asset('images/hangar.png') }}" alt="Airplane Maintenance" class="max-w-full max-h-72 object-contain rounded-2xl shadow-2xl">
                            </div>
                            <div class="space-y-4">
                                <h2 class="text-3xl font-bold text-[#7EBB1A] leading-tight">
                                    Reliability Report System
                                </h2>
                                <p class="text-lg text-gray-300 leading-relaxed">
                                    Manajemen laporan keandalan untuk pemeliharaan pesawat dengan sistem yang terintegrasi
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Slide 2 -->
                    <div class="swiper-slide flex flex-col items-center justify-center text-center px-10">
                        <div class="w-full max-w-lg space-y-6">
                            <div class="h-72 flex items-center justify-center mb-6">
                                <img src="{{ asset('images/hangar1.png') }}" alt="Data Analytics" class="max-w-full max-h-72 object-contain rounded-2xl shadow-2xl">
                            </div>
                            <div class="space-y-4">
                                <h2 class="text-3xl font-bold text-[#7EBB1A] leading-tight">
                                    Analisis Data Terpadu
                                </h2>
                                <p class="text-lg text-gray-300 leading-relaxed">
                                    Visualisasi dan analisis data untuk pengambilan keputusan yang lebih baik
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Slide 3 -->
                    <div class="swiper-slide flex flex-col items-center justify-center text-center px-10">
                        <div class="w-full max-w-lg space-y-6">
                            <div class="h-72 flex items-center justify-center mb-6">
                                <img src="{{ asset('images/hangar2.png') }}" alt="Collaboration" class="max-w-full max-h-72 object-contain rounded-2xl shadow-2xl">
                            </div>
                            <div class="space-y-4">
                                <h2 class="text-3xl font-bold text-[#7EBB1A] leading-tight">
                                    Tim Kolaborasi
                                </h2>
                                <p class="text-lg text-gray-300 leading-relaxed">
                                    Kerja tim yang efisien dengan fitur kolaborasi real-time untuk semua departemen
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Custom Pagination -->
            <div class="flex justify-center mt-8 mb-4 space-x-2">
                <span class="custom-bullet" data-index="0"></span>
                <span class="custom-bullet" data-index="1"></span>
                <span class="custom-bullet" data-index="2"></span>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize page animations
            setTimeout(() => {
                document.querySelectorAll('.page-element').forEach(el => {
                    el.classList.add('animate-in');
                });
            }, 100);

            // Swiper initialization
            if (typeof Swiper !== 'undefined') {
                const swiper = new Swiper(".mySwiper", {
                    spaceBetween: 30,
                    centeredSlides: true,
                    autoplay: {
                        delay: 5000,
                        disableOnInteraction: false,
                    },
                    effect: "fade",
                    speed: 1000,
                    fadeEffect: {
                        crossFade: true
                    },
                    on: {
                        slideChange: function() {
                            const activeIndex = this.activeIndex;
                            document.querySelectorAll('.custom-bullet').forEach(bullet => {
                                const index = parseInt(bullet.getAttribute('data-index'));
                                bullet.classList.toggle('active', index === activeIndex);
                            });
                        }
                    }
                });

                // Bullet click handlers
                document.querySelectorAll('.custom-bullet').forEach(bullet => {
                    bullet.addEventListener('click', function() {
                        const slideIndex = parseInt(this.getAttribute('data-index'));
                        if (!isNaN(slideIndex)) {
                            swiper.slideTo(slideIndex);
                        }
                    });
                });

                // Set first bullet as active
                document.querySelector('.custom-bullet[data-index="0"]')?.classList.add('active');
            }

            // Login form submission
            const loginForm = document.getElementById('login-form');
            const pageLoader = document.getElementById('page-loader');
            const loginBtn = document.getElementById('login-btn');

            if (loginForm && pageLoader && loginBtn) {
                loginForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    pageLoader.classList.remove('hidden');
                    pageLoader.classList.add('flex');
                    loginBtn.disabled = true;
                    setTimeout(() => {
                        loginForm.submit();
                    }, 500);
                });
            }
        });
    </script>
</body>
</html>
