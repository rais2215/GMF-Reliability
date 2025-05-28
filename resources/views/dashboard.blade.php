<x-app-layout>
    <!-- Loading Spinner Overlay -->
    <div id="page-loader" class="fixed inset-0 bg-white/70 backdrop-blur-sm z-50 hidden items-center justify-center transition-opacity duration-300">
        <div class="flex flex-col items-center">
            <i data-lucide="loader" class="w-10 h-10 text-blue-600 animate-spin mb-3"></i>
            <p class="text-sm text-gray-600">Loading...</p>
        </div>
    </div>

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mx-6 my-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="min-h-screen bg-gray-100 flex flex-col">
        <!-- Main Content Area (No Sidebar) -->
        <main class="flex-1 flex flex-col">
            <!-- Header Banner -->
            <header class="bg-gradient-to-r from-[#0066B3] to-[#7EBB1A] p-8 text-white relative overflow-hidden">
                <div class="relative z-10">
                    <h1 class="text-3xl font-bold mb-2">Hi, Welcome to!</h1>
                    <h2 class="text-4xl font-bold mb-4">Reliability Dashboard</h2>
                    <p class="opacity-90">
                        If you have any trouble, please contact to<br>
                        <span class="font-bold">spoc-ict@gmf-aeroasia.co.id</span>
                    </p>
                </div>
                <div class="absolute right-8 top-8 text-white/100">
                    <span id="current-datetime" class="text-lg font-semibold">{{ now()->format('l, d M Y, H:i:s') }}</span>
                </div>
            </header>

            <!-- Content Area -->
            <section class="flex-1 p-8 overflow-auto bg-gray-100">
                <!-- KPI Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-xl shadow flex flex-col items-center">
                        <h4 class="text-sm text-gray-500 mb-1">Total Penerbangan</h4>
                        <p class="text-2xl font-semibold text-blue-600">
                            {{ $totalFlights ?? '-' }}
                        </p>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow flex flex-col items-center">
                        <h4 class="text-sm text-gray-500 mb-1">Delay Hari Ini</h4>
                        <p class="text-2xl font-semibold text-red-500">
                            {{ $todayDelays ?? '-' }}
                        </p>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow flex flex-col items-center">
                        <h4 class="text-sm text-gray-500 mb-1">Dispatch Rate</h4>
                        <p class="text-2xl font-semibold text-green-600">
                            {{ $dispatchRate ?? '-' }}%
                        </p>
                    </div>
                </div>

                <!-- Power BI Report Section -->
                <div class="bg-white p-6 rounded-xl shadow mb-8">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Dispatch Reliability Report</h3>
                    </div>
                    <div class="w-full h-96 relative">
                        <iframe id="reportContainer"
                                src="https://app.powerbi.com/view?r=eyJrIjoiNWYxNjYxZGItZTVjZS00YmQxLWIxMTctNjU3NDU0YmM0ODI5IiwidCI6ImIxNTAxOTBhLTE2ZjMtNGZiYS04YmY2LTNhNjIwYWI3NjA3OSIsImMiOjEwfQ%3D%3D"
                                class="w-full h-full border-none"
                                allowfullscreen></iframe>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Date Time Script -->
    <script>
        function updateDateTime() {
            const now = new Date();
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            
            const day = days[now.getDay()];
            const date = now.getDate();
            const month = months[now.getMonth()];
            const year = now.getFullYear();
            
            // Format time with leading zeros
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            
            // Format in Indonesian style: Rabu, 22 Mei 2025, 12:00:00
            const formattedDateTime = `${day}, ${date} ${month} ${year}, ${hours}:${minutes}:${seconds}`;
            
            document.getElementById('current-datetime').textContent = formattedDateTime;
        }

        // Update immediately and then every second
        document.addEventListener('DOMContentLoaded', () => {
            updateDateTime();
            setInterval(updateDateTime, 1000);
        });
    </script>

    <!-- Spinner Script -->
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
        const loader = document.getElementById('page-loader');
        const links = document.querySelectorAll('a[href]:not([target="_blank"])');

        // Handle link clicks
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

        // Handle logout form submission
        const logoutForm = document.getElementById('logout-form');
        if (logoutForm) {
            logoutForm.addEventListener('submit', function () {
                loader.classList.remove('hidden');
                loader.classList.add('flex');
            });
        }
    });
    </script>
</x-app-layout>