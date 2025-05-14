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
        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            overflow-x: hidden;
            transition: background-color 0.5s ease;
        }

        .fade-in {
            opacity: 0;
            animation: fadeIn 0.4s ease forwards;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* .slide-up {
            opacity: 0;
            transform: translateY(10px);
            animation: slideUp 0.5s ease forwards;
        } */

        /* @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        } */

        .bullet-ripple {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 4px;
            height: 4px;
            background: rgba(34, 197, 94, 0.5);
            border-radius: 100%;
            transform: translate(-50%, -50%);
            animation: bulletRipple 0.6s ease-out;
            pointer-events: none;
        }

        @keyframes bulletRipple {
            0% {
                transform: translate(-50%, -50%) scale(1);
                opacity: 0.5;
            }
            100% {
                transform: translate(-50%, -50%) scale(12);
                opacity: 0;
            }
        }

        .input-focused {
            position: relative;
        }

        .input-focused::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100%;
            height: 2px;
            background: rgba(34, 197, 94, 0.5);
            transform: scaleX(0);
            transform-origin: left;
            animation: inputFocus 0.3s forwards ease-out;
        }

        @keyframes inputFocus {
            to {
                transform: scaleX(1);
            }
        }

        .swiper {
            width: 100%;
            height: 100%;
            position: relative;
        }

        .swiper-slide {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 0 40px;
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .swiper-slide-active {
            opacity: 1;
        }

        .custom-pagination {
            text-align: center;
            margin-top: 10px;
            margin-bottom: 20px;
        }

        .custom-bullet {
            display: inline-block;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background-color: #ffffff;
            opacity: 0.5;
            margin: 0 4px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .custom-bullet.active {
            background-color: #22c55e;
            opacity: 1;
            width: 8px;
            height: 8px;
        }

        .animated-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }

        .bg-gradient {
            position: absolute;
            width: 100%;
            height: 100%;
            background: radial-gradient(ellipse at top right, rgba(34, 197, 94, 0.08) 0%, rgba(15, 8, 38, 0) 70%),
                        radial-gradient(ellipse at bottom left, rgba(34, 197, 94, 0.08) 0%, rgba(15, 8, 38, 0) 70%);
            opacity: 0.8;
            animation: gentlePulse 10s infinite alternate ease-in-out;
        }

        @keyframes gentlePulse {
            0% {
                opacity: 0.7;
                transform: scale(1);
            }
            100% {
                opacity: 0.9;
                transform: scale(1.02);
            }
        }

        .slide-image-container {
            height: 280px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        .slide-image {
            max-width: 100%;
            max-height: 280px;
            object-fit: contain;
            opacity: 0;
            transform: translateY(10px);
            transition: opacity 0.5s ease, transform 0.5s ease;
        }

        .swiper-slide-active .slide-image {
            opacity: 1;
            transform: translateY(0);
        }

        .slide-content {
            width: 100%;
            max-width: 32rem;
        }

        .slide-text-content {
            margin-top: 20px;
            opacity: 0;
            transform: translateY(8px);
            transition: opacity 0.5s ease 0.2s, transform 0.5s ease 0.2s;
        }

        .swiper-slide-active .slide-text-content {
            opacity: 1;
            transform: translateY(0);
        }

        .form-container {
            width: 100%;
            max-width: 400px;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border-radius: 0.375rem;
            border: 1px solid #374151;
            background-color: rgba(255, 255, 255, 0.8);
            color: #111827;
            transition: all 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: #22c55e;
            box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.15);
            background-color: rgba(255, 255, 255, 0.95);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            border-radius: 0.375rem;
            padding: 0.5rem 1.5rem;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: #22c55e;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .btn-primary:hover {
            background-color: #16a34a;
            transform: translateY(-1px);
            box-shadow: 0 3px 5px rgba(0, 0, 0, 0.1);
        }

        .btn-primary:active {
            transform: translateY(0);
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .btn-primary::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 5px;
            height: 5px;
            background: rgba(255, 255, 255, 0.5);
            opacity: 0;
            border-radius: 100%;
            transform: scale(1, 1) translate(-50%, -50%);
            transform-origin: 50% 50%;
        }

        .btn-primary:focus:not(:active)::after {
            animation: ripple 0.6s ease-out;
        }

        @keyframes ripple {
            0% {
                transform: scale(0, 0);
                opacity: 0.5;
            }
            100% {
                transform: scale(20, 20);
                opacity: 0;
            }
        }

        @keyframes logoEntry {
            0% {
                opacity: 0;
                transform: translateY(-20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-logo-entry {
            animation: logoEntry 0.5s ease forwards;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg) scale(1); }
            25% { transform: translateY(-20px) rotate(5deg) scale(1.05); }
            50% { transform: translateY(0) rotate(0deg) scale(1); }
            75% { transform: translateY(20px) rotate(-5deg) scale(0.95); }
        }

        /* New Fade and Slide Up Animation */
        @keyframes fadeSlideUp {
            0% {
                opacity: 0;
                transform: translateY(40px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in-up {
            animation: fadeSlideUp 1s ease-out forwards;
        }
    </style>
</head>

<body class="bg-[#0F0826] text-white font-sans min-h-screen overflow-hidden relative" x-data="{ showLogin: false }">
    <div class="animated-bg">
        <div class="bg-gradient"></div>
    </div>

    <div class="flex flex-col md:flex-row items-center justify-between h-screen relative z-10 container mx-auto px-4 md:px-12">
        <div class="w-full md:w-1/2 p-6 md:p-12 flex flex-col justify-center">
            <div class="mb-8">
                <img src="{{ asset('images/gmfwhite.png') }}" alt="Logo GMF" 
                    class="h-24 animate-logo-entry"
                    :class="h-24 animate-logo-entry">
            </div>


            <div 
                x-show="!showLogin"
                x-transition:enter="transition duration-100 ease-out"
                x-transition:enter-start="opacity-0 translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="space-y-5 max-w-lg"
            >
                <h4 class="text-sm uppercase tracking-wider text-gray-400 fade-in">Selamat Datang di</h4>
                <h1 class="text-5xl font-light leading-tight fade-in" style="animation-delay: 0.1s">Reliability Report</h1>
                <p class="text-gray-300 max-w-md fade-in" style="animation-delay: 0.2s">
                    Platform laporan keandalan pesawat yang membantu memantau performa operasional dan mendukung pengambilan keputusan berbasis data.
                </p>
                <button 
                    @click="showLogin = true"
                    class="inline-block bg-green-500 hover:bg-green-600 text-white font-semibold px-6 py-3 rounded shadow-sm transition-all duration-300 fade-in hover:shadow-md"
                    style="animation-delay: 0.3s"
                >
                    MASUK
                </button>
                <p class="text-xs text-gray-500 mt-8 fade-in" style="animation-delay: 0.4s">
                    © {{ date('Y') }} PT Garuda Maintenance Facility Aero Asia Tbk
                </p>
            </div>

            <div 
                x-show="showLogin"
                x-transition:enter="transition duration-400 ease-out"
                x-transition:enter-start="opacity-0 translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition duration-300 ease-in"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="w-full max-w-lg fade-in-up"
            >
                <h2 class="text-4xl font-light mb-6">Sign in to your account</h2>

                <div class="w-full max-w-md" style="animation-delay: 0.1s">
                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf
                        <div class="fade-in" style="animation-delay: 0.15s">
                            <label for="email" class="block text-sm text-white">Email</label>
                            <input 
                                id="email" 
                                class="form-input mt-1" 
                                type="email" 
                                name="email" 
                                value="{{ old('email') }}" 
                                required 
                                autofocus 
                                autocomplete="username" 
                            />
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="fade-in" style="animation-delay: 0.2s">
                            <label for="password" class="block text-sm text-white">Password</label>
                            <input 
                                id="password" 
                                class="form-input mt-1" 
                                type="password" 
                                name="password" 
                                required 
                                autocomplete="current-password" 
                            />
                            @error('password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-between text-sm text-gray-300 fade-in" style="animation-delay: 0.25s">
                            <div class="flex items-center">
                                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-green-500 shadow-sm focus:ring-green-500" name="remember">
                                <label for="remember_me" class="ms-2">Remember me</label>
                            </div>
                            @if (Route::has('password.request'))
                                <a class="underline text-gray-400 hover:text-white transition-colors duration-200" href="{{ route('password.request') }}">
                                    Forgot your password?
                                </a>
                            @endif
                        </div>

                        <div class="fade-in" style="animation-delay: 0.3s">
                            <button type="submit" class="btn btn-primary w-full">LOG IN</button>
                        </div>

                        <div class="text-left mt-4 fade-in" style="animation-delay: 0.35s">
                            <p>
                                Belum punya akun?
                                <a href="{{ route('register') }}" class="text-sm text-white hover:underline transition-all duration-200">
                                    <span class="text-green-500 font-semibold"> Daftar di sini</span>
                                </a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="hidden md:block md:w-1/2 h-full bg-[#0F0826]/10 backdrop-blur-sm px-4 md:px-12">
            <div class="swiper mySwiper h-full w-full relative">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <div class="slide-content">
                            <div class="slide-image-container">
                                <img src="{{ asset('images/hangar.png') }}" alt="Airplane Maintenance" class="slide-image">
                            </div>
                            <div class="slide-text-content">
                                <h2 class="text-3xl font-bold mb-4 text-green-500">Reliability Report System</h2>
                                <p class="text-lg text-gray-200">Manajemen laporan keandalan untuk pemeliharaan pesawat dengan sistem yang terintegrasi</p>
                            </div>
                        </div>
                    </div>

                    <div class="swiper-slide">
                        <div class="slide-content">
                            <div class="slide-image-container">
                                <img src="{{ asset('images/hangar1.png') }}" alt="Data Analytics" class="slide-image">
                            </div>
                            <div class="slide-text-content">
                                <h2 class="text-3xl font-bold mb-4 text-green-500">Analisis Data Terpadu</h2>
                                <p class="text-lg text-gray-200">Visualisasi dan analisis data untuk pengambilan keputusan yang lebih baik</p>
                            </div>
                        </div>
                    </div>

                    <div class="swiper-slide">
                        <div class="slide-content">
                            <div class="slide-image-container">
                                <img src="{{ asset('images/hangar2.png') }}" alt="Collaboration" class="slide-image">
                            </div>
                            <div class="slide-text-content">
                                <h2 class="text-3xl font-bold mb-4 text-green-500">Tim Kolaborasi</h2>
                                <p class="text-lg text-gray-200">Kerja tim yang efisien dengan fitur kolaborasi real-time untuk semua departemen</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="custom-pagination absolute bottom-10 left-1/2 transform -translate-x-1/2 z-10">
                    <span class="custom-bullet active" data-index="0"></span>
                    <span class="custom-bullet" data-index="1"></span>
                    <span class="custom-bullet" data-index="2"></span>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.addEventListener('load', function() {
                const swiper = new Swiper(".mySwiper", {
                    spaceBetween: 30,
                    centeredSlides: true,
                    autoplay: {
                        delay: 5000,
                        disableOnInteraction: false,
                    },
                    effect: "fade",
                    speed: 500,
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
        
                // Click bullets to change slide
                document.querySelectorAll('.custom-bullet').forEach(bullet => {
                    bullet.addEventListener('click', function() {
                        const slideIndex = parseInt(this.getAttribute('data-index'));
                        if (!isNaN(slideIndex)) {
                            swiper.slideTo(slideIndex);
                        }
                    });
                });
        
                // Set first bullet as active by default
                document.querySelector('.custom-bullet[data-index="0"]').classList.add('active');
            });
        });
    </script>
</body>
</html>
