<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register | GMF Reliability</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background-color: #0F0826;
            color: white;
            min-height: 100vh;
            position: relative;
            overflow: hidden;
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
            0%, 100% { transform: translateY(0) rotate(0deg) scale(1); }
            25% { transform: translateY(-20px) rotate(5deg) scale(1.05); }
            50% { transform: translateY(0) rotate(0deg) scale(1); }
            75% { transform: translateY(20px) rotate(-5deg) scale(0.95); }
        }

        /* Loader Animation Style */
        @keyframes bar-bounce {
            0%, 100% { transform: scaleY(0.5); opacity: 0.5; }
            50% { transform: scaleY(1.2); opacity: 1; }
        }
        .animate-loader-bar {
            animation: bar-bounce 1s infinite ease-in-out;
        }
        .delay-150 {
            animation-delay: 0.15s;
        }
        .delay-300 {
            animation-delay: 0.3s;
        }
    </style>
</head>

<body class="flex items-center justify-center px-4">
    <!-- Background -->
    <div class="animated-bg">
        <div class="bg-circle"></div>
        <div class="bg-circle"></div>
        <div class="bg-circle"></div>
    </div>

    <!-- Register Form -->
    <div class="w-full max-w-md bg-blur/10 backdrop-blur-lg rounded-xl p-8 z-10">
        <h2 class="text-3xl font-semibold text-center mb-6 text-green-400">Sign up your account</h2>

        <form id="register-form" method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <!-- Name -->
            <div>
                <x-input-label for="name" :value="('Name')" class="text-white" />
                <x-text-input id="name" class="block mt-1 w-full text-black" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email -->
            <div>
                <x-input-label for="email" :value="('Email')" class="text-white"/>
                <x-text-input id="email" class="block mt-1 w-full text-black" type="email" name="email" :value="old('email')" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Position -->
            <div>
                <x-input-label for="Position" :value="('Position')" class="text-white"/>
                <x-text-input id="Position" class="block mt-1 w-full text-black" type="text" name="Position" :value="old('Position')" required />
                <x-input-error :messages="$errors->get('Position')" class="mt-2" />
            </div>

            <!-- Password -->
            <div>
                <x-input-label for="password" :value="('Password')" class="text-white"/>
                <x-text-input id="password" class="block mt-1 w-full text-black" type="password" name="password" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div>
                <x-input-label for="password_confirmation" :value="('Confirm Password')" class="text-white"/>
                <x-text-input id="password_confirmation" class="block mt-1 w-full text-black" type="password" name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between mt-6">
                <a class="text-sm text-white hover:underline" href="/">
                     Sudah punya akun? Login
                </a>
                <x-primary-button id="register-btn" class="bg-green-500 hover:bg-green-600 text-white">
                    Create Account
                </x-primary-button>
            </div>
        </form>
    </div>

    <!-- Page Loader: 3 Bar Loader -->
    <div id="page-loader" class="fixed inset-0 z-50 flex-col items-center justify-center bg-white bg-opacity-80 hidden transition-opacity duration-300">
        <div class="flex space-x-2 mb-4">
            <div class="w-3 h-12 bg-blue-600 rounded animate-loader-bar"></div>
            <div class="w-3 h-12 bg-blue-600 rounded animate-loader-bar delay-150"></div>
            <div class="w-3 h-12 bg-blue-600 rounded animate-loader-bar delay-300"></div>
        </div>
        <span id="loader-text" class="text-sm font-medium text-gray-800">Creating account...</span>
    </div>

    <script>
        // Pastikan loader muncul saat tombol Register diklik
        document.getElementById('register-form').addEventListener('submit', function(e) {
            const loader = document.getElementById('page-loader');
            loader.classList.remove('hidden');
            loader.classList.add('flex');
        });
    </script>
</body>
</html>
