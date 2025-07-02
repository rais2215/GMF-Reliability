<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register | GMF Reliability</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- External Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs" defer></script>

    <style>
        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
        }

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

        @keyframes slide-in-up {
            0% { transform: translateY(40px) scale(0.95); opacity: 0; }
            100% { transform: translateY(0) scale(1); opacity: 1; }
        }

        @keyframes slide-in-left {
            0% { transform: translateX(-100%) scale(0.95); opacity: 0; }
            100% { transform: translateX(0) scale(1); opacity: 1; }
        }

        @keyframes slide-in-right {
            0% { transform: translateX(100%) scale(0.95); opacity: 0; }
            100% { transform: translateX(0) scale(1); opacity: 1; }
        }

        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(126, 187, 26, 0.3); }
            50% { box-shadow: 0 0 40px rgba(126, 187, 26, 0.6); }
        }

        @keyframes fade-in {
            0% { opacity: 0; transform: scale(0.9); }
            100% { opacity: 1; transform: scale(1); }
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

        .animate-slide-in-up {
            animation: slide-in-up 0.8s cubic-bezier(0.4, 2, 0.6, 1) forwards;
        }

        .animate-slide-in-left {
            animation: slide-in-left 0.8s cubic-bezier(0.4, 2, 0.6, 1) forwards;
        }

        .animate-slide-in-right {
            animation: slide-in-right 0.8s cubic-bezier(0.4, 2, 0.6, 1) forwards;
        }

        .animate-pulse-glow {
            animation: pulse-glow 2s infinite;
        }

        .animate-fade-in {
            animation: fade-in 0.6s ease-out forwards;
        }

        /* Page Load Animations */
        .page-element {
            opacity: 0;
            transform: translateY(40px) scale(0.95);
        }

        .page-element.animate-in {
            animation: slide-in-up 0.8s cubic-bezier(0.4, 2, 0.6, 1) forwards;
        }

        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        .delay-400 { animation-delay: 0.4s; }
        .delay-500 { animation-delay: 0.5s; }
        .delay-600 { animation-delay: 0.6s; }
        .delay-700 { animation-delay: 0.7s; }

        /* Input Focus Effects */
        .input-focus {
            transition: all 0.3s cubic-bezier(0.4, 2, 0.6, 1);
        }

        .input-focus:focus {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1), 0 0 0 3px rgba(126, 187, 26, 0.2);
        }

        .input-focus:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body class="bg-[#112955] text-white font-sans min-h-screen overflow-hidden relative" x-data="{ showSuccess: false }">
    <!-- Animated Background Circles -->
    <div class="absolute inset-0 overflow-hidden z-0">
        <div class="bg-circle"></div>
        <div class="bg-circle"></div>
        <div class="bg-circle"></div>
    </div>

    <!-- Registration Loader -->
    <div id="page-loader" class="fixed inset-0 z-50 hidden flex-col items-center justify-center bg-white bg-opacity-80 transition-all duration-500 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl p-8 border border-gray-200">
            <div class="flex space-x-3 mb-6 justify-center">
                <div class="w-4 h-16 bg-gradient-to-t from-blue-800 to-blue-500 rounded-full animate-loader-bar"></div>
                <div class="w-4 h-16 bg-gradient-to-t from-blue-700 to-blue-400 rounded-full animate-loader-bar delay-150"></div>
                <div class="w-4 h-16 bg-gradient-to-t from-green-600 to-green-400 rounded-full animate-loader-bar delay-300"></div>
            </div>
            <span id="loader-text" class="text-lg font-semibold text-gray-700 block text-center">Creating account...</span>
            <div class="mt-4 w-48 h-1 bg-gray-200 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-blue-600 to-green-500 rounded-full animate-pulse"></div>
            </div>
        </div>
    </div>

    <!-- Main Container -->
    <div class="flex flex-col lg:flex-row items-center justify-between min-h-screen relative z-10 container mx-auto px-4 lg:px-12 py-8">

        <!-- Left Content - Logo and Welcome Text -->
        <div class="w-full lg:w-1/2 flex flex-col justify-center min-h-[500px] p-6 lg:p-12">
            <!-- Logo -->
            <div class="mb-10 flex justify-center lg:justify-start page-element delay-100">
                <img
                    src="{{ asset('images/gmfwhite.png') }}"
                    alt="Logo GMF"
                    class="h-20 w-auto max-w-full hover:scale-110 transition-transform duration-300 drop-shadow-2xl"
                >
            </div>

            <!-- Welcome Content -->
            <div class="space-y-6 max-w-lg page-element delay-200">
                <div class="space-y-4">
                    <h4 class="text-sm uppercase tracking-wider text-gray-300 font-semibold">Bergabung dengan</h4>
                    <h1 class="text-4xl lg:text-5xl font-light leading-tight bg-gradient-to-r from-white to-gray-200 bg-clip-text text-transparent">
                        Reliability Report
                    </h1>
                    <p class="text-gray-300 max-w-md text-lg leading-relaxed">
                        Buat akun Anda untuk mengakses sistem pelaporan keandalan pesawat yang dirancang untuk mendukung analisis teknis dan operasional secara terstruktur.
                    </p>
                </div>

                <div class="space-y-4 text-gray-400 text-sm">
                    <div class="flex items-center space-x-3">
                        <div class="w-2 h-2 bg-[#7EBB1A] rounded-full animate-pulse"></div>
                        <span>Akses dashboard analitik</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="w-2 h-2 bg-[#7EBB1A] rounded-full animate-pulse" style="animation-delay: 0.5s;"></div>
                        <span>Laporan keandalan terstruktur</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="w-2 h-2 bg-[#7EBB1A] rounded-full animate-pulse" style="animation-delay: 1s;"></div>
                        <span>Pemantauan keandalan pesawat</span>
                    </div>
                </div>

                <p class="text-xs text-gray-400 mt-10 font-medium">
                    © {{ date('Y') }} PT Garuda Maintenance Facility AeroAsia Tbk
                </p>
            </div>
        </div>

        <!-- Right Content - Registration Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-6 lg:p-12">
            <div class="w-full max-w-md">
                <!-- Registration Form Container -->
                <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-8 shadow-2xl border border-white/20 page-element delay-300">
                    <div class="text-center mb-8">
                        <h2 class="text-3xl font-light leading-tight mb-2 bg-gradient-to-r from-white to-gray-200 bg-clip-text text-transparent">
                            Create Account
                        </h2>
                        <p class="text-gray-300 text-sm">Lengkapi informasi di bawah ini</p>
                    </div>

                    <form id="register-form" method="POST" action="{{ route('register') }}" class="space-y-5">
                        @csrf

                        <!-- Name Field -->
                        <div class="space-y-2 page-element delay-400">
                            <label for="name" class="block text-sm text-white font-medium">Full Name</label>
                            <input
                                id="name"
                                class="w-full px-4 py-3 rounded-xl border border-gray-600 bg-white/90 text-gray-900 placeholder-gray-500 focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-500/20 input-focus shadow-lg"
                                type="text"
                                name="name"
                                value="{{ old('name') }}"
                                placeholder="Enter your full name"
                                required
                                autofocus
                                autocomplete="name"
                            />
                            @error('name')
                                <p class="text-red-400 text-xs mt-1 font-medium animate-fade-in">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email Field -->
                        <div class="space-y-2 page-element delay-500">
                            <label for="email" class="block text-sm text-white font-medium">Email Address</label>
                            <input
                                id="email"
                                class="w-full px-4 py-3 rounded-xl border border-gray-600 bg-white/90 text-gray-900 placeholder-gray-500 focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-500/20 input-focus shadow-lg"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="Enter your email address"
                                required
                                autocomplete="username"
                            />
                            @error('email')
                                <p class="text-red-400 text-xs mt-1 font-medium animate-fade-in">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Position Field -->
                        <div class="space-y-2 page-element delay-600">
                            <label for="Position" class="block text-sm text-white font-medium">Position</label>
                            <input
                                id="Position"
                                class="w-full px-4 py-3 rounded-xl border border-gray-600 bg-white/90 text-gray-900 placeholder-gray-500 focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-500/20 input-focus shadow-lg"
                                type="text"
                                name="Position"
                                value="{{ old('Position') }}"
                                placeholder="Enter your position"
                                required
                            />
                            @error('Position')
                                <p class="text-red-400 text-xs mt-1 font-medium animate-fade-in">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password Field -->
                        <div class="space-y-2 page-element delay-700">
                            <label for="password" class="block text-sm text-white font-medium">Password</label>
                            <input
                                id="password"
                                class="w-full px-4 py-3 rounded-xl border border-gray-600 bg-white/90 text-gray-900 placeholder-gray-500 focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-500/20 input-focus shadow-lg"
                                type="password"
                                name="password"
                                placeholder="Create a strong password"
                                required
                                autocomplete="new-password"
                            />
                            @error('password')
                                <p class="text-red-400 text-xs mt-1 font-medium animate-fade-in">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirm Password Field -->
                        <div class="space-y-2 page-element delay-700">
                            <label for="password_confirmation" class="block text-sm text-white font-medium">Confirm Password</label>
                            <input
                                id="password_confirmation"
                                class="w-full px-4 py-3 rounded-xl border border-gray-600 bg-white/90 text-gray-900 placeholder-gray-500 focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-500/20 input-focus shadow-lg"
                                type="password"
                                name="password_confirmation"
                                placeholder="Confirm your password"
                                required
                                autocomplete="new-password"
                            />
                            @error('password_confirmation')
                                <p class="text-red-400 text-xs mt-1 font-medium animate-fade-in">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Register Button -->
                        <div class="page-element delay-700">
                            <button
                                type="submit"
                                class="group w-full bg-[#7EBB1A] hover:bg-[#8DC63F] text-white font-semibold px-6 py-4 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 transform relative overflow-hidden min-h-[48px] flex items-center justify-center animate-pulse-glow"
                                id="register-btn"
                            >
                                <span class="relative z-10 flex items-center space-x-2">
                                    <span>CREATE ACCOUNT</span>
                                </span>
                                <div class="absolute inset-0 bg-gradient-to-r from-[#8DC63F] to-[#7EBB1A] opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            </button>
                        </div>

                        <!-- Login Link -->
                        <div class="text-center page-element delay-700">
                            <p class="text-gray-300 text-sm">
                                Sudah punya akun?
                                <a
                                    href="/"
                                    class="text-[#7EBB1A] hover:text-[#8DC63F] font-semibold hover:underline transition-all duration-200 ml-1"
                                >
                                    Login di sini
                                </a>
                            </p>
                        </div>
                    </form>
                </div>
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

            // Registration form submission
            const registerForm = document.getElementById('register-form');
            const pageLoader = document.getElementById('page-loader');
            const registerBtn = document.getElementById('register-btn');

            if (registerForm && pageLoader && registerBtn) {
                registerForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    // Show loader
                    pageLoader.classList.remove('hidden');
                    pageLoader.classList.add('flex');
                    registerBtn.disabled = true;

                    // Add loading state to button
                    const originalText = registerBtn.innerHTML;
                    registerBtn.innerHTML = `
                        <span class="flex items-center space-x-2">
                            <div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                            <span>Creating...</span>
                        </span>
                    `;

                    // Submit form after delay
                    setTimeout(() => {
                        registerForm.submit();
                    }, 500);
                });
            }

            // Input field animations
            const inputs = document.querySelectorAll('input');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('animate-slide-in-up');
                });

                input.addEventListener('blur', function() {
                    this.parentElement.classList.remove('animate-slide-in-up');
                });
            });

            // Show success message if registration successful (you can trigger this from backend)
            @if(session('success'))
                setTimeout(() => {
                    alert('{{ session('success') }}');
                }, 1000);
            @endif
        });
    </script>
</body>
</html>
