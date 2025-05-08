<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>GMF | Reliability Report</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css">

    <style>
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
            0%, 100% { transform: translateY(0) rotate(0deg); }
            25% { transform: translateY(-20px) rotate(5deg); }
            50% { transform: translateY(0) rotate(0deg); }
            75% { transform: translateY(20px) rotate(-5deg); }
        }
    </style>
</head>
<body class="bg-[#0F0826] text-white font-sans min-h-screen overflow-hidden relative">

    <!-- Background -->
    <div class="animated-bg">
        <div class="bg-circle"></div>
        <div class="bg-circle"></div>
        <div class="bg-circle"></div>
    </div>

    <!-- Main Layout -->
    <div class="flex flex-col md:flex-row items-center justify-between h-screen relative z-10 px-4 md:px-16 lg:px-24">

        <!-- Left Text Content -->
        <div class="w-full md:w-1/2 lg:w-[45%] text-left space-y-6 md:space-y-8">
            <img src="{{ asset('images/gmfwhite.png') }}" alt="Logo GMF" class="h-24 mb-4">
            <h4 class="text-sm uppercase tracking-wider text-gray-400">Selamat Datang di</h4>
            <h1 class="text-5xl font-light leading-tight">Reliability Report</h1>
            <p class="text-gray-300 max-w-md md:max-w-lg leading-relaxed">
                Platform laporan keandalan pesawat yang membantu memantau performa operasional dan mendukung pengambilan keputusan berbasis data.
            </p>
            <a href="{{ route('login') }}" class="inline-block px-6 py-2 bg-green-500 text-white text-sm font-semibold rounded-lg shadow hover:bg-green-600 transition">
                MASUK
            </a>
            <p class="text-xs text-gray-500 mt-10">
                © {{ date('Y') }} PT Garuda Maintenance Facility Aero Asia Tbk
            </p>
        </div>

        <!-- Right Carousel -->
        <div class="w-full md:w-1/2 lg:w-[50%] flex items-center justify-center mt-10 md:mt-0">
            <div class="swiper w-[90%] h-[70%] rounded-2xl overflow-hidden shadow-lg">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <img src="{{ asset('images/hangar.png') }}" alt="Slide 1" class="object-cover w-full h-full" />
                    </div>
                    <div class="swiper-slide">
                        <img src="{{ asset('images/hangar1.png') }}" alt="Slide 2" class="object-cover w-full h-full" />
                    </div>
                    <div class="swiper-slide">
                        <img src="{{ asset('images/hangar2.png') }}" alt="Slide 3" class="object-cover w-full h-full" />
                    </div>
                    <div class="swiper-slide">
                        <img src="{{ asset('images/hangar3.png') }}" alt="Slide 4" class="object-cover w-full h-full" />
                    </div>
                    <div class="swiper-slide">
                        <img src="{{ asset('images/hangar4.png') }}" alt="Slide 5" class="object-cover w-full h-full" />
                    </div>
                </div>
                <div class="swiper-pagination bottom-3"></div>
            </div>
        </div>
    </div>

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script>
        const swiper = new Swiper('.swiper', {
            loop: true,
            autoplay: {
                delay: 4000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
        });
    </script>
</body>
</html>
