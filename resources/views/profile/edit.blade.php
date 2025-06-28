<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between px-6 py-3 bg-white border-b header-responsive"
            style="border-color:#006ba1; border-bottom-width:1.5px; border-style:solid; border-bottom-left-radius:1rem; border-bottom-right-radius:1rem; box-shadow:0 1px 4px 0 rgba(0,107,161,0.07);">
            <div class="flex items-center gap-3 flex-wrap">
                <button id="back-button"
                    class="flex items-center gap-2 px-3 py-1 rounded bg-blue-50 text-blue-700 hover:bg-blue-100 hover:text-blue-900 transition"
                    title="Back to Dashboard">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                         stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    <span class="font-medium">Back</span>
                </button>
                <h2 class="font-bold text-2xl text-[#006ba1] flex items-center gap-2">
                    <span class="inline-block w-1 h-6 rounded-full bg-[#006ba1]"></span>
                    Profile
                </h2>
            </div>
            <span class="text-base font-semibold text-[#006ba1]">GMF Reliability</span>
        </div>
    </x-slot>

    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-blue-100 py-12 px-4 sm:px-8">
        <div class="max-w-4xl mx-auto space-y-8">
            <div class="p-8 bg-white rounded-2xl shadow-2xl border border-blue-100">
                <div class="max-w-xl mx-auto">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-8 bg-white rounded-2xl shadow-2xl border border-blue-100">
                <div class="max-w-xl mx-auto">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-8 bg-white rounded-2xl shadow-2xl border border-blue-100">
                <div class="max-w-xl mx-auto">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>

    <!-- Loader Overlay -->
    <div id="page-loader" class="fixed inset-0 z-50 flex-col items-center justify-center bg-gradient-to-br from-white via-blue-50 to-blue-100 bg-opacity-95 hidden transition-all duration-500 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl p-8 border border-gray-200">
            <div class="flex space-x-3 mb-6 justify-center">
                <div class="w-4 h-16 bg-gradient-to-t from-blue-800 to-blue-500 rounded-full animate-loader-bar"></div>
                <div class="w-4 h-16 bg-gradient-to-t from-blue-700 to-blue-400 rounded-full animate-loader-bar delay-150"></div>
                <div class="w-4 h-16 bg-gradient-to-t from-green-600 to-green-400 rounded-full animate-loader-bar delay-300"></div>
            </div>
            <span id="loader-text" class="text-lg font-semibold text-gray-700 block text-center">Redirecting page...</span>
            <div class="mt-4 w-48 h-1 bg-gray-200 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-blue-600 to-green-500 rounded-full animate-pulse"></div>
            </div>
        </div>
    </div>

    <style>
        @keyframes bar-bounce {
            0%, 100% { transform: scaleY(0.4) translateY(0); opacity: 0.6; }
            25% { transform: scaleY(0.8) translateY(-8px); opacity: 0.8; }
            50% { transform: scaleY(1.2) translateY(-16px); opacity: 1; }
            75% { transform: scaleY(0.8) translateY(-8px); opacity: 0.8; }
        }
        .animate-loader-bar { animation: bar-bounce 1.4s infinite ease-in-out; }
        .delay-150 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.4s; }
        @media (max-width: 768px) {
            .header-responsive {
                flex-direction: column !important;
                align-items: flex-start !important;
                gap: 1rem !important;
                padding: 1rem 1rem !important;
            }
            .header-responsive h2 {
                font-size: 1.25rem !important;
            }
            .header-responsive .text-base {
                font-size: 0.95rem !important;
            }
        }
    </style>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            lucide.createIcons();

            const backButton = document.getElementById('back-button');
            const loader = document.getElementById('page-loader');

            backButton.addEventListener('click', function () {
                loader.classList.remove('hidden');
                loader.classList.add('flex');
                setTimeout(() => {
                    window.location.href = "{{ route('dashboard') }}";
                }, 500);
            });
        });
    </script>
</x-app-layout>
