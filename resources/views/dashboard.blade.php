<x-app-layout>
    {{-- LOADER OVERLAY --}}
    <div id="page-loader" class="fixed inset-0 z-50 flex-col items-center justify-center bg-white bg-opacity-80 hidden transition-opacity duration-300">
        <div class="flex space-x-2 mb-4">
            <div class="w-3 h-12 bg-blue-600 rounded animate-loader-bar"></div>
            <div class="w-3 h-12 bg-blue-600 rounded animate-loader-bar delay-150"></div>
            <div class="w-3 h-12 bg-blue-600 rounded animate-loader-bar delay-300"></div>
        </div>
        <span id="loader-text" class="text-sm font-medium text-gray-800">Redirecting page...</span>
    </div>

    {{-- SIDEBAR --}}
    <aside class="fixed top-0 left-0 h-full w-64 bg-white shadow-xl z-40 flex flex-col justify-between transition-all duration-300" id="sidebar">
        <div class="overflow-y-auto flex-1">
            <div class="px-6 pt-6 pb-4 text-center">
                <img src="{{ asset('images/gmfblue.png') }}" alt="GMF Logo" class="w-40 mx-auto mb-6 object-contain">
                <hr class="border-t border-gray-200 my-4">
                @php $initial = strtoupper(substr(Auth::user()->name, 0, 1)); @endphp
                <div class="w-20 h-20 rounded-full bg-[#0F265C] text-white flex items-center justify-center text-3xl font-bold mx-auto shadow-md">
                    {{ $initial }}
                </div>
                <p class="mt-2 font-semibold text-gray-800 text-sm">{{ Auth::user()->name }}</p>
                <div class="mt-2">
                    <a href="javascript:void(0)" onclick="fadeNavigate('{{ route('profile.edit') }}')" class="flex items-center justify-center space-x-2 text-gray-700 hover:text-blue-500 transition">
                        <i data-lucide="user" class="w-5 h-5"></i>
                        <span>Edit Profile</span>
                    </a>
                </div>
                <hr class="border-t border-gray-200 my-6">
                <div class="text-xs uppercase text-gray-500 font-semibold text-left pl-2 mb-2">Menu</div>
                <nav class="space-y-4 text-left px-2">
                    <a href="javascript:void(0)" onclick="fadeNavigate('{{ route('dashboard') }}')" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-700 hover:text-white hover:bg-blue-500 transition duration-300 group">
                        <i data-lucide="layout-dashboard" class="w-5 h-5 group-hover:text-white transition duration-300"></i>
                        <span class="font-medium">Dashboard</span>
                    </a>
                    @if(Route::has('report.index'))
                        <a href="javascript:void(0)" onclick="fadeNavigate('{{ route('report.index') }}')" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-700 hover:text-white hover:bg-blue-500 transition duration-300 group">
                            <i data-lucide="file-text" class="w-5 h-5 group-hover:text-white transition duration-300"></i>
                            <span class="font-medium">Report</span>
                        </a>
                    @else
                        <a href="javascript:void(0)" onclick="fadeNavigate('/report')" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-700 hover:text-white hover:bg-blue-500 transition duration-300 group">
                            <i data-lucide="file-text" class="w-5 h-5 group-hover:text-white transition duration-300"></i>
                            <span class="font-medium">Report</span>
                        </a>
                    @endif
                    <a href="javascript:void(0)" onclick="fadeNavigate('https://dashboard-reliability.gmf-aeroasia.co.id/', true)" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-700 hover:text-white hover:bg-blue-500 transition duration-300 group">
                        <i data-lucide="clock" class="w-5 h-5 group-hover:text-white transition duration-300"></i>
                        <span class="font-medium">Techlog Delay</span>
                    </a>
                </nav>
                <hr class="border-t border-gray-200 my-6">
            </div>
        </div>
        <div class="px-6 mb-6">
            <form method="POST" action="{{ route('logout') }}" onsubmit="showLoader('Logging out...')">
                @csrf
                <button type="submit" class="w-full flex items-center space-x-3 text-red-500 hover:text-red-300 transition">
                    <i data-lucide="log-out" class="w-5 h-5"></i>
                    <span>Log Out</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- MAIN CONTENT --}}
    <div id="main-content" class="min-h-screen bg-gray-50 pt-10 pb-20 ml-64 transition-opacity duration-500">
        <div class="mb-6 px-6">
            <h1 class="text-3xl font-bold text-gray-800">Reliability Dashboard</h1>
        </div>

        {{-- Welcome Card --}}
        <div class="px-6 mb-10">
            <div class="relative rounded-2xl overflow-hidden shadow bg-cover bg-center text-white w-full h-44 md:h-52 xl:h-56" style="background-image: url('{{ asset('images/bgwelcome.jpg') }}');">
                <div class="absolute inset-0 bg-black/40 backdrop-blur-sm p-4 md:p-6 flex flex-col justify-center">
                    <div>
                        <h2 class="text-lg md:text-2xl font-semibold leading-tight">Selamat Datang,</h2>
                        <h1 class="text-2xl md:text-4xl font-bold leading-tight">{{ Auth::user()->name }}</h1>
                        <p class="text-xs md:text-sm mt-2">Butuh bantuan? <a href="mailto:spoc-ict@gmf-aeroasia.co.id" class="underline font-semibold">spoc-ict@gmf-aeroasia.co.id</a></p>
                    </div>
                </div>
                <div id="clock" class="absolute top-4 right-6 text-xs md:text-sm font-semibold whitespace-nowrap"></div>
            </div>
        </div>

        {{-- Power BI Card --}}
        <div class="px-6">
            <div class="bg-white p-6 shadow-lg rounded-xl">
                <div class="mb-4">
                    <h4 class="text-2xl font-semibold text-gray-800">Dispatch Reliability Report</h4>
                </div>
                <div class="w-full h-[45vh] overflow-hidden rounded-md">
                    <iframe class="w-full h-full border-none rounded-md" src="https://app.powerbi.com/view?r=eyJrIjoiNWYxNjYxZGItZTVjZS00YmQxLWIxMTctNjU3NDU0YmM0ODI5IiwidCI6ImIxNTAxOTBhLTE2ZjMtNGZiYS04YmY2LTNhNjIwYWI3NjA3OSIsImMiOjEwfQ%3D%3D" allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes bar-bounce {
            0%, 100% { transform: scaleY(0.5); opacity: 0.5; }
            50% { transform: scaleY(1.2); opacity: 1; }
        }
        .animate-loader-bar { animation: bar-bounce 1s infinite ease-in-out; }
        .delay-150 { animation-delay: 0.15s; }
        .delay-300 { animation-delay: 0.3s; }

        @media (max-width: 768px) {
            #main-content { margin-left: 0; }
            #sidebar { width: 100%; position: relative; box-shadow: none; }
        }
    </style>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
            updateClock();
        });

        function updateClock() {
            const now = new Date();
            const hari = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
            const bulan = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
            const fullTime = `${hari[now.getDay()]}, ${now.getDate()} ${bulan[now.getMonth()]} ${now.getFullYear()}, ${now.getHours().toString().padStart(2, '0')}:${now.getMinutes().toString().padStart(2, '0')}:${now.getSeconds().toString().padStart(2, '0')}`;
            document.getElementById("clock").textContent = fullTime;
        }

        setInterval(updateClock, 1000);

        function showLoader(message = 'Redirecting page...') {
            document.getElementById('loader-text').textContent = message;
            document.getElementById('page-loader').classList.remove('hidden');
            document.getElementById('page-loader').classList.add('flex');
        }

        function fadeNavigate(url, isExternal = false) {
            showLoader();
            setTimeout(() => {
                if (isExternal) {
                    window.open(url, '_blank');
                    document.getElementById('page-loader').classList.add('hidden');
                } else {
                    window.location.href = url;
                }
            }, 500);
        }
    </script>
</x-app-layout>
