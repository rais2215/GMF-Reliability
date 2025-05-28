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
        }
        .custom-pagination {
            text-align: center;
            margin-top: 10px;
            margin-bottom: 20px;
        }
        .custom-bullet {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #ffffff;
            opacity: 0.5;
            margin: 0 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .custom-bullet.active {
            background-color: #22c55e;
            opacity: 1;
            width: 12px;
            height: 12px;
            transform: translateY(-2px);
        }
        .animated-bg {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            overflow: hidden; z-index: 0;
        }
        .bg-circle {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(45deg, rgba(34, 197, 94, 0.3), rgba(15, 8, 38, 0.1));
            filter: blur(30px);
            animation: float 20s infinite ease-in-out;
        }
        .bg-circle:nth-child(1) { width: 500px; height: 500px; top: -100px; right: -100px; animation-delay: 0s; }
        .bg-circle:nth-child(2) { width: 300px; height: 300px; bottom: -50px; left: 10%; animation-delay: -5s; }
        .bg-circle:nth-child(3) { width: 200px; height: 200px; bottom: 30%; right: 20%; animation-delay: -10s; }
        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg) scale(1);}
            25% { transform: translateY(-20px) rotate(5deg) scale(1.05);}
            50% { transform: translateY(0) rotate(0deg) scale(1);}
            75% { transform: translateY(20px) rotate(-5deg) scale(0.95);}
        }
        .slide-image-container {
            height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }
        .slide-image {
            max-width: 100%;
            max-height: 300px;
            object-fit: contain;
        }
        .slide-content { width: 100%; max-width: 32rem; }
        .slide-text-content { margin-top: 20px; }
        .form-container { width: 100%; max-width: 400px; }
        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border-radius: 0.375rem;
            border: 1px solid #374151;
            background-color: rgba(255, 255, 255, 0.9);
            color: #111827;
            transition: all 0.3s ease;
        }
        .form-input:focus {
            outline: none;
            border-color: #22c55e;
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.2);
            transform: translateY(-1px);
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
        }
        .btn-primary:hover {
            background-color: #16a34a;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .btn-primary:active {
            transform: translateY(0);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="bg-[#112955] text-white font-sans min-h-screen overflow-hidden relative" x-data="{ showLogin: false }">
    <!-- Animated Background Circles -->
    <div class="animated-bg">
        <div class="bg-circle"></div>
        <div class="bg-circle"></div>
        <div class="bg-circle"></div>
    </div>

    <!-- Main Wrapper -->
    <div class="flex flex-col md:flex-row items-center justify-between min-h-screen relative z-10 container mx-auto px-4 md:px-12 py-8">
        <!-- Left Content (Welcome / Login Transition) -->
        <div class="w-full md:w-1/2 flex flex-col justify-center min-h-[500px] p-6 md:p-12">
            <!-- Logo -->
            <div class="mb-10 flex justify-center md:justify-start">
                <img src="{{ asset('images/gmfwhite.png') }}" alt="Logo GMF" class="h-24">
            </div>
            <!-- Container untuk Welcome & Login -->
            <div class="relative min-h-[400px]">
                <!-- Welcome Section -->
                <div 
                    x-show="!showLogin"
                    x-transition:enter="transition-all transform duration-700 ease-out"
                    x-transition:enter-start="opacity-0 translate-x-12"
                    x-transition:enter-end="opacity-100 translate-x-0"
                    x-transition:leave="transition-all transform duration-500 ease-in"
                    x-transition:leave-start="opacity-100 translate-x-0"
                    x-transition:leave-end="opacity-0 -translate-x-12"
                    class="space-y-6 max-w-lg absolute top-0 left-0 w-full bg-transparent"
                >
                    <h4 class="text-sm uppercase tracking-wider text-white-500">Selamat Datang di</h4>
                    <h1 class="text-5xl font-light leading-tight">Reliability Report</h1>
                    <p class="text-white-500 max-w-md">
                        Platform laporan keandalan pesawat yang membantu memantau performa operasional dan mendukung pengambilan keputusan berbasis data.
                    </p>
                    <button 
                        @click="showLogin = true"
                        class="inline-block transition-all duration-300 ease-in-out"
                        style="background-color: #7EBB1A; color: #fff; font-weight: 600; padding: 0.75rem 1.5rem; border-radius: 0.375rem; box-shadow: 0 2px 6px rgba(0,0,0,0.08); border: none; transform: translateY(0);"
                        onmouseover="this.style.backgroundColor='#8DC63F'; this.style.transform='translateY(-4px)';"
                        onmouseout="this.style.backgroundColor='#7EBB1A'; this.style.transform='translateY(0)';"
                    >
                        MASUK
                    </button>
                    <p class="text-xs text-white-500 mt-10">
                        © {{ date('Y') }} PT Garuda Maintenance Facility AeroAsia Tbk
                    </p>
                </div>
                <!-- Login Section -->
                <div 
                    x-show="showLogin"
                    x-transition:enter="transition-all transform duration-700 ease-out"
                    x-transition:enter-start="opacity-0 translate-x-12"
                    x-transition:enter-end="opacity-100 translate-x-0"
                    x-transition:leave="transition-all transform duration-500 ease-in"
                    x-transition:leave-start="opacity-100 translate-x-0"
                    x-transition:leave-end="opacity-0 -translate-x-12"
                    class="w-full max-w-lg absolute top-0 left-0 bg-transparent"
                >
                    <h2 class="text-4xl font-light leading-tight mb-8">Sign in to your account</h2>
                    <!-- Form Wrapper -->
                    <div class="w-full max-w-md">
                        <form method="POST" action="{{ route('login') }}" class="space-y-6">
                        @csrf
                        <!-- Email Address -->
                        <div>
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
                        <!-- Password -->
                        <div>
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
                        <!-- Remember Me & Forgot Password -->
                        <div class="flex items-center justify-between text-sm text-white-300">
                            <div class="flex items-center">
                                <input id="remember_me" type="checkbox" class="rounded border-white-300 text-green-500 shadow-sm focus:ring-green-500" name="remember">
                                <label for="remember_me" class="ms-2">Remember me</label>
                            </div>
                            @if (Route::has('password.request'))
                                <a class="underline text-gray-400 hover:text-white" href="{{ route('password.request') }}">
                                    Forgot your password?
                                </a>
                            @endif
                        </div>
                        <!-- Action Button -->
                        <div>
                            <button 
                                type="submit" 
                                class="w-full inline-block transition-all duration-300 ease-in-out"
                                style="background-color: #7EBB1A; color: #fff; font-weight: 600; padding: 0.75rem 1.5rem; border-radius: 0.375rem; box-shadow: 0 2px 6px rgba(0,0,0,0.08); border: none; transform: translateY(0);"
                                onmouseover="this.style.backgroundColor='#8DC63F'; this.style.transform='translateY(-4px)';"
                                onmouseout="this.style.backgroundColor='#7EBB1A'; this.style.transform='translateY(0)';"
                            >
                                LOG IN
                            </button>
                        </div>
                        <!-- Link to Register aligned right -->
                        <div class="text-left mt-4">
                            <p>
                                Belum punya akun?
                                <a 
                                    href="{{ route('register') }}" 
                                    class="text-sm text-white hover:underline"
                                    >
                                    <span class="font-semibold" style="color: #7EBB1A;"> Daftar di sini</span>
                                </a>
                            </p>
                        </div>
                        <!-- Tombol Kembali -->
                        <div class="mt-6">
                            <button 
                                @click="showLogin = false" 
                                type="button" 
                                class="text-sm font-semibold hover:underline"
                                style="color: #7EBB1A;"
                            >
                                ← Kembali ke halaman awal
                            </button>
                        </div>
                    </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Right: Carousel -->
        <div class="hidden md:flex md:w-1/2 h-full bg-[#112955]/10 backdrop-blur-sm px-4 md:px-12 py-8 items-center flex-col">
            <div class="swiper mySwiper w-full">
                <div class="swiper-wrapper">
                    <!-- Slide 1 -->
                    <div class="swiper-slide">
                        <div class="slide-content">
                            <div class="slide-image-container">
                                <img src="{{ asset('images/hangar.png') }}" alt="Airplane Maintenance" class="slide-image">
                            </div>
                            <div class="slide-text-content">
                                <h2 class="text-3xl font-bold mb-4" style="color: #7EBB1A;">Reliability Report System</h2>
                                <p class="text-lg text-white-400">Manajemen laporan keandalan untuk pemeliharaan pesawat dengan sistem yang terintegrasi</p>
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
                                <h2 class="text-3xl font-bold mb-4" style="color: #7EBB1A;">Analisis Data Terpadu</h2>
                                <p class="text-lg text-white-400">Visualisasi dan analisis data untuk pengambilan keputusan yang lebih baik</p>
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
                                <h2 class="text-3xl font-bold mb-4" style="color: #7EBB1A;">Tim Kolaborasi</h2>
                                <p class="text-lg text-white-400">Kerja tim yang efisien dengan fitur kolaborasi real-time untuk semua departemen</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Custom Pagination di luar swiper, center dan bisa di klik -->
            <div class="custom-pagination flex justify-center mt-6 mb-2 w-full">
                <span class="custom-bullet" data-index="0"></span>
                <span class="custom-bullet" data-index="1"></span>
                <span class="custom-bullet" data-index="2"></span>
            </div>
        </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
    </script>
</body>
</html>