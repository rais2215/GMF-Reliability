<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login | GMF Reliability Report</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- AlpineJS -->
    <script src="https://unpkg.com/alpinejs" defer></script>
    
    <!-- Swiper JS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/9.2.0/swiper-bundle.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/9.2.0/swiper-bundle.min.js"></script>
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
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
        }

        .custom-pagination {
            text-align: center;
            margin-top: 10px;
            margin-bottom: 20px;
        }

        .custom-bullet {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: #ffffff;
            opacity: 0.5;
            margin: 0 5px;
            cursor: pointer;
        }

        .custom-bullet.active {
            background-color: #22c55e;
            opacity: 1;
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

        .bg-circle {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(45deg, rgba(34, 197, 94, 0.3), rgba(15, 8, 38, 0.1));
            filter: blur(30px);
            animation: float 20s infinite ease-in-out;
        }

        .bg-circle:nth-child(1) {
            width: 500px;
            height: 500px;
            top: -100px;
            right: -100px;
            animation-delay: 0s;
        }

        .bg-circle:nth-child(2) {
            width: 300px;
            height: 300px;
            bottom: -50px;
            left: 10%;
            animation-delay: -5s;
        }

        .bg-circle:nth-child(3) {
            width: 200px;
            height: 200px;
            bottom: 30%;
            right: 20%;
            animation-delay: -10s;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0) rotate(0deg);
            }
            25% {
                transform: translateY(-20px) rotate(5deg);
            }
            50% {
                transform: translateY(0) rotate(0deg);
            }
            75% {
                transform: translateY(20px) rotate(-5deg);
            }
        }

        .slide-image-container {
            height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0;
        }

        .slide-image {
            max-width: 100%;
            max-height: 300px;
            object-fit: contain;
        }

        .slide-content {
            width: 100%;
            max-width: 32rem;
        }
        
        .slide-text-content {
            margin-top: 10px;
        }
    </style>
</head>
<body class="bg-[#0F0826] text-white font-sans min-h-screen overflow-hidden relative">
    <!-- Animated Background -->
    <div class="animated-bg">
        <div class="bg-circle"></div>
        <div class="bg-circle"></div>
        <div class="bg-circle"></div>
    </div>

    <div class="flex flex-col md:flex-row items-center justify-between h-screen relative z-10">
        <!-- Left: Login Form -->
        <div class="px-8 md:px-12 md:w-1/2 text-left flex justify-center py-8 md:py-0">
            <div class="w-full max-w-md space-y-6">
                <img src="{{ asset('images/gmfwhite.png') }}" alt="Logo GMF" class="h-20 mb-4">
                <h1 class="text-3xl md:text-4xl font-light">Sign in to your account</h1>

                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <x-input-label for="email" :value="'Email'" class="text-white" />
                        <x-text-input id="email" class="block mt-1 w-full text-black" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div>
                        <x-input-label for="password" :value="'Password'" class="text-white" />
                        <x-text-input id="password" class="block mt-1 w-full text-black" type="password" name="password" required autocomplete="current-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between text-sm text-gray-300">
                        <div class="flex items-center">
                            <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-green-500 shadow-sm focus:ring-green-500" name="remember">
                            <label for="remember_me" class="ms-2">Remember me</label>
                        </div>

                        @if (Route::has('password.request'))
                            <a class="underline text-gray-400 hover:text-white" href="{{ route('password.request') }}">
                                Forgot your password?
                            </a>
                        @endif
                    </div>

                    <!-- Buttons -->
                    <div class="mt-6 flex justify-center gap-4">
                        <button type="submit" class="px-6 py-2 bg-green-500 text-white text-sm font-semibold rounded-lg shadow hover:bg-green-600 transition">
                            LOG IN
                        </button>
                        <a href="{{ route('register') }}" class="inline-block px-6 py-2 bg-green-500 text-white text-sm font-semibold rounded-lg shadow hover:bg-green-600 transition">
                            REGISTER
                        </a>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Right: Carousel -->
        <div class="hidden md:block md:w-1/2 h-full bg-[#0F0826]/10 backdrop-blur-sm">
            <div class="swiper mySwiper h-full w-full relative">
                <div class="swiper-wrapper">
                    <!-- Slide 1 -->
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

                    <!-- Slide 2 -->
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

                    <!-- Slide 3 -->
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

                <!-- ✅ Global Pagination (dipindah ke luar slide) -->
                <div class="custom-pagination absolute bottom-6 left-1/2 transform -translate-x-1/2 z-10">
                    <span class="custom-bullet" data-index="0"></span>
                    <span class="custom-bullet" data-index="1"></span>
                    <span class="custom-bullet" data-index="2"></span>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            window.addEventListener('load', function () {
                const swiper = new Swiper(".mySwiper", {
                    spaceBetween: 30,
                    centeredSlides: true,
                    autoplay: {
                        delay: 5000,
                        disableOnInteraction: false,
                    },
                    effect: "fade",
                    fadeEffect: {
                        crossFade: true
                    },
                    on: {
                        slideChange: function () {
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
                    bullet.addEventListener('click', function () {
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