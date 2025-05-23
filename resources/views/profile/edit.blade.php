<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <!-- Tombol Back ke Dashboard dengan Icon -->
            <a href="{{ route('dashboard') }}"
               class="text-gray-600 hover:text-blue-600 transition duration-150 ease-in-out"
               title="Back to Dashboard">
                <!-- Heroicon: Arrow Left Circle -->
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M11.25 15.75L7.5 12m0 0l3.75-3.75M7.5 12h9M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </a>

            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Profile
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Spinner Overlay -->
    <div id="page-loader" class="fixed inset-0 bg-white/70 backdrop-blur-sm z-50 invisible pointer-events-none flex items-center justify-center transition-opacity duration-300">
        <div class="flex flex-col items-center">
            <i data-lucide="loader" class="w-10 h-10 text-blue-600 animate-spin mb-3"></i>
            <p class="text-sm text-gray-600">Loading...</p>
        </div>
    </div>

    <!-- Script untuk loader dan ikon -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            lucide.createIcons(); // Inisialisasi ikon Lucide

            const backButton = document.querySelector('a[href="{{ route('dashboard') }}"]');
            const loader = document.getElementById('page-loader');

            if (backButton && loader) {
                backButton.addEventListener('click', function (e) {
                    e.preventDefault(); // Hentikan navigasi default
                    loader.classList.remove('hidden'); // Tampilkan loader
                    setTimeout(() => {
                        window.location.href = backButton.href; // Redirect manual
                    }, 300); // Delay biar animasi kelihatan
                });
            }
        });
    </script>
</x-app-layout>