<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lupa Password | GMF Reliability</title>
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

        @keyframes fadeInUp {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in-up {
            opacity: 0;
            animation: fadeInUp 0.8s ease forwards;
        }

        .fade-in-up.delay-1 { animation-delay: 0.2s; }
        .fade-in-up.delay-2 { animation-delay: 0.4s; }
        .fade-in-up.delay-3 { animation-delay: 0.6s; }
        .fade-in-up.delay-4 { animation-delay: 0.8s; }

        .form-container {
            animation: fadeInUp 1s ease forwards;
            animation-delay: 0.3s;
            opacity: 0;
        }

        .animated-button {
            transition: all 0.3s ease;
            animation: pulseButton 2s infinite;
        }

        @keyframes pulseButton {
            0% { transform: scale(1); }
            50% { transform: scale(1.03); }
            100% { transform: scale(1); }
        }

        .animated-button:hover {
            transform: scale(1.05);
            background-color: #16a34a;
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

    <!-- Forgot Password Form -->
    <div class="w-full max-w-md bg-white/10 backdrop-blur-lg rounded-xl p-8 z-10 form-container">
        <h2 class="text-3xl font-semibold text-center mb-6 text-green-400 fade-in-up delay-1">Forgot your password?</h2>

        <p class="text-sm text-white mb-4 text-center fade-in-up delay-2">
            No problem. Just enter your email address and we’ll send you a password reset link.
        </p>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4 text-green-400 text-sm text-center fade-in-up delay-3" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf

            <!-- Email Address -->
            <div class="fade-in-up delay-3">
                <x-input-label for="email" :value="('Email')" class="text-white" />
                <x-text-input id="email" class="block mt-1 w-full text-black" type="email" name="email" :value="old('email')" required autofocus />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Submit -->
            <div class="flex items-center justify-end mt-4 fade-in-up delay-4">
                <x-primary-button class="bg-green-500 hover:bg-green-600 text-white animated-button">
                    Email Password Reset Link
                </x-primary-button>
            </div>
        </form>
    </div>
</body>
</html>
