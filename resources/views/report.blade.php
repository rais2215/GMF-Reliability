<x-app-layout>
    <!-- Page Loader: 3 Bar Loader -->
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

    <!-- Header -->
    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <!-- Back Button -->
            <button id="back-button"
                class="text-gray-600 hover:text-blue-600 transition duration-150 ease-in-out"
                title="Back to Dashboard">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
            </button>

            <!-- Title Report -->
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Report
            </h2>
        </div>
    </x-slot>

    <div class="flex min-h-screen mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <!-- Sidebar -->
        <div class="w-[17%] bg-white p-4 border-r border-gray-300 ml-0">
            <div class="mb-4 space-y-2">
                <a href="/report" class="font-bold border-b-2 border-black w-full py-2 block">All Report</a>
            </div>

            <ul class="space-y-2">
                <!-- Sidebar Links -->
                <li><a href="#" class="flex items-center text-blue-500 hover:text-blue-900 sidebar-item" data-url="{{ route('report.aos.index') }}"><span class="mr-2 text-xl">✈</span> Aircraft Operation Summary</a></li>
                <li><a href="#" class="flex items-center text-blue-500 hover:text-blue-900 sidebar-item" data-url="{{ route('report.pilot.index') }}"><span class="mr-2 text-xl">✈</span> Pilot Report And Technical Delay</a></li>
                <li><a href="#" class="flex items-center text-blue-500 hover:text-blue-900 sidebar-item" data-url="{{ route('report.cumulative') }}"><span class="mr-2 text-xl">✈</span> Cumulative Flight Hours and Take Off</a></li>
                <li><a href="#" class="flex items-center text-blue-500 hover:text-blue-900 sidebar-item"><span class="mr-2 text-xl">✈</span> Etops Reliability Report</a></li>
                <li><a href="#" class="flex items-center text-blue-500 hover:text-blue-900 sidebar-item"><span class="mr-2 text-xl">✈</span> Etops Event</a></li>
                <li><a href="#" class="flex items-center text-blue-500 hover:text-blue-900 sidebar-item"><span class="mr-2 text-xl">✈</span> Reliability Graph</a></li>
                <li><a href="#" class="flex items-center text-blue-500 hover:text-blue-900 sidebar-item"><span class="mr-2 text-xl">✈</span> Engine Operation Summary</a></li>
                <li><a href="#" class="flex items-center text-blue-500 hover:text-blue-900 sidebar-item"><span class="mr-2 text-xl">✈</span> Engine Removal & Shutdown</a></li>
                <li><a href="#" class="flex items-center text-blue-500 hover:text-blue-900 sidebar-item"><span class="mr-2 text-xl">✈</span> Weekly Reliability Report</a></li>
                <li><a href="#" class="flex items-center text-blue-500 hover:text-blue-900 sidebar-item"><span class="mr-2 text-xl">✈</span> Summary Report</a></li>
                <li><a href="#" class="flex items-center text-blue-500 hover:text-blue-900 sidebar-item"><span class="mr-2 text-xl">✈</span> Graph ATA Pilot</a></li>
                <li><a href="#" class="flex items-center text-blue-500 hover:text-blue-900 sidebar-item"><span class="mr-2 text-xl">✈</span> Graph ATA Delay</a></li>
                <li><a href="#" class="flex items-center text-blue-500 hover:text-blue-900 sidebar-item"><span class="mr-2 text-xl">✈</span> APU Operation Summary</a></li>
                <li><a href="#" class="flex items-center text-blue-500 hover:text-blue-900 sidebar-item"><span class="mr-2 text-xl">✈</span> APU Removal</a></li>
                <li><a href="#" class="flex items-center text-blue-500 hover:text-blue-900 sidebar-item"><span class="mr-2 text-xl">✈</span> Cabin Reliability Report</a></li>

                <!-- Divider -->
                <li><hr class="border-t border-gray-300 my-2"></li>

                <!-- Fitur Gabungan di Paling Bawah -->
                <li>
                     <a href="#" class="flex items-center text-blue-500 hover:text-blue-900 sidebar-item"
                       data-url="{{ route('report.combined.index') }}">
                        <i data-lucide="download" class="mr-2 w-5 h-5"></i> Export AOS & Pilot Report (PDF)
                    </a>
                </li>
            </ul>
        </div>

        <!-- Main Content Area -->
        <div class="flex-1 p-6" id="main-content" style="background-color: #7EBB1A;">
            <h1 class="text-3xl text-white font-bold mb-4">Main Content Area</h1>
            <p class="text-white">This is where the main content will go. You can place your reports, data, or any other content here.</p>
        </div>
    </div>

    <!-- Script -->
    <script src="{{ asset('js/report.js') }}"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
            const loader = document.getElementById('page-loader');

            // Back Button
            document.getElementById('back-button').addEventListener('click', function (e) {
                e.preventDefault();
                loader.classList.remove('hidden');
                loader.classList.add('flex');

                setTimeout(() => {
                    window.location.href = "{{ route('dashboard') }}";
                }, 500);
            });

            // Link Navigasi
            const links = document.querySelectorAll('a[href]:not([target="_blank"])');
            links.forEach(link => {
                link.addEventListener('click', function (e) {
                    const href = this.getAttribute('href');
                    if (!href || href.startsWith('#') || href === window.location.href) return;

                    e.preventDefault();
                    loader.classList.remove('hidden');
                    loader.classList.add('flex');

                    setTimeout(() => {
                        window.location.href = href;
                    }, 500);
                });
            });
        });
    </script>
</x-app-layout>