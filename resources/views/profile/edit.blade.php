<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <button id="back-button"
                class="text-gray-600 hover:text-blue-600 transition duration-150 ease-in-out"
                title="Back to Dashboard">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
            </button>

            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Profile</h2>
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

    <!-- Loader Overlay (Sama dengan dashboard) -->
    <div id="page-loader" class="fixed inset-0 z-50 flex-col items-center justify-center bg-white bg-opacity-80 hidden transition-opacity duration-300">
        <div class="flex space-x-2 mb-4">
            <div class="w-3 h-12 bg-blue-600 rounded animate-loader-bar"></div>
            <div class="w-3 h-12 bg-blue-600 rounded animate-loader-bar delay-150"></div>
            <div class="w-3 h-12 bg-blue-600 rounded animate-loader-bar delay-300"></div>
        </div>
        <span id="loader-text" class="text-sm font-medium text-gray-800">Redirecting page...</span>
    </div>

    <!-- Loader Animation Style -->
    <style>
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

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            lucide.createIcons();

            const backButton = document.getElementById('back-button');
            const loader = document.getElementById('page-loader');

            backButton.addEventListener('click', function () {
                // Tampilkan loader saat kembali ke dashboard
                loader.classList.remove('hidden');
                loader.classList.add('flex');

                setTimeout(() => {
                    window.location.href = "{{ route('dashboard') }}";
                }, 500);
            });
        });
    </script>
</x-app-layout>