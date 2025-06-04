<x-app-layout>
    <!-- Page Loader -->
    <div id="page-loader" class="fixed inset-0 bg-white/70 backdrop-blur-sm z-50 hidden items-center justify-center transition-opacity duration-300">
        <div class="w-40 h-6 bg-gray-300 animate-pulse rounded"></div>
    </div>

    <!-- Header -->
    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <!-- Back to Dashboard Button (Disamakan) -->
            <a href="{{ route('dashboard') }}"
               class="text-gray-600 hover:text-blue-600 transition duration-150 ease-in-out"
               title="Back to Dashboard">
                <!-- Heroicon: Arrow Left -->
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
            </a>

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
            </ul>
        </div>
    
        <!-- Main Content Area -->
        <div class="flex-1 p-6" id="main-content" style="background-color: #7EBB1A;">
            <h1 class="text-3xl text-white font-bold mb-4">Main Content Area</h1>
            <p class="text-white">This is where the main content will go. You can place your reports, data, or any other content here.</p>
        </div>
    </div>

    <script src="{{ asset('js/report.js') }}"></script>

    <!-- Lucide Icons Script -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();

            const loader = document.getElementById('page-loader');
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