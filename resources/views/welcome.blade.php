<script src="//unpkg.com/alpinejs" defer></script>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>GMF | Reliability Report</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#0F0826] text-white font-sans min-h-screen overflow-hidden relative">

    {{-- Main Content --}}
    <div class="flex flex-col md:flex-row items-center justify-between h-screen relative z-10">
        {{-- Left Content --}}
        <div class="px-8 md:px-24 md:w-1/2 text-left space-y-6">
            <img src="{{ asset('images/gmfwhite.png') }}" alt="Logo GMF" class="h-28 mb-6">
            <h4 class="text-sm uppercase tracking-wider text-gray-400">Selamat Datang di</h4>
            <h1 class="text-5xl font-light leading-tight">Reliability Report</h1>
            <p class="text-gray-300 max-w-md">Platform laporan keandalan pesawat yang membantu memantau performa operasional dan mendukung pengambilan keputusan berbasis data..</p>
            <a href="{{ route('login') }}" class="inline-block bg-green-500 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded shadow-md transition duration-300">
                MASUK
            </a>
            <p class="text-xs text-gray-500 mt-10">© {{ date('Y') }} PT Garuda Maintenance Facility Aero Asia Tbk</p>
        </div>

    {{-- Right Content --}}
    <div class="md:w-3/4 h-full relative overflow-hidden flex items-center justify-center"
        x-data="{
            images: [
                '{{ asset('images/hangar.png') }}',
                '{{ asset('images/hangar1.png') }}',
                '{{ asset('images/hangar2.png') }}',
                '{{ asset('images/hangar3.png') }}',
                '{{ asset('images/hangar4.png') }}'
            ],
            currentIndex: 0,
            setIndex(index) {
                this.currentIndex = index;
            },
            init() {
                setInterval(() => {
                    this.currentIndex = (this.currentIndex + 1) % this.images.length;
                }, 4000); // Change image every 4 seconds
            }
        }"
    >
        <!-- Image Carousel -->
        <template x-for="(image, index) in images" :key="index">
            <img
                x-show="currentIndex === index"
                x-transition:enter="transition-all transform duration-1000 ease-in-out"
                x-transition:enter-start="opacity-0 translate-x-10 scale-95"
                x-transition:enter-end="opacity-100 translate-x-0 scale-100"
                x-transition:leave="transition-all transform duration-1000 ease-in-out"
                x-transition:leave-start="opacity-100 translate-x-0 scale-100"
                x-transition:leave-end="opacity-0 -translate-x-10 scale-95"
                :src="image"
                alt="GMF Image"
                class="absolute rounded-2xl shadow-lg object-cover w-[85%] h-[70%] z-10"
            >
        </template>

        <!-- Dot Navigation -->
        <div class="absolute bottom-4 flex space-x-3">
            <template x-for="(image, index) in images" :key="index">
                <button
                    :class="{'bg-green-500': currentIndex === index, 'bg-gray-500': currentIndex !== index}"
                    @click="setIndex(index)"
                    class="w-3 h-3 rounded-full transition-all"
                ></button>
            </template>
        </div>
    </div>
</body>
</html>
