<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register | GMF Reliability</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- External Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* ==============================================
           GLOBAL STYLES & FONTS
        ============================================== */
        html, body {
            height: 100vh;
            overflow: hidden;
        }

        * {
            transition: transform 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94),
                        opacity 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94),
                        box-shadow 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        body {
            font-family: 'Inter', 'Segoe UI', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        /* ==============================================
           LOADER ANIMATIONS - Fixed to match edit.blade.php
        ============================================== */
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

        /* ==============================================
           ENHANCED ANIMATIONS
        ============================================== */
        @keyframes float-enhanced {
            0%, 100% {
                transform: translateY(0) rotate(0deg) scale(1);
                filter: blur(30px) brightness(1);
            }
            25% {
                transform: translateY(-30px) rotate(8deg) scale(1.08);
                filter: blur(35px) brightness(1.1);
            }
            50% {
                transform: translateY(0) rotate(0deg) scale(1);
                filter: blur(30px) brightness(1);
            }
            75% {
                transform: translateY(30px) rotate(-8deg) scale(0.92);
                filter: blur(25px) brightness(0.9);
            }
        }

        @keyframes bar-bounce-smooth {
            0%, 100% {
                transform: scaleY(0.3) scaleX(1);
                opacity: 0.4;
                filter: brightness(0.8);
            }
            50% {
                transform: scaleY(1.4) scaleX(1.1);
                opacity: 1;
                filter: brightness(1.2);
            }
        }

        @keyframes slide-in-up-smooth {
            0% {
                transform: translateY(60px) scale(0.9) rotateX(15deg);
                opacity: 0;
                filter: blur(8px);
            }
            100% {
                transform: translateY(0) scale(1) rotateX(0deg);
                opacity: 1;
                filter: blur(0px);
            }
        }

        @keyframes slide-in-left-smooth {
            0% {
                transform: translateX(-120%) scale(0.9) rotateY(-15deg);
                opacity: 0;
                filter: blur(8px);
            }
            100% {
                transform: translateX(0) scale(1) rotateY(0deg);
                opacity: 1;
                filter: blur(0px);
            }
        }

        @keyframes slide-in-right-smooth {
            0% {
                transform: translateX(120%) scale(0.9) rotateY(15deg);
                opacity: 0;
                filter: blur(8px);
            }
            100% {
                transform: translateX(0) scale(1) rotateY(0deg);
                opacity: 1;
                filter: blur(0px);
            }
        }

        @keyframes pulse-glow-enhanced {
            0%, 100% {
                box-shadow: 0 0 30px rgba(126, 187, 26, 0.4),
                           0 0 60px rgba(126, 187, 26, 0.2),
                           inset 0 0 20px rgba(126, 187, 26, 0.1);
            }
            50% {
                box-shadow: 0 0 50px rgba(126, 187, 26, 0.7),
                           0 0 100px rgba(126, 187, 26, 0.4),
                           inset 0 0 30px rgba(126, 187, 26, 0.2);
            }
        }

        @keyframes fade-in-smooth {
            0% {
                opacity: 0;
                transform: scale(0.85) translateY(20px);
                filter: blur(4px);
            }
            100% {
                opacity: 1;
                transform: scale(1) translateY(0);
                filter: blur(0px);
            }
        }

        @keyframes input-focus-glow {
            0% {
                box-shadow: 0 0 0 0 rgba(126, 187, 26, 0);
                transform: translateY(0) scale(1);
            }
            100% {
                box-shadow: 0 0 0 8px rgba(126, 187, 26, 0.15),
                           0 10px 30px rgba(0, 0, 0, 0.15);
                transform: translateY(-3px) scale(1.02);
            }
        }

        @keyframes shimmer-effect {
            0% {
                background-position: -200% center;
                opacity: 0;
            }
            50% {
                opacity: 1;
            }
            100% {
                background-position: 200% center;
                opacity: 0;
            }
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

        .glass-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 25px 45px rgba(0, 0, 0, 0.1);
        }

        /* ==============================================
           BACKGROUND ELEMENTS
        ============================================== */
        .bg-circle {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(45deg,
                rgba(34, 197, 94, 0.4),
                rgba(126, 187, 26, 0.3),
                rgba(15, 8, 38, 0.2));
            animation: float-enhanced 25s infinite ease-in-out;
        }

        .bg-circle:nth-child(1) {
            width: 600px; height: 600px;
            top: -150px; right: -150px;
            animation-delay: 0s;
        }

        .bg-circle:nth-child(2) {
            width: 400px; height: 400px;
            bottom: -100px; left: 8%;
            animation-delay: -8s;
        }

        .bg-circle:nth-child(3) {
            width: 250px; height: 250px;
            bottom: 25%; right: 15%;
            animation-delay: -16s;
        }

        .bg-circle:nth-child(4) {
            width: 180px; height: 180px;
            top: 15%; left: 3%;
            animation-delay: -24s;
        }

        /* ==============================================
           ANIMATION CLASSES
        ============================================== */
        .animate-loader-bar {
            animation: bar-bounce-smooth 1.2s infinite ease-in-out;
        }

        .animate-slide-in-up {
            animation: slide-in-up-smooth 1s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        }

        .animate-slide-in-left {
            animation: slide-in-left-smooth 1s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        }

        .animate-slide-in-right {
            animation: slide-in-right-smooth 1s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        }

        .animate-pulse-glow {
            animation: pulse-glow-enhanced 3s infinite;
        }

        .animate-fade-in {
            animation: fade-in-smooth 0.8s ease-out forwards;
        }

        /* ==============================================
           PAGE LOAD ANIMATIONS
        ============================================== */
        .page-element {
            opacity: 0;
            transform: translateY(60px) scale(0.9);
            filter: blur(8px);
        }

        .page-element.animate-in {
            animation: slide-in-up-smooth 1s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        }

        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.3s; }
        .delay-300 { animation-delay: 0.5s; }
        .delay-400 { animation-delay: 0.7s; }
        .delay-500 { animation-delay: 0.9s; }
        .delay-600 { animation-delay: 1.1s; }
        .delay-700 { animation-delay: 1.3s; }
        .delay-800 { animation-delay: 1.5s; }

        /* ==============================================
           ENHANCED INPUT EFFECTS
        ============================================== */
        .input-enhanced {
            transition: all 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative;
            overflow: hidden;
        }

        .input-enhanced::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg,
                transparent,
                rgba(126, 187, 26, 0.1),
                transparent);
            transition: left 0.8s ease;
        }

        .input-enhanced:focus::before {
            left: 100%;
        }

        .input-enhanced:focus {
            animation: input-focus-glow 0.6s ease-out forwards;
            background: rgba(255, 255, 255, 1);
            border-color: #7EBB1A;
        }

        .input-enhanced:hover {
            transform: translateY(-2px) scale(1.01);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }

        /* ==============================================
           BUTTON ENHANCEMENTS
        ============================================== */
        .btn-enhanced {
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #7EBB1A 0%, #8DC63F 50%, #7EBB1A 100%);
            background-size: 200% 100%;
            transition: all 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .btn-enhanced::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg,
                transparent,
                rgba(255,255,255,0.4),
                transparent);
            transition: left 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-enhanced:hover::before {
            left: 100%;
        }

        .btn-enhanced:hover {
            transform: translateY(-4px) scale(1.03);
            background-position: 100% 0;
            box-shadow: 0 20px 40px rgba(126, 187, 26, 0.4),
                       0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .btn-enhanced:active {
            transform: translateY(-2px) scale(1.01);
        }

        /* ==============================================
           GLASS MORPHISM EFFECTS
        ============================================== */
        .glass-morphism {
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.25);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        }

        .glass-morphism:hover {
            background: rgba(255, 255, 255, 0.18);
            border-color: rgba(126, 187, 26, 0.3);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.2);
        }

        /* ==============================================
           LOADER ENHANCEMENTS
        ============================================== */
        .loader-backdrop {
            background: rgba(17, 41, 85, 0.95);
            backdrop-filter: blur(15px) saturate(120%);
        }

        .loader-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        /* ==============================================
           RESPONSIVE ENHANCEMENTS
        ============================================== */
        @media (max-width: 768px) {
            .bg-circle {
                filter: blur(20px);
            }

            .page-element {
                transform: translateY(40px) scale(0.95);
            }
        }

        @media (max-width: 640px) {
            .animate-slide-in-up {
                animation-duration: 0.8s;
            }
        }

        /* ==============================================
           ADVANCED HOVER EFFECTS
        ============================================== */
        .hover-lift {
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .hover-lift:hover {
            transform: translateY(-3px) scale(1.02);
            filter: brightness(1.1);
        }

        .shimmer {
            background: linear-gradient(
                90deg,
                transparent,
                rgba(255, 255, 255, 0.2),
                transparent
            );
            background-size: 200% 100%;
            animation: shimmer-effect 2s infinite;
        }
    </style>
</head>

<body class="bg-[#112955] text-white font-sans h-screen overflow-hidden relative" x-data="{
    showSuccess: false,
    isLoading: false,
    formData: {
        name: '',
        email: '',
        position: '',
        password: '',
        password_confirmation: ''
    }
}">
    <!-- ==============================================
         ENHANCED ANIMATED BACKGROUND
    ============================================== -->
    <div class="absolute inset-0 overflow-hidden z-0">
        <div class="bg-circle"></div>
        <div class="bg-circle"></div>
        <div class="bg-circle"></div>
        <div class="bg-circle"></div>
    </div>

    <!-- ==============================================
     ENHANCED REGISTRATION LOADER - Fixed to match edit.blade.php
    ============================================== -->
    <div id="page-loader" class="fixed inset-0 z-50 hidden flex-col items-center justify-center bg-[#112955]/90 backdrop-blur-lg transition-all duration-500">
        <div class="glass-card rounded-3xl shadow-2xl p-12 border border-white/20 max-w-sm w-full mx-4 bg-white/10 backdrop-blur-xl">

            <div class="text-center space-y-4">
                <span id="loader-text" class="text-xl font-semibold text-white block">Creating your account...</span>
                <p class="text-sm text-gray-300">Please wait while we set up your profile</p>
            </div>

            <div class="mt-6 w-full h-2 bg-white/20 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-[#7EBB1A] to-[#8DC63F] rounded-full progress-bar"></div>
            </div>
        </div>
    </div>

    <!-- ==============================================
         MAIN CONTAINER
    ============================================== -->
    <div class="flex flex-col lg:flex-row items-center justify-between h-screen relative z-10 container mx-auto px-4 lg:px-12">

        <!-- ==============================================
             LEFT CONTENT SECTION
        ============================================== -->
        <div class="w-full lg:w-1/2 flex flex-col justify-center h-full p-6 lg:p-12">
            <!-- Logo -->
            <div class="mb-10 flex justify-center lg:justify-start page-element delay-100">
                <img
                    src="{{ asset('images/gmfwhite.png') }}"
                    alt="GMF Logo"
                    class="h-20 lg:h-24 w-auto hover-lift drop-shadow-2xl"
                >
            </div>

            <!-- Welcome Content -->
            <div class="space-y-8 max-w-lg page-element delay-200">
                <div class="space-y-6">
                    <!-- Badge -->
                    <div class="inline-flex items-center space-x-2 bg-[#7EBB1A]/20 backdrop-blur-sm border border-[#7EBB1A]/30 rounded-full px-4 py-2 hover-lift">
                        <div class="w-2 h-2 bg-[#7EBB1A] rounded-full animate-pulse"></div>
                        <span class="text-sm font-medium text-[#7EBB1A]">Join GMF Reliability</span>
                    </div>

                    <!-- Hero Title -->
                    <div class="space-y-4">
                        <h4 class="text-sm uppercase tracking-wider text-gray-300 font-semibold">Welcome to</h4>
                        <h1 class="text-4xl lg:text-5xl xl:text-6xl font-light leading-tight bg-gradient-to-r from-white via-gray-100 to-gray-200 bg-clip-text text-transparent">
                            Reliability<br>
                            <span class="text-[#7EBB1A] font-bold">Report System</span>
                        </h1>
                    </div>

                    <!-- Description -->
                    <p class="text-gray-300 max-w-md text-lg leading-relaxed">
                        Create your account to access the aircraft reliability reporting system designed to support structured technical and operational analysis.
                    </p>
                </div>

                <!-- Features List -->
                <div class="space-y-4 text-gray-400 text-sm">
                    <div class="flex items-center space-x-3 hover-lift">
                        <div class="w-2 h-2 bg-[#7EBB1A] rounded-full animate-pulse"></div>
                        <span>Access analytical dashboard</span>
                    </div>
                    <div class="flex items-center space-x-3 hover-lift">
                        <div class="w-2 h-2 bg-[#7EBB1A] rounded-full animate-pulse" style="animation-delay: 0.5s;"></div>
                        <span>Structured reliability reports</span>
                    </div>
                    <div class="flex items-center space-x-3 hover-lift">
                        <div class="w-2 h-2 bg-[#7EBB1A] rounded-full animate-pulse" style="animation-delay: 1s;"></div>
                        <span>Aircraft reliability monitoring</span>
                    </div>
                </div>

                <!-- Copyright -->
                <p class="text-xs text-gray-500 mt-10 font-medium">
                    © {{ date('Y') }} PT Garuda Maintenance Facility AeroAsia Tbk.
                </p>
            </div>
        </div>

        <!-- ==============================================
             RIGHT CONTENT - REGISTRATION FORM
        ============================================== -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-6 lg:p-12">
            <div class="w-full max-w-md">
                <!-- Registration Form Container -->
                <div class="glass-morphism rounded-3xl p-8 lg:p-10 shadow-2xl page-element delay-300">
                    <!-- Form Header -->
                    <div class="text-center mb-8 page-element delay-400">
                        <div class="inline-flex items-center space-x-2 bg-[#7EBB1A]/20 backdrop-blur-sm border border-[#7EBB1A]/30 rounded-full px-4 py-2 mb-4">
                            <i class="fas fa-user-plus text-[#7EBB1A]"></i>
                            <span class="text-sm font-medium text-[#7EBB1A]">REGISTER</span>
                        </div>
                        <h2 class="text-3xl lg:text-4xl font-light leading-tight mb-2 bg-gradient-to-r from-white to-gray-200 bg-clip-text text-transparent">
                            Create Account
                        </h2>
                        <p class="text-gray-300 text-sm">Fill in the information below to get started</p>
                    </div>

                    <!-- Registration Form -->
                    <form id="register-form" method="POST" action="{{ route('register') }}" class="space-y-6">
                        @csrf

                        <!-- Name Field -->
                        <div class="space-y-2 page-element delay-500">
                            <label for="name" class="block text-sm text-white font-medium">Full Name</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-user text-gray-500"></i>
                                </div>
                                <input
                                    id="name"
                                    class="input-enhanced w-full pl-10 pr-4 py-3 lg:py-4 rounded-xl border border-gray-600/50 bg-white/95 text-gray-900 placeholder-gray-500 focus:outline-none shadow-lg"
                                    type="text"
                                    name="name"
                                    value="{{ old('name') }}"
                                    placeholder="Enter your full name"
                                    required
                                    autofocus
                                    autocomplete="name"
                                />
                            </div>
                            @error('name')
                                <p class="text-red-400 text-xs mt-1 font-medium flex items-center space-x-1 animate-fade-in">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>

                        <!-- Email Field -->
                        <div class="space-y-2 page-element delay-600">
                            <label for="email" class="block text-sm text-white font-medium">Email Address</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-envelope text-gray-500"></i>
                                </div>
                                <input
                                    id="email"
                                    class="input-enhanced w-full pl-10 pr-4 py-3 lg:py-4 rounded-xl border border-gray-600/50 bg-white/95 text-gray-900 placeholder-gray-500 focus:outline-none shadow-lg"
                                    type="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    placeholder="Enter your email address"
                                    required
                                    autocomplete="username"
                                />
                            </div>
                            @error('email')
                                <p class="text-red-400 text-xs mt-1 font-medium flex items-center space-x-1 animate-fade-in">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>

                        <!-- Position Field -->
                        <div class="space-y-2 page-element delay-700">
                            <label for="Position" class="block text-sm text-white font-medium">Position</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-briefcase text-gray-500"></i>
                                </div>
                                <input
                                    id="Position"
                                    class="input-enhanced w-full pl-10 pr-4 py-3 lg:py-4 rounded-xl border border-gray-600/50 bg-white/95 text-gray-900 placeholder-gray-500 focus:outline-none shadow-lg"
                                    type="text"
                                    name="Position"
                                    value="{{ old('Position') }}"
                                    placeholder="Enter your position"
                                    required
                                />
                            </div>
                            @error('Position')
                                <p class="text-red-400 text-xs mt-1 font-medium flex items-center space-x-1 animate-fade-in">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>

                        <!-- Password Field -->
                        <div class="space-y-2 page-element delay-800">
                            <label for="password" class="block text-sm text-white font-medium">Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-500"></i>
                                </div>
                                <input
                                    id="password"
                                    class="input-enhanced w-full pl-10 pr-4 py-3 lg:py-4 rounded-xl border border-gray-600/50 bg-white/95 text-gray-900 placeholder-gray-500 focus:outline-none shadow-lg"
                                    type="password"
                                    name="password"
                                    placeholder="Create a strong password"
                                    required
                                    autocomplete="new-password"
                                />
                            </div>
                            @error('password')
                                <p class="text-red-400 text-xs mt-1 font-medium flex items-center space-x-1 animate-fade-in">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>

                        <!-- Confirm Password Field -->
                        <div class="space-y-2 page-element delay-800">
                            <label for="password_confirmation" class="block text-sm text-white font-medium">Confirm Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-shield-alt text-gray-500"></i>
                                </div>
                                <input
                                    id="password_confirmation"
                                    class="input-enhanced w-full pl-10 pr-4 py-3 lg:py-4 rounded-xl border border-gray-600/50 bg-white/95 text-gray-900 placeholder-gray-500 focus:outline-none shadow-lg"
                                    type="password"
                                    name="password_confirmation"
                                    placeholder="Confirm your password"
                                    required
                                    autocomplete="new-password"
                                />
                            </div>
                            @error('password_confirmation')
                                <p class="text-red-400 text-xs mt-1 font-medium flex items-center space-x-1 animate-fade-in">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>

                        <!-- Register Button -->
                        <div class="page-element delay-800">
                            <button
                                type="submit"
                                class="btn-enhanced group w-full text-white font-semibold px-6 py-4 lg:py-5 rounded-xl shadow-lg transition-all duration-600 relative overflow-hidden min-h-[56px] flex items-center justify-center animate-pulse-glow"
                                id="register-btn"
                            >
                                <span class="relative z-10 flex items-center space-x-3">
                                    <i class="fas fa-user-plus transition-transform duration-300 group-hover:rotate-12"></i>
                                    <span>CREATE ACCOUNT</span>
                                    <svg class="w-5 h-5 group-hover:translate-x-2 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                    </svg>
                                </span>
                            </button>
                        </div>

                        <!-- Divider -->
                        <div class="relative my-5 lg:my-6">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-600"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-2 text-gray-400">-</span>
                            </div>
                        </div>

                        <!-- Login Link -->
                        <div class="text-center page-element delay-800">
                            <p class="text-gray-300 text-sm">
                                Already have an account?
                                <a
                                    href="/"
                                    class="text-[#7EBB1A] hover:text-[#8DC63F] font-semibold hover:underline transition-all duration-300 ml-1"
                                >
                                    Sign in here
                                </a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ==============================================
         ENHANCED JAVASCRIPT
    ============================================== -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ==============================================
            // SMOOTH PAGE LOAD ANIMATIONS
            // ==============================================
            setTimeout(() => {
                document.querySelectorAll('.page-element').forEach((el, index) => {
                    setTimeout(() => {
                        el.classList.add('animate-in');
                        el.style.transform = 'translateY(0) scale(1)';
                        el.style.opacity = '1';
                        el.style.filter = 'blur(0px)';
                    }, index * 150);
                });
            }, 200);

            // ==============================================
            // ENHANCED INPUT INTERACTIONS
            // ==============================================
            const inputs = document.querySelectorAll('.input-enhanced');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.willChange = 'transform, opacity';
                    this.parentElement.style.transition = 'all 0.6s cubic-bezier(0.34, 1.56, 0.64, 1)';
                    this.parentElement.style.transform = 'scale(1.02) translateY(-3px)';
                });

                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'scale(1) translateY(0)';
                    setTimeout(() => {
                        this.parentElement.style.willChange = 'auto';
                    }, 600);
                });

                input.addEventListener('input', function() {
                    this.style.transition = 'transform 0.3s ease';
                    this.style.transform = 'translateY(-1px) scale(1.001)';
                    setTimeout(() => {
                        this.style.transform = 'translateY(0) scale(1)';
                    }, 200);
                });
            });

            // ==============================================
            // ENHANCED FORM SUBMISSION
            // ==============================================
            const registerForm = document.getElementById('register-form');
            const pageLoader = document.getElementById('page-loader');
            const registerBtn = document.getElementById('register-btn');

            if (registerForm && pageLoader && registerBtn) {
                registerForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    // Show enhanced loader
                    pageLoader.classList.remove('hidden');
                    pageLoader.classList.add('flex');
                    registerBtn.disabled = true;

                    // Enhanced button loading state
                    const originalContent = registerBtn.innerHTML;
                    registerBtn.innerHTML = `
                        <span class="relative z-10 flex items-center space-x-3">
                            <div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                            <span>CREATING ACCOUNT...</span>
                        </span>
                    `;

                    // Add loading shimmer effect
                    registerBtn.classList.add('shimmer');

                    // Submit with enhanced timing
                    setTimeout(() => {
                        registerForm.submit();
                    }, 1000);
                });
            }

            // ==============================================
            // ENHANCED HOVER EFFECTS
            // ==============================================
            const hoverElements = document.querySelectorAll('.hover-lift');
            hoverElements.forEach(element => {
                element.addEventListener('mouseenter', function() {
                    this.style.willChange = 'transform, filter';
                });

                element.addEventListener('mouseleave', function() {
                    setTimeout(() => {
                        this.style.willChange = 'auto';
                    }, 400);
                });
            });

            // ==============================================
            // BACKGROUND ANIMATION OPTIMIZATION
            // ==============================================
            const circles = document.querySelectorAll('.bg-circle');
            circles.forEach(circle => {
                circle.style.willChange = 'transform, filter';
            });

            // ==============================================
            // SUCCESS MESSAGE HANDLING
            // ==============================================
            @if(session('success'))
                setTimeout(() => {
                    const successMessage = document.createElement('div');
                    successMessage.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in';
                    successMessage.innerHTML = `
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-check-circle"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    `;
                    document.body.appendChild(successMessage);

                    setTimeout(() => {
                        successMessage.remove();
                    }, 5000);
                }, 1000);
            @endif

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

            // Optimize scroll and resize handlers
            const optimizedResize = debounce(() => {
                // Handle responsive adjustments
                const isMobile = window.innerWidth < 768;
                if (isMobile) {
                    circles.forEach(circle => {
                        circle.style.filter = 'blur(15px)';
                    });
                }
            }, 250);

            window.addEventListener('resize', optimizedResize);
        });
    </script>
</body>
</html>
