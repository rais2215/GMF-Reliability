<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>GMF | Reliability Report</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- External Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css">
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* ==============================================
           GLOBAL STYLES
        ============================================== */
        html, body {
            height: 100vh;
            overflow: hidden;
        }

        * {
            transition: transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94),
                        opacity 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        /* ==============================================
           CUSTOM ANIMATIONS
        ============================================== */
        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg) scale(1); }
            25% { transform: translateY(-20px) rotate(5deg) scale(1.05); }
            50% { transform: translateY(0) rotate(0deg) scale(1); }
            75% { transform: translateY(20px) rotate(-5deg) scale(0.95); }
        }

        @keyframes loader-bounce {
            0%, 100% {
                transform: scaleY(0.3);
                opacity: 0.5;
            }
            50% {
                transform: scaleY(1.2);
                opacity: 1;
            }
        }

        @keyframes progress-fill {
            0% {
                width: 0%;
            }
            100% {
                width: 100%;
            }
        }

        @keyframes fade-up {
            0% { transform: translateY(40px) scale(0.95); opacity: 0; }
            100% { transform: translateY(0) scale(1); opacity: 1; }
        }

        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(126, 187, 26, 0.3); }
            50% { box-shadow: 0 0 40px rgba(126, 187, 26, 0.6); }
        }

        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }

        /* ==============================================
           BACKGROUND ELEMENTS
        ============================================== */
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

        .bg-circle:nth-child(4) {
            width: 150px; height: 150px;
            top: 20%; left: 5%;
            animation-delay: -15s;
        }

        /* ==============================================
           LAYOUT COMPONENTS
        ============================================== */
        .section-wrapper {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.9s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        .content-container {
            transition: all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            position: relative;
            overflow: hidden;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 25px 45px rgba(0, 0, 0, 0.1);
        }

        /* ==============================================
           LOADER CLASSES
        ============================================== */
        .loader-bar {
            animation: loader-bounce 1.4s infinite ease-in-out;
        }

        .loader-delay-1 {
            animation-delay: 0.16s;
        }

        .loader-delay-2 {
            animation-delay: 0.32s;
        }

        .progress-bar {
            animation: progress-fill 3s ease-in-out infinite;
        }

        /* ==============================================
           FORM & BUTTON STYLES
        ============================================== */
        .btn-primary {
            background: linear-gradient(135deg, #7EBB1A 0%, #8DC63F 100%);
            position: relative;
            overflow: hidden;
            transition: all 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        .btn-primary:active {
            transform: translateY(-1px) scale(0.98);
        }

        .input-field {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.3);
            transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        .input-field:focus {
            background: rgba(255, 255, 255, 1);
            border-color: #7EBB1A;
            box-shadow: 0 0 0 4px rgba(126, 187, 26, 0.1);
            transform: translateY(-2px);
        }

        /* ==============================================
           ANIMATION CLASSES
        ============================================== */
        .animate-loader-bar { animation: loader-bounce 1.4s infinite ease-in-out; }
        .animate-fade-up { animation: fade-up 0.8s cubic-bezier(0.4, 2, 0.6, 1) forwards; }
        .animate-pulse-glow { animation: pulse-glow 2s infinite; }
        .animate-shimmer {
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            background-size: 200% 100%;
            animation: shimmer 2s infinite;
        }

        .page-element {
            opacity: 0;
            transform: translateY(40px) scale(0.95);
        }

        .page-element.animate-in {
            animation: fade-up 0.8s cubic-bezier(0.4, 2, 0.6, 1) forwards;
        }

        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-400 { animation-delay: 0.4s; }

        /* ==============================================
           SWIPER COMPONENTS
        ============================================== */
        .feature-card {
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(126, 187, 26, 0.1), transparent);
            transition: left 0.8s ease;
        }

        .feature-card:hover::before {
            left: 100%;
        }

        .feature-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 25px 50px rgba(0,0,0,0.15);
            border-color: rgba(126, 187, 26, 0.4);
        }

        .swiper-button-next:after,
        .swiper-button-prev:after {
            font-size: 16px !important;
            font-weight: 600 !important;
        }

        .swiper-button-next,
        .swiper-button-prev {
            margin-top: 0 !important;
            transform: translateY(-50%);
        }

        .swiper-button-next:hover,
        .swiper-button-prev:hover {
            transform: translateY(-50%) scale(1.1);
        }

        .swiper-progress {
            transition: width 0.5s ease;
        }

        .current-slide {
            color: #7EBB1A;
            font-weight: bold;
        }

        /* ==============================================
           RESPONSIVE DESIGN
        ============================================== */
        @media (max-width: 1024px) {
            .feature-card {
                max-width: 100%;
            }

            .swiper-button-next,
            .swiper-button-prev {
                display: none !important;
            }
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem !important;
            }
        }

        @media (max-width: 640px) {
            .hero-title {
                font-size: 2rem !important;
            }
        }
    </style>
</head>

<body class="bg-[#112955] text-white font-sans h-screen overflow-hidden relative flex" x-data="{
    showLogin: false,
    currentSlide: 0,
    isTransitioning: false,
    smoothTransition(state) {
        if (this.isTransitioning) return;
        this.isTransitioning = true;

        const container = this.$el.querySelector('.content-container');
        if (container) {
            container.style.willChange = 'transform, opacity';
        }

        setTimeout(() => {
            this.showLogin = state;
            setTimeout(() => {
                this.isTransitioning = false;
                if (container) {
                    container.style.willChange = 'auto';
                }
            }, 900);
        }, 100);
    }
}">
    <!-- ==============================================
         ANIMATED BACKGROUND
    ============================================== -->
    <div class="absolute inset-0 overflow-hidden z-0">
        <div class="bg-circle"></div>
        <div class="bg-circle"></div>
        <div class="bg-circle"></div>
        <div class="bg-circle"></div>
    </div>

    <!-- ==============================================
         LOGIN LOADER - Fixed to match edit.blade.php
    ============================================== -->
    <div id="page-loader" class="fixed inset-0 z-50 hidden flex-col items-center justify-center bg-[#112955]/90 backdrop-blur-lg transition-all duration-500">
        <div class="glass-card rounded-3xl shadow-2xl p-12 border border-white/20 max-w-sm w-full mx-4 bg-white/10 backdrop-blur-xl">
            <div class="text-center space-y-4">
                <span id="loader-text" class="text-xl font-semibold text-white block">Logging in...</span>
                <p class="text-sm text-gray-300">Please wait while we authenticate your credentials</p>
            </div>
            <div class="mt-6 w-full h-2 bg-white/20 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-[#7EBB1A] to-[#8DC63F] rounded-full progress-bar"></div>
            </div>
        </div>
    </div>

    <!-- ==============================================
         TOP NAVIGATION
    ============================================== -->
    <nav class="absolute top-0 left-0 right-0 z-30 p-6">
        <div class="container mx-auto flex justify-between items-center">
            <div class="page-element delay-100">
                <img
                    src="{{ asset('images/gmfwhite.png') }}"
                    alt="GMF Logo"
                    class="h-16 lg:h-20 w-auto hover:scale-110 transition-transform duration-300 drop-shadow-2xl"
                >
            </div>
        </div>
    </nav>

    <!-- ==============================================
     LEFT CONTENT SECTION
    ============================================== -->
    <div class="w-full lg:w-1/2 flex flex-col justify-center h-full p-4 lg:p-8" style="min-height: 100vh;">
        <div class="relative h-full flex items-center justify-center content-container" style="min-height: 700px; padding: 2rem 0;">

            <!-- Welcome Section -->
            <div
                x-show="!showLogin"
                x-transition:enter="transition-all transform duration-[900ms] ease-out"
                x-transition:enter-start="opacity-0 translate-x-24 scale-90 blur-sm"
                x-transition:enter-end="opacity-100 translate-x-0 scale-100 blur-none"
                x-transition:leave="transition-all transform duration-[700ms] ease-in"
                x-transition:leave-start="opacity-100 translate-x-0 scale-100 blur-none"
                x-transition:leave-end="opacity-0 translate-x-24 scale-90 blur-sm"
                class="section-wrapper"
            >
                <div class="w-full max-w-lg flex flex-col justify-center" style="margin-top: -20px; margin-left: -60px; min-height: 500px;">
                    <div class="space-y-6 lg:space-y-8 page-element delay-200">
                        <!-- Badge -->
                        <div class="inline-flex items-center space-x-2 bg-[#7EBB1A]/20 backdrop-blur-sm border border-[#7EBB1A]/30 rounded-full px-4 py-2 transform transition-all duration-500 hover:scale-105">
                            <div class="w-2 h-2 bg-[#7EBB1A] rounded-full animate-pulse"></div>
                            <span class="text-sm font-medium text-[#7EBB1A]">GMF Reliability Report</span>
                        </div>

                        <!-- Hero Title -->
                        <div class="space-y-4">
                            <h4 class="text-sm uppercase tracking-wider text-gray-300 font-semibold transform transition-all duration-500">Welcome to</h4>
                            <h1 class="hero-title text-5xl lg:text-6xl xl:text-7xl font-light leading-tight bg-gradient-to-r from-white via-gray-100 to-gray-200 bg-clip-text text-transparent transform transition-all duration-500">
                                Reliability<br>
                                <span class="text-[#7EBB1A] font-bold">Report</span>
                            </h1>
                        </div>

                        <!-- Description -->
                        <p class="text-gray-300 max-w-lg text-lg lg:text-xl leading-relaxed transform transition-all duration-500">
                            Aircraft reliability reporting platform that helps monitor operational performance and supports data-driven decision making in real-time.
                        </p>

                        <!-- CTA Button -->
                        <div class="flex flex-col sm:flex-row gap-3 lg:gap-4">
                            <button
                                @click="smoothTransition(true)"
                                class="btn-primary group relative text-white font-semibold px-6 lg:px-8 py-3 lg:py-4 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-600 hover:-translate-y-2 transform overflow-hidden animate-pulse-glow"
                            >
                                <span class="relative z-10 flex items-center justify-center space-x-3">
                                    <i class="fas fa-sign-in-alt transition-transform duration-300 group-hover:rotate-12"></i>
                                    <span>LOGIN NOW</span>
                                    <svg class="w-4 h-4 lg:w-5 lg:h-5 group-hover:translate-x-2 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                    </svg>
                                </span>
                            </button>
                        </div>

                        <!-- Copyright -->
                        <p class="text-xs text-gray-500 font-medium transform transition-all duration-500">
                            © {{ date('Y') }} PT Garuda Maintenance Facility AeroAsia Tbk.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Login Section -->
            <div
                x-show="showLogin"
                x-transition:enter="transition-all transform duration-[900ms] ease-out"
                x-transition:enter-start="opacity-0 translate-x-24 scale-90 blur-sm"
                x-transition:enter-end="opacity-100 translate-x-0 scale-100 blur-none"
                x-transition:leave="transition-all transform duration-[700ms] ease-in"
                x-transition:leave-start="opacity-100 translate-x-0 scale-100 blur-none"
                x-transition:leave-end="opacity-0 translate-x-24 scale-90 blur-sm"
                class="section-wrapper"
            >
                <div class="w-full max-w-lg flex flex-col justify-center" style="margin-top: -40px; margin-left: -60px; min-height: 650px;">
                    <div class="space-y-6 lg:space-y-8">
                        <!-- Login Header -->
                        <div class="text-left space-y-4">
                            <div class="inline-flex items-center space-x-2 bg-[#7EBB1A]/20 backdrop-blur-sm border border-[#7EBB1A]/30 rounded-full px-4 py-2 transform transition-all duration-500 hover:scale-105">
                                <i class="fas fa-lock text-[#7EBB1A]"></i>
                                <span class="text-sm font-medium text-[#7EBB1A]">LOGIN</span>
                            </div>
                            <h2 class="text-3xl lg:text-4xl xl:text-5xl font-light leading-tight bg-gradient-to-r from-white to-gray-200 bg-clip-text text-transparent transform transition-all duration-500">
                                Welcome Back
                            </h2>
                            <p class="text-gray-400 transform transition-all duration-500">Sign in to access your reliability dashboard</p>
                        </div>

                        <!-- Login Form -->
                        <div class="w-full">
                            <form method="POST" action="{{ route('login') }}" class="space-y-5 lg:space-y-6" id="login-form">
                                @csrf

                                <!-- Email Field -->
                                <div class="space-y-2">
                                    <label for="email" class="block text-sm text-white font-medium">Email Address</label>
                                    <div class="relative transform transition-all duration-300 hover:scale-[1.02]">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-envelope text-gray-500"></i>
                                        </div>
                                        <input
                                            id="email"
                                            class="input-field w-full pl-10 pr-4 py-3 lg:py-4 rounded-xl text-gray-900 placeholder-gray-500 focus:outline-none transition-all duration-400 hover:shadow-lg focus:shadow-xl"
                                            type="email"
                                            name="email"
                                            value="{{ old('email') }}"
                                            placeholder="Enter your email address"
                                            required
                                            autofocus
                                            autocomplete="username"
                                        />
                                    </div>
                                    @error('email')
                                        <p class="text-red-400 text-xs mt-1 font-medium flex items-center space-x-1">
                                            <i class="fas fa-exclamation-circle"></i>
                                            <span>{{ $message }}</span>
                                        </p>
                                    @enderror
                                </div>

                                <!-- Password Field -->
                                <div class="space-y-2">
                                    <label for="password" class="block text-sm text-white font-medium">Password</label>
                                    <div class="relative transform transition-all duration-300 hover:scale-[1.02]">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-lock text-gray-500"></i>
                                        </div>
                                        <input
                                            id="password"
                                            class="input-field w-full pl-10 pr-4 py-3 lg:py-4 rounded-xl text-gray-900 placeholder-gray-500 focus:outline-none transition-all duration-400 hover:shadow-lg focus:shadow-xl"
                                            type="password"
                                            name="password"
                                            placeholder="Enter your password"
                                            required
                                            autocomplete="current-password"
                                        />
                                    </div>
                                    @error('password')
                                        <p class="text-red-400 text-xs mt-1 font-medium flex items-center space-x-1">
                                            <i class="fas fa-exclamation-circle"></i>
                                            <span>{{ $message }}</span>
                                        </p>
                                    @enderror
                                </div>

                                <!-- Remember Me & Forgot Password -->
                                <div class="flex items-center justify-between text-sm">
                                    <div class="flex items-center space-x-3">
                                        <input
                                            id="remember_me"
                                            type="checkbox"
                                            class="w-4 h-4 rounded border-gray-300 text-[#7EBB1A] shadow-sm focus:ring-[#7EBB1A] focus:ring-2 transition-all duration-300"
                                            name="remember"
                                        >
                                        <label for="remember_me" class="text-gray-300 font-medium">Remember me</label>
                                    </div>
                                    @if (Route::has('password.request'))
                                        <a class="text-[#7EBB1A] hover:text-[#8DC63F] font-medium underline transition-colors duration-300" href="{{ route('password.request') }}">
                                            Forgot password?
                                        </a>
                                    @endif
                                </div>

                                <!-- Login Button -->
                                <div>
                                    <button
                                        type="submit"
                                        class="btn-primary group w-full text-white font-semibold px-6 py-3 lg:py-4 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-400 hover:-translate-y-2 transform relative overflow-hidden min-h-[48px] lg:min-h-[56px] flex items-center justify-center"
                                        id="login-btn"
                                    >
                                        <span class="relative z-10 flex items-center space-x-3">
                                            <i class="fas fa-sign-in-alt transition-transform duration-300 group-hover:rotate-12"></i>
                                            <span>SIGN IN</span>
                                        </span>
                                    </button>
                                </div>

                                <!-- Divider -->
                                <div class="relative my-5 lg:my-6">
                                    <div class="absolute inset-0 flex items-center">
                                        <div class="w-full border-t border-gray-600"></div>
                                    </div>
                                    <div class="relative flex justify-center text-sm">
                                        <span class="px-2 bg-[#112955] text-gray-400">-</span>
                                    </div>
                                </div>

                                <!-- Register Link -->
                                <div class="text-left">
                                    <p class="text-gray-300 text-sm">
                                        Don't have an account?
                                        <a
                                            href="{{ route('register') }}"
                                            class="text-[#7EBB1A] hover:text-[#8DC63F] font-semibold hover:underline transition-all duration-300 ml-1"
                                        >
                                            Register here
                                        </a>
                                    </p>
                                </div>

                                <!-- Back Button -->
                                <div class="mt-6 lg:mt-8">
                                    <button
                                        @click="smoothTransition(false)"
                                        type="button"
                                        class="group text-[#7EBB1A] hover:text-[#8DC63F] font-semibold transition-all duration-500 flex items-center space-x-2 text-sm hover:translate-x-2"
                                    >
                                        <svg class="w-4 h-4 group-hover:-translate-x-3 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                        </svg>
                                        <span>Back to homepage</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ==============================================
         RIGHT CAROUSEL SECTION
    ============================================== -->
    <div class="w-full lg:w-1/2 h-full px-4 lg:px-8 py-8 items-center justify-center flex-col page-element delay-400 flex">
        <!-- Swiper Container -->
        <div class="swiper mySwiper w-full max-w-2xl relative flex-shrink-0 ml-8" style="height: 520px;">
            <div class="swiper-wrapper">
                <!-- Slide 1 - Reliability Report System -->
                <div class="swiper-slide flex items-center justify-center text-center px-4">
                    <div class="feature-card w-full max-w-lg space-y-6 bg-white/5 backdrop-blur-sm rounded-3xl p-8 border border-white/10 hover:border-[#7EBB1A]/30 transition-all duration-500 mx-auto">
                        <div class="relative group mx-auto">
                            <div class="absolute inset-0 bg-[#7EBB1A]/20 rounded-2xl blur-xl animate-pulse group-hover:bg-[#7EBB1A]/30 transition-all duration-500"></div>
                            <div class="relative h-48 lg:h-56 flex items-center justify-center mb-6 overflow-hidden rounded-2xl bg-white/5">
                                <img
                                    src="{{ asset('images/hangar.png') }}"
                                    alt="Airplane Maintenance"
                                    class="max-w-full max-h-full object-contain rounded-xl shadow-xl transform group-hover:scale-105 transition-transform duration-500"
                                >
                            </div>
                        </div>
                        <div class="space-y-5 text-center">
                            <div class="flex items-center justify-center space-x-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-[#7EBB1A] to-[#8DC63F] rounded-xl flex items-center justify-center shadow-lg flex-shrink-0">
                                    <i class="fas fa-file-alt text-white text-xl"></i>
                                </div>
                                <h2 class="text-xl lg:text-2xl font-bold text-[#7EBB1A] leading-tight">
                                    Reliability Report System
                                </h2>
                            </div>
                            <p class="text-base text-gray-300 leading-relaxed max-w-md mx-auto">
                                Integrated system for managing and monitoring aircraft reliability reports efficiently, supporting technical evaluation and strategic decision-making.
                            </p>
                            <div class="flex flex-wrap justify-center gap-2 mt-5">
                                <span class="px-4 py-2 bg-[#7EBB1A]/20 text-[#7EBB1A] text-sm rounded-full border border-[#7EBB1A]/30">Report Management</span>
                                <span class="px-4 py-2 bg-[#7EBB1A]/20 text-[#7EBB1A] text-sm rounded-full border border-[#7EBB1A]/30">Data Analysis</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 2 - Maintenance Insight -->
                <div class="swiper-slide flex items-center justify-center text-center px-4">
                    <div class="feature-card w-full max-w-lg space-y-6 bg-white/5 backdrop-blur-sm rounded-3xl p-8 border border-white/10 hover:border-[#7EBB1A]/30 transition-all duration-500 mx-auto">
                        <div class="relative group mx-auto">
                            <div class="absolute inset-0 bg-[#7EBB1A]/20 rounded-2xl blur-xl animate-pulse group-hover:bg-[#7EBB1A]/30 transition-all duration-500"></div>
                            <div class="relative h-48 lg:h-56 flex items-center justify-center mb-6 overflow-hidden rounded-2xl bg-white/5">
                                <img
                                    src="{{ asset('images/hangar1.png') }}"
                                    alt="Data Analytics"
                                    class="max-w-full max-h-full object-contain rounded-xl shadow-xl transform group-hover:scale-105 transition-transform duration-500"
                                >
                            </div>
                        </div>
                        <div class="space-y-5 text-center">
                            <div class="flex items-center justify-center space-x-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-[#7EBB1A] to-[#8DC63F] rounded-xl flex items-center justify-center shadow-lg flex-shrink-0">
                                    <i class="fas fa-lightbulb text-white text-xl"></i>
                                </div>
                                <h2 class="text-xl lg:text-2xl font-bold text-[#7EBB1A] leading-tight">
                                    Maintenance Insight
                                </h2>
                            </div>
                            <p class="text-base text-gray-300 leading-relaxed max-w-md mx-auto">
                                Presenting statistical data and operational trends to assess aircraft maintenance effectiveness and support accurate technical decision-making.
                            </p>
                            <div class="flex flex-wrap justify-center gap-2 mt-5">
                                <span class="px-4 py-2 bg-[#7EBB1A]/20 text-[#7EBB1A] text-sm rounded-full border border-[#7EBB1A]/30">Statistics</span>
                                <span class="px-4 py-2 bg-[#7EBB1A]/20 text-[#7EBB1A] text-sm rounded-full border border-[#7EBB1A]/30">Trends</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 3 - Progress & Corrective Actions -->
                <div class="swiper-slide flex items-center justify-center text-center px-4">
                    <div class="feature-card w-full max-w-lg space-y-6 bg-white/5 backdrop-blur-sm rounded-3xl p-8 border border-white/10 hover:border-[#7EBB1A]/30 transition-all duration-500 mx-auto">
                        <div class="relative group mx-auto">
                            <div class="absolute inset-0 bg-[#7EBB1A]/20 rounded-2xl blur-xl animate-pulse group-hover:bg-[#7EBB1A]/30 transition-all duration-500"></div>
                            <div class="relative h-48 lg:h-56 flex items-center justify-center mb-6 overflow-hidden rounded-2xl bg-white/5">
                                <img
                                    src="{{ asset('images/hangar2.png') }}"
                                    alt="Collaboration"
                                    class="max-w-full max-h-full object-contain rounded-xl shadow-xl transform group-hover:scale-105 transition-transform duration-500"
                                >
                            </div>
                        </div>
                        <div class="space-y-5 text-center">
                            <div class="flex items-center justify-center space-x-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-[#7EBB1A] to-[#8DC63F] rounded-xl flex items-center justify-center shadow-lg flex-shrink-0">
                                    <i class="fas fa-tasks text-white text-xl"></i>
                                </div>
                                <h2 class="text-xl lg:text-2xl font-bold text-[#7EBB1A] leading-tight">
                                    Progress & Corrective Actions
                                </h2>
                            </div>
                            <p class="text-base text-gray-300 leading-relaxed max-w-md mx-auto">
                                Structured monitoring of investigation progress and implementation of corrective actions until all findings are effectively resolved.
                            </p>
                            <div class="flex flex-wrap justify-center gap-2 mt-5">
                                <span class="px-4 py-2 bg-[#7EBB1A]/20 text-[#7EBB1A] text-sm rounded-full border border-[#7EBB1A]/30">Monitoring</span>
                                <span class="px-4 py-2 bg-[#7EBB1A]/20 text-[#7EBB1A] text-sm rounded-full border border-[#7EBB1A]/30">Investigation</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 4 - Trends & Risk Analysis -->
                <div class="swiper-slide flex items-center justify-center text-center px-4">
                    <div class="feature-card w-full max-w-lg space-y-6 bg-white/5 backdrop-blur-sm rounded-3xl p-8 border border-white/10 hover:border-[#7EBB1A]/30 transition-all duration-500 mx-auto">
                        <div class="relative group mx-auto">
                            <div class="absolute inset-0 bg-[#7EBB1A]/20 rounded-2xl blur-xl animate-pulse group-hover:bg-[#7EBB1A]/30 transition-all duration-500"></div>
                            <div class="relative h-48 lg:h-56 flex items-center justify-center mb-6 overflow-hidden rounded-2xl bg-white/5">
                                <img
                                    src="{{ asset('images/hangar3.png') }}"
                                    alt="Risk Analysis"
                                    class="max-w-full max-h-full object-contain rounded-xl shadow-xl transform group-hover:scale-105 transition-transform duration-500"
                                >
                            </div>
                        </div>
                        <div class="space-y-5 text-center">
                            <div class="flex items-center justify-center space-x-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-[#7EBB1A] to-[#8DC63F] rounded-xl flex items-center justify-center shadow-lg flex-shrink-0">
                                    <i class="fas fa-chart-line text-white text-xl"></i>
                                </div>
                                <h2 class="text-xl lg:text-2xl font-bold text-[#7EBB1A] leading-tight">
                                    Trends & Risk Analysis
                                </h2>
                            </div>
                            <p class="text-base text-gray-300 leading-relaxed max-w-md mx-auto">
                                Early detection of failure patterns and potential risks through technical trend analysis and warning thresholds based on MTBF data and alert levels.
                            </p>
                            <div class="flex flex-wrap justify-center gap-2 mt-5">
                                <span class="px-4 py-2 bg-[#7EBB1A]/20 text-[#7EBB1A] text-sm rounded-full border border-[#7EBB1A]/30">Risk Assessment</span>
                                <span class="px-4 py-2 bg-[#7EBB1A]/20 text-[#7EBB1A] text-sm rounded-full border border-[#7EBB1A]/30">MTBF Analysis</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Swiper Navigation Arrows -->
            <div class="swiper-button-next !text-[#7EBB1A] !w-11 !h-11 !bg-white/10 !backdrop-blur-sm !rounded-full !border !border-white/20 hover:!bg-[#7EBB1A]/20 !transition-all !duration-300 after:!text-lg after:!font-bold !top-1/2 !right-2"></div>
            <div class="swiper-button-prev !text-[#7EBB1A] !w-11 !h-11 !bg-white/10 !backdrop-blur-sm !rounded-full !border !border-white/20 hover:!bg-[#7EBB1A]/20 !transition-all !duration-300 after:!text-lg after:!font-bold !top-1/2 !left-2"></div>
        </div>

        <!-- Custom Pagination -->
        <div class="flex flex-col items-center space-y-4 mt-8">
            <div class="w-28 h-1.5 bg-white/20 rounded-full overflow-hidden">
                <div class="swiper-progress h-full bg-gradient-to-r from-[#7EBB1A] to-[#8DC63F] rounded-full transition-all duration-500 w-1/4"></div>
            </div>
            <div class="text-sm text-gray-400 font-medium">
                <span class="current-slide">1</span> / <span class="total-slides">4</span>
            </div>
        </div>
    </div>

    <!-- ==============================================
         JAVASCRIPT
    ============================================== -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ==============================================
            // SMOOTH TRANSITION SYSTEM
            // ==============================================
            document.body.classList.add('transition-state');

            window.smoothTransition = function(targetState) {
                return new Promise((resolve) => {
                    setTimeout(() => {
                        const currentElement = document.querySelector(targetState ? '[x-show="!showLogin"]' : '[x-show="showLogin"]');
                        const targetElement = document.querySelector(targetState ? '[x-show="showLogin"]' : '[x-show="!showLogin"]');

                        if (currentElement) currentElement.style.willChange = 'transform, opacity';
                        if (targetElement) targetElement.style.willChange = 'transform, opacity';

                        setTimeout(() => {
                            const alpineData = document.querySelector('[x-data]').__x.$data;
                            if (alpineData) alpineData.showLogin = targetState;

                            setTimeout(() => {
                                if (currentElement) currentElement.style.willChange = 'auto';
                                if (targetElement) targetElement.style.willChange = 'auto';
                                resolve();
                            }, 900);
                        }, 50);
                    }, 100);
                });
            };

            // ==============================================
            // PAGE ANIMATIONS
            // ==============================================
            setTimeout(() => {
                document.querySelectorAll('.page-element').forEach((el, index) => {
                    setTimeout(() => {
                        el.classList.add('animate-in');
                        el.style.transform = 'translateY(0) scale(1)';
                        el.style.opacity = '1';
                    }, index * 150);
                });
            }, 200);

            // ==============================================
            // FORM INTERACTIONS
            // ==============================================
            const inputs = document.querySelectorAll('.input-field');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transition = 'all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
                    this.parentElement.style.transform = 'scale(1.02) translateY(-2px)';
                    this.style.transform = 'translateY(-2px)';
                });

                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'scale(1) translateY(0)';
                    this.style.transform = 'translateY(0)';
                });

                input.addEventListener('input', function() {
                    this.style.transition = 'transform 0.2s ease';
                    this.style.transform = 'translateY(-1px) scale(1.002)';
                    setTimeout(() => {
                        this.style.transform = 'translateY(-2px) scale(1)';
                    }, 150);
                });
            });

            // ==============================================
            // BUTTON INTERACTIONS
            // ==============================================
            const buttons = document.querySelectorAll('.btn-primary');
            buttons.forEach(button => {
                button.addEventListener('mouseenter', function() {
                    this.style.transition = 'all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
                    this.style.transform = 'translateY(-3px) scale(1.02)';
                    this.style.boxShadow = '0 25px 50px rgba(126, 187, 26, 0.3)';
                });

                button.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                    this.style.boxShadow = '';
                });

                button.addEventListener('mousedown', function() {
                    this.style.transform = 'translateY(-1px) scale(0.98)';
                });

                button.addEventListener('mouseup', function() {
                    this.style.transform = 'translateY(-3px) scale(1.02)';
                });
            });

            // ==============================================
            // SWIPER INITIALIZATION
            // ==============================================
            if (typeof Swiper !== 'undefined') {
                const swiper = new Swiper(".mySwiper", {
                    spaceBetween: 30,
                    centeredSlides: true,
                    autoplay: {
                        delay: 8000,
                        disableOnInteraction: false,
                        pauseOnMouseEnter: true,
                    },
                    effect: "fade",
                    speed: 1200,
                    fadeEffect: {
                        crossFade: true
                    },
                    loop: true,
                    grabCursor: true,
                    navigation: {
                        nextEl: ".swiper-button-next",
                        prevEl: ".swiper-button-prev",
                    },
                    on: {
                        slideChange: function() {
                            const realIndex = this.realIndex;
                            const progressBar = document.querySelector('.swiper-progress');
                            if (progressBar) {
                                progressBar.style.width = `${((realIndex + 1) / 4) * 100}%`;
                            }

                            const currentSlideEl = document.querySelector('.current-slide');
                            if (currentSlideEl) {
                                currentSlideEl.textContent = realIndex + 1;
                            }
                        },
                        transitionStart: function() {
                            const activeSlide = this.slides[this.activeIndex];
                            const card = activeSlide.querySelector('.feature-card');
                            if (card) {
                                card.style.transform = 'scale(0.95) translateY(10px)';
                                card.style.opacity = '0.8';
                                setTimeout(() => {
                                    card.style.transform = 'scale(1) translateY(0)';
                                    card.style.opacity = '1';
                                }, 300);
                            }
                        }
                    }
                });

                // Keyboard navigation
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'ArrowLeft') swiper.slidePrev();
                    else if (e.key === 'ArrowRight') swiper.slideNext();
                });
            }

            // ==============================================
            // LOGIN FORM HANDLING
            // ==============================================
            const loginForm = document.getElementById('login-form');
            const pageLoader = document.getElementById('page-loader');
            const loginBtn = document.getElementById('login-btn');

            if (loginForm && pageLoader && loginBtn) {
                loginForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    pageLoader.classList.remove('hidden');
                    pageLoader.classList.add('flex');
                    loginBtn.disabled = true;

                    const originalContent = loginBtn.innerHTML;
                    loginBtn.innerHTML = `
                        <span class="relative z-10 flex items-center space-x-3">
                            <div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                            <span>SIGNING IN...</span>
                        </span>
                    `;

                    setTimeout(() => {
                        loginForm.submit();
                    }, 1000);
                });
            }

            // ==============================================
            // PERFORMANCE OPTIMIZATIONS
            // ==============================================
            function debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            }

            let ticking = false;
            function handleScroll(e) {
                if (!ticking) {
                    requestAnimationFrame(() => {
                        e.preventDefault();
                        ticking = false;
                    });
                    ticking = true;
                }
            }

            document.addEventListener('wheel', handleScroll, { passive: false });
            document.addEventListener('touchmove', handleScroll, { passive: false });

            document.addEventListener('keydown', function(e) {
                if(['ArrowUp', 'ArrowDown', 'Space', 'PageUp', 'PageDown', 'Home', 'End'].includes(e.code)) {
                    if (!e.target.matches('input, textarea')) {
                        e.preventDefault();
                    }
                }
            });
        });
    </script>
</body>
</html>
