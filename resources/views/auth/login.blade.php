<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login | GMF Reliability Report</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- AlpineJS -->
    <script src="https://unpkg.com/alpinejs" defer></script>

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#0F0826] text-white font-sans min-h-screen overflow-hidden relative">

    <div class="flex flex-col md:flex-row items-center justify-between h-screen relative z-10">
        <!-- Left: Login Form -->
        <div class="px-8 md:px-24 md:w-1/2 text-left space-y-6">
            <img src="{{ asset('images/gmfwhite.png') }}" alt="Logo GMF" class="h-28 mb-6">
            <h1 class="text-4xl font-light">Sign in to your account</h1>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <!-- Email Address -->
                <div>
                    <x-input-label for="email" :value="'Email'" class="text-green-500" />
                    <x-text-input id="email" class="block mt-1 w-full text-black" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div>
                    <x-input-label for="password" :value="'Password'" class="text-green-500" />
                    <x-text-input id="password" class="block mt-1 w-full text-black"
                                  type="password" name="password" required autocomplete="current-password" />
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
                    <button type="submit" class="px-6 py-2 bg-green-500 text-white text-sm font-semibold rounded-lg shadow hover:bg-green-800 transition">
                        LOG IN
                    </button>
                    <a href="{{ route('register') }}" class="inline-block px-6 py-2 bg-green-500 text-white text-sm font-semibold rounded-lg shadow hover:bg-green-800 transition">
                        REGISTER
                    </a>
                </div>
            </form>
        </div>

        <!-- Right: Background Carousel -->
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
                    }, 4000);
                }
             }"
             x-init="init()"
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
    </div>

</body>
</html>
