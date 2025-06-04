<x-app-layout>
    <!-- Loading Skeleton Overlay -->
    <div id="page-loader" class="fixed inset-0 bg-white/70 backdrop-blur-sm z-50 hidden items-center justify-center transition-opacity duration-300">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-8 w-full max-w-5xl">
            @for ($i = 0; $i < 3; $i++)
                <div class="bg-white p-6 rounded-xl shadow animate-pulse">
                    <div class="h-4 bg-gray-300 rounded w-1/2 mb-4"></div>
                    <div class="h-8 bg-gray-400 rounded w-3/4"></div>
                </div>
            @endfor
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
                <!-- Fitur Navigasi Ganti KPI Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <a href="{{ url('/report') }}" 
                                           class="flex bg-gradient-to-r from-[#0066B3] to-[#7EBB1A] text-white p-6 rounded-xl shadow hover:from-[#005799] hover:to-[#6ca00a] transition text-center flex-col items-center space-y-3">
                        <i data-lucide="file-text" class="lucide lucide-file-text w-10 h-10"></i>
                        <h4 class="text-xl font-semibold">Report</h4>
                        <p class="text-sm opacity-90">Lihat detail report lengkap</p>
                    </a>

                    <a href="https://dashboard-reliability.gmf-aeroasia.co.id/" target="_blank" 
                                           class="bg-gradient-to-r from-[#0066B3] to-[#7EBB1A] text-white p-6 rounded-xl shadow hover:from-[#005799] hover:to-[#6ca00a] transition text-center flex flex-col items-center space-y-3">
                        <i data-lucide="clock" class="lucide lucide-clock w-10 h-10"></i>
                        <h4 class="text-xl font-semibold">Techlog Delay</h4>
                        <p class="text-sm opacity-90">Cek data delay techlog terbaru</p>
                    </a>
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

            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');

            const formattedDateTime = ${day}, ${date} ${month} ${year}, ${hours}:${minutes}:${seconds};

            document.getElementById('current-datetime').textContent = formattedDateTime;
        }

        document.addEventListener('DOMContentLoaded', () => {
            updateDateTime();
            setInterval(updateDateTime, 1000);
        });
    </script>

    <!-- Loader Display Script -->
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