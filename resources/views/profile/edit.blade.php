<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <!-- Tombol Back ke Dashboard dengan Icon -->
            <a href="{{ route('dashboard') }}"
               class="text-gray-600 hover:text-blue-600 transition duration-150 ease-in-out"
               title="Back to Dashboard">
                <!-- Heroicon: Arrow Left (tanpa lingkaran) -->
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
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

    <!-- Skeleton Loading Overlay -->
    <div id="page-loader" class="fixed inset-0 bg-white/80 backdrop-blur-sm z-50 opacity-0 pointer-events-none flex items-center justify-center transition-opacity duration-300">
        <div class="w-full max-w-md px-6 space-y-4 animate-pulse">
            <div class="h-4 bg-gray-300 rounded w-3/4"></div>
            <div class="h-4 bg-gray-300 rounded w-full"></div>
            <div class="h-4 bg-gray-300 rounded w-5/6"></div>
            <div class="h-4 bg-gray-300 rounded w-1/2"></div>
            <div class="h-10 bg-gray-300 rounded w-full mt-6"></div>
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
                    loader.classList.remove('opacity-0', 'pointer-events-none');
                    loader.classList.add('opacity-100');
                    setTimeout(() => {
                        window.location.href = backButton.href; // Redirect manual
                    }, 500); // Delay untuk menampilkan animasi
                });
            }
        });
    </script>
</x-app-layout>