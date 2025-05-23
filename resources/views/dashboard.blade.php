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

    <div class="flex min-h-screen bg-gray-100">
        <!-- Sidebar -->
        <div class="w-96 bg-white shadow-md flex flex-col">
            <div class="p-6 bg-sky-200 flex items-center justify-between">
                <img src="{{ asset('images/gmfblue.png') }}" alt="GMF AeroAsia" class="h-8">
            </div>

            <div class="flex flex-col items-center py-6 border-b border-gray-200">
                @php
                    $initial = strtoupper(substr(Auth::user()->name, 0, 1));
                @endphp
                <div class="w-40 h-40 flex items-center justify-center rounded-full bg-blue-600 text-white text-6xl font-bold mb-3">
                    {{ $initial }}
                </div>
                <h2 class="text-xl font-bold text-gray-800">{{ Auth::user()->name ?? '' }}</h2>
                <div class="w-full px-8 mt-4 space-y-2">
                    <a href="{{ route('profile.edit') }}" class="w-full flex items-center justify-center py-2 px-4 rounded-md bg-sky-200 text-blue-800">
                        <i data-lucide="user" class="w-5 h-5 mr-2"></i>
                        Profile Information
                    </a>
                </div>
            </div>

            <div class="flex-1 p-4">
                <ul class="space-y-3">
                    <li>
                        <a href="{{ route('dashboard') }}" class="w-full flex items-center p-3 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                            <div class="w-8 h-8 flex items-center justify-center mr-3 text-blue-600">
                                <i data-lucide="home" class="w-5 h-5"></i>
                            </div>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('report') }}" class="w-full flex items-center p-3 rounded-lg {{ request()->routeIs('report') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                            <div class="w-8 h-8 flex items-center justify-center mr-3 text-green-600">
                                <i data-lucide="bar-chart-2" class="w-5 h-5"></i>
                            </div>
                            <span>Report</span>
                        </a>
                    </li>
                    <li>
                        <a href="https://dashboard-reliability.gmf-aeroasia.co.id/" target="_blank" class="w-full flex items-center p-3 rounded-lg text-gray-700 hover:bg-gray-100">
                            <div class="w-8 h-8 flex items-center justify-center mr-3 text-yellow-600">
                                <i data-lucide="clock" class="w-5 h-5"></i>
                            </div>
                            <span>Techlog Delay</span>
                        </a>
                    </li>
                    <li>
                        <form id="logout-form" method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center p-3 rounded-lg text-gray-700 hover:bg-gray-100">
                                <div class="w-8 h-8 flex items-center justify-center mr-3 text-red-600">
                                    <i data-lucide="log-out" class="w-5 h-5"></i>
                                </div>
                                <span>Log Out</span>
                            </button>
                        </form>
                    </li>
                    @if(Auth::user()->Position === 'Admin')
                        <li>
                            <a href="{{ route('user-setting') }}" class="w-full flex items-center p-3 rounded-lg {{ request()->routeIs('user-setting') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                                <div class="w-8 h-8 flex items-center justify-center mr-3 text-purple-600">
                                    <i data-lucide="settings" class="w-5 h-5"></i>
                                </div>
                                <span>User Setting</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </div>

            <div class="p-4 text-xs text-center text-gray-500">
                © 2025, Made with ❤ by Reliability Management for a better way
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col">
            <!-- Header Banner -->
            <div class="bg-gradient-to-r from-blue-500 to-teal-400 p-6 text-white relative overflow-hidden">
                <div class="relative z-10">
                    <h1 class="text-2xl font-bold mb-2">Hi, Welcome to!</h1>
                    <h2 class="text-3xl font-bold mb-4">Reliability Dashboard</h2>
                    <p class="opacity-90">
                        If you have any trouble, please contact to<br>
                        <span class="font-medium">spoc-ict@gmf-aeroasia.co.id</span>
                    </p>
                </div>
                <div class="absolute right-6 top-6 text-white/80">
                    <span id="current-datetime">{{ now()->format('l, d M Y, H:i:s') }}</span>
                </div>
                <!-- Background Image - Semi-transparent -->
                <div class="absolute inset-0 bg-cover bg-center opacity-20" style="background-image: url('{{ asset('images/hangar3.png') }}')"></div>
            </div>

            <!-- Content Area -->
            <div class="flex-1 p-6 overflow-auto">
                <!-- KPI Cards -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
                    <div class="bg-white p-4 rounded-xl shadow">
                        <h4 class="text-sm text-gray-500">Total Penerbangan</h4>
                        <p class="text-2xl font-semibold text-blue-600">
                            {{ $totalFlights ?? '-' }}
                        </p>
                    </div>
                    <div class="bg-white p-4 rounded-xl shadow">
                        <h4 class="text-sm text-gray-500">Delay Hari Ini</h4>
                        <p class="text-2xl font-semibold text-red-500">
                            {{ $todayDelays ?? '-' }}
                        </p>
                    </div>
                    <div class="bg-white p-4 rounded-xl shadow">
                        <h4 class="text-sm text-gray-500">Dispatch Rate</h4>
                        <p class="text-2xl font-semibold text-green-600">
                            {{ $dispatchRate ?? '-' }}%
                        </p>
                    </div>
                </div>

                <!-- Power BI Report Section -->
                <div class="bg-white p-4 rounded-xl shadow mb-6">
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
            </div>
        </div>
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