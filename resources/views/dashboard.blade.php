<x-app-layout>
    {{-- Page Loader --}}
    <div id="page-loader" class="fixed inset-0 z-50 hidden flex-col items-center justify-center backdrop-blur-2xl bg-white/40 transition-all duration-500">
        <div class="glass-card rounded-3xl shadow-2xl p-12 border border-white/20 max-w-sm w-full mx-4 bg-transparent">
            <div class="text-center space-y-4">
                <span id="loader-text" class="text-2xl font-extrabold" style="color: #0069a1;">Loading Dashboard...</span>
                <p class="text-base font-bold" style="color: #0069a1;">Please wait while we prepare your data</p>
            </div>
            <div class="mt-6 w-full h-2 bg-white/20 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-[#7EBB1A] to-[#8DC63F] rounded-full progress-bar"></div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}

    {{-- Sidebar --}}
    <aside class="fixed top-0 left-0 h-full w-72 bg-gradient-to-b from-white via-blue-50 to-gray-100 shadow-2xl z-40 flex flex-col justify-between transition-all duration-300 border-r border-gray-200 transform-gpu" id="sidebar" style="transition: box-shadow 0.3s, transform 0.4s cubic-bezier(.4,2,.6,1);">
        {{-- Sidebar Content --}}
        <div class="overflow-y-auto flex-1 custom-scrollbar">
            <div class="px-6 pt-8 pb-6">
                {{-- Logo Section --}}
                <div class="text-center mb-8 sidebar-logo-fade">
                    <div class="relative">
                        <img src="{{ asset('images/gmfblue.png') }}" alt="GMF Logo" class="w-44 mx-auto mb-6 object-contain transition-transform hover:scale-105 duration-300 drop-shadow-lg">
                    </div>
                </div>

                {{-- User Profile Section --}}
                <div class="bg-gradient-to-r from-blue-50 via-blue-100 to-blue-200 rounded-2xl p-6 mb-8 border border-blue-200 shadow-lg hover:shadow-xl transition-shadow duration-300 animate__animated" id="sidebar-profile">
                    @php $initial = strtoupper(substr(Auth::user()->name, 0, 1)); @endphp

                    <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-blue-700 via-blue-500 to-blue-400 text-white flex items-center justify-center text-4xl font-extrabold mx-auto mb-4 ring-4 ring-blue-100 border-2 transition-transform duration-300 animate__animated" id="sidebar-avatar">
                        {{ $initial }}
                    </div>

                    <h3 class="font-bold text-gray-800 text-lg text-center mb-1 tracking-wide">{{ Auth::user()->name }}</h3>
                    <div class="mb-2"></div>

                    <a href="javascript:void(0)" onclick="fadeNavigate('{{ route('profile.edit') }}')" class="group flex items-center justify-center space-x-2 bg-white hover:bg-blue-100 text-gray-700 hover:text-blue-700 py-2 px-4 rounded-xl transition-all duration-200 border border-gray-200 hover:border-blue-400 w-full shadow hover:shadow-md">
                        <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span class="font-medium">Edit Profile</span>
                    </a>
                </div>

                {{-- Current Info Widget --}}
                <div class="mt-8 bg-gradient-to-br from-gray-50 via-blue-50 to-white rounded-2xl p-4 shadow border border-blue-100 animate__animated" id="sidebar-info">
                    <h4 class="text-sm font-bold text-blue-700 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Current Info
                    </h4>
                    <div class="grid grid-cols-1 gap-3">
                        <div class="bg-white rounded-lg p-3 text-center shadow hover:shadow-md transition-shadow duration-200 border border-blue-50">
                            <div class="flex items-center justify-center gap-2 mb-1">
                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span class="text-sm font-bold text-blue-700" id="current-date">
                                    <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            const now = new Date();
                                            const dateOptions = { weekday: 'short', month: 'short', day: 'numeric' };
                                            document.getElementById('current-date').textContent = now.toLocaleDateString('en-US', dateOptions);
                                        });
                                    </script>
                                </span>
                            </div>
                            <div class="text-xs text-gray-500">Today</div>
                        </div>
                        <div class="bg-white rounded-lg p-3 text-center shadow hover:shadow-md transition-shadow duration-200 border border-green-50">
                            <div class="flex items-center justify-center gap-2 mb-1">
                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3"/>
                                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none"/>
                                </svg>
                                <span class="text-sm font-bold text-green-600" id="current-time">
                                    <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            const now = new Date();
                                            const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false };
                                            document.getElementById('current-time').textContent = now.toLocaleTimeString('en-US', timeOptions);
                                        });
                                    </script>
                                </span>
                            </div>
                            <div class="text-xs text-gray-500">Local Time</div>
                        </div>
                    </div>
                </div>

                {{-- Quick Links --}}
                <div class="mt-8 animate__animated" id="sidebar-links">
                    <h4 class="text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">Quick Links</h4>
                    <div class="flex flex-col gap-2">
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-blue-100 to-blue-50 text-blue-700 font-semibold hover:bg-blue-200 hover:text-blue-900 transition-all duration-200 sidebar-link">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7m-9 2v8m4-8v8m5 0a2 2 0 002-2V7a2 2 0 00-2-2h-3.5"/>
                            </svg>
                            Dashboard
                        </a>
                        <a href="{{ route('report.index') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-green-100 to-green-50 text-green-700 font-semibold hover:bg-green-200 hover:text-green-900 transition-all duration-200 sidebar-link">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Reports
                        </a>
                        <a href="https://dashboard-reliability.gmf-aeroasia.co.id/" target="_blank" rel="noopener noreferrer" class="flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-orange-100 to-amber-50 text-orange-700 font-semibold hover:bg-orange-200 hover:text-orange-900 transition-all duration-200 sidebar-link">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle stroke-linecap="round" stroke-linejoin="round" stroke-width="2" cx="12" cy="12" r="10"/>
                                <polyline stroke-linecap="round" stroke-linejoin="round" stroke-width="2" points="12,6 12,12 16,14"/>
                            </svg>
                            Techlog Delay
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <style>
            /* Sidebar Slide In Animation */
            #sidebar {
                will-change: transform, box-shadow;
            }
            #sidebar.open {
                animation: sidebar-slide-in 0.5s cubic-bezier(.4,2,.6,1);
            }
            @keyframes sidebar-slide-in {
                0% {
                    transform: translateX(-100%) scale(0.95) skewY(2deg);
                    box-shadow: 0 0 0 rgba(0,0,0,0);
                    opacity: 0.7;
                }
                60% {
                    transform: translateX(10px) scale(1.02) skewY(-1deg);
                    box-shadow: 0 8px 32px 0 rgba(16, 112, 202, 0.12);
                    opacity: 1;
                }
                100% {
                    transform: translateX(0) scale(1) skewY(0deg);
                    box-shadow: 0 8px 32px 0 rgba(16, 112, 202, 0.16);
                    opacity: 1;
                }
            }
            /* Sidebar Content Fade In */
            #sidebar-profile, #sidebar-info, #sidebar-links {
                opacity: 0;
                transform: translateY(30px) scale(0.98);
                transition: opacity 0.5s, transform 0.5s;
            }
            #sidebar.animated #sidebar-profile {
                opacity: 1;
                transform: translateY(0) scale(1);
                transition-delay: 0.15s;
            }
            #sidebar.animated #sidebar-info {
                opacity: 1;
                transform: translateY(0) scale(1);
                transition-delay: 0.3s;
            }
            #sidebar.animated #sidebar-links {
                opacity: 1;
                transform: translateY(0) scale(1);
                transition-delay: 0.45s;
            }
            /* Sidebar Link Hover Animation */
            .sidebar-link {
                position: relative;
                overflow: hidden;
            }
            .sidebar-link::before {
                content: '';
                position: absolute;
                left: 0; top: 0; right: 0; bottom: 0;
                background: linear-gradient(90deg,rgba(59,130,246,0.08),rgba(16,185,129,0.06));
                opacity: 0;
                transition: opacity 0.3s;
                z-index: 0;
            }
            .sidebar-link:hover::before {
                opacity: 1;
            }
        </style>
        <script>
            // Animate sidebar on open
            document.addEventListener('DOMContentLoaded', function() {
                const sidebar = document.getElementById('sidebar');
                if (sidebar) {
                    setTimeout(() => {
                        sidebar.classList.add('animated');
                    }, 200);
                }
            });
            // Animate sidebar on mobile toggle
            function toggleSidebar() {
                const sidebar = document.getElementById('sidebar');
                if (sidebar) {
                    sidebar.classList.toggle('open');
                    if (sidebar.classList.contains('open')) {
                        setTimeout(() => sidebar.classList.add('animated'), 100);
                    } else {
                        sidebar.classList.remove('animated');
                    }
                }
            }
        </script>

        {{-- Logout Section --}}
        <div class="px-6 pb-6">
            <div class="bg-red-50 rounded-xl p-3 border border-red-100">
            <form method="POST" action="{{ route('logout') }}" onsubmit="showLoader('Logging out...')">
                @csrf
                <button type="submit" class="group w-full flex items-center justify-center space-x-2 text-red-600 hover:text-white hover:bg-red-600 py-2 px-3 rounded-lg transition-all duration-300 font-medium">
                <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span>Sign Out</span>
                </button>
            </form>
            </div>
        </div>
    </aside>

    {{-- Main Content --}}
    <div id="main-content" class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-blue-100 pt-8 pb-20 ml-72 transition-all duration-500">
        <div class="dashboard-anim-wrapper">
            {{-- Header Section --}}
            <div class="mb-8 px-8 dashboard-fade dashboard-delay-1">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-700 to-blue-500 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-800 to-blue-600 bg-clip-text text-transparent">
                                Reliability Dashboard
                            </h1>
                            <p class="text-gray-600 text-lg">Aircraft Operations Monitoring</p>
                        </div>
                    </div>

                    {{-- Mobile Menu Toggle --}}
                    <button class="lg:hidden p-2 rounded-lg bg-white shadow-md" onclick="toggleSidebar()">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Welcome Card --}}
            <div class="px-8 mb-10 dashboard-fade dashboard-delay-2">
                <div class="relative rounded-2xl overflow-hidden shadow-2xl bg-cover bg-center text-white w-full h-64 md:h-72 xl:h-80" style="background-image: url('{{ asset('images/bgdashboard.png') }}');">
                    {{-- Overlay --}}
                    <div class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/50 to-transparent"></div>

                    {{-- Content --}}
                    <div class="relative h-full flex flex-col justify-center items-start p-6 md:p-10 z-10">
                        <div class="mb-4">
                            <h2 class="text-xl md:text-3xl font-light opacity-90 mb-1">Welcome,</h2>
                            <h1 class="text-3xl md:text-5xl font-bold leading-tight mb-2">{{ Auth::user()->name }}</h1>
                        </div>
                        <p class="text-sm md:text-base opacity-90">
                            Need help?
                            <a href="mailto:spoc-ict@gmf-aeroasia.co.id" class="underline font-semibold hover:text-blue-200 transition-colors">
                                spoc-ict@gmf-aeroasia.co.id
                            </a>
                        </p>
                    </div>
                </div>
            </div>
            </div>

            {{-- Quick Navigation Cards Section --}}
            <div class="px-8 mb-10 dashboard-fade dashboard-delay-3">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Report Card --}}
                    <div class="relative bg-gradient-to-br from-green-50 to-emerald-100 rounded-3xl p-6 shadow-xl border border-green-200/50 hover:shadow-2xl transition-all duration-500 group overflow-hidden">
                        {{-- Background Pattern --}}
                        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-green-300/20 to-transparent rounded-full transform translate-x-8 -translate-y-8"></div>
                        <div class="absolute bottom-0 left-0 w-16 h-16 bg-gradient-to-tr from-emerald-300/20 to-transparent rounded-full transform -translate-x-6 translate-y-6"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-14 h-14 bg-gradient-to-r from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <span class="text-xs font-bold text-green-700 bg-white/70 backdrop-blur-sm px-3 py-1 rounded-full">REPORT</span>
                            </div>
                            <h3 class="text-xl font-black text-gray-800 mb-2 group-hover:text-green-700 transition-colors">Reports</h3>
                            <p class="text-gray-600 text-sm font-semibold mb-4">View and analyze reliability reports.</p>
                            @if(Route::has('report.index'))
                                <a href="{{ route('report.index') }}" class="inline-flex items-center space-x-2 bg-gradient-to-r from-green-600 to-green-500 text-white font-semibold px-5 py-2 rounded-xl shadow hover:scale-105 transition-all duration-200">
                                    <span>Go to Reports</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            @else
                                <a href="/report" class="inline-flex items-center space-x-2 bg-gradient-to-r from-green-600 to-green-500 text-white font-semibold px-5 py-2 rounded-xl shadow hover:scale-105 transition-all duration-200">
                                    <span>Go to Reports</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- Techlog Delay Card --}}
                    <div class="relative bg-gradient-to-br from-orange-50 to-amber-100 rounded-3xl p-6 shadow-xl border border-orange-200/50 hover:shadow-2xl transition-all duration-500 group overflow-hidden">
                        {{-- Background Pattern --}}
                        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-orange-300/20 to-transparent rounded-full transform translate-x-8 -translate-y-8"></div>
                        <div class="absolute bottom-0 left-0 w-16 h-16 bg-gradient-to-tr from-amber-300/20 to-transparent rounded-full transform -translate-x-6 translate-y-6"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-14 h-14 bg-gradient-to-r from-orange-500 to-amber-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <circle stroke-linecap="round" stroke-linejoin="round" stroke-width="2" cx="12" cy="12" r="10"/>
                                        <polyline stroke-linecap="round" stroke-linejoin="round" stroke-width="2" points="12,6 12,12 16,14"/>
                                    </svg>
                                </div>
                                <span class="text-xs font-bold text-orange-700 bg-white/70 backdrop-blur-sm px-3 py-1 rounded-full">TECHLOG DELAY</span>
                            </div>
                            <h3 class="text-xl font-black text-gray-800 mb-2 group-hover:text-orange-700 transition-colors">Techlog Delay</h3>
                            <p class="text-gray-600 text-sm font-semibold mb-4">Access Techlog Delay dashboard.</p>
                            <a href="https://dashboard-reliability.gmf-aeroasia.co.id/" target="_blank" rel="noopener noreferrer"
                                class="inline-flex items-center space-x-2 bg-gradient-to-r from-orange-600 to-orange-500 text-white font-semibold px-5 py-2 rounded-xl shadow hover:scale-105 transition-all duration-200"
                                onclick="fadeNavigate('https://dashboard-reliability.gmf-aeroasia.co.id/', true); return false;">
                                <span>Open Techlog Delay</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Power BI Report Card --}}
            <div class="px-8 dashboard-fade dashboard-delay-4">
                <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden">
                    {{-- Header --}}
                    <div class="bg-gradient-to-r from-blue-700 to-blue-500 px-8 py-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-2xl font-bold text-white">Dispatch Reliability Report</h2>
                                    <p class="text-blue-100">Aircraft Performance Analytics</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Power BI Content --}}
                    <div class="p-2">
                        <div class="w-full h-[55vh] overflow-hidden rounded-xl border border-gray-200">
                            <iframe class="w-full h-full border-none" src="https://app.powerbi.com/view?r=eyJrIjoiNWYxNjYxZGItZTVjZS00YmQxLWIxMTctNjU3NDU0YmM0ODI5IiwidCI6ImIxNTAxOTBhLTE2ZjMtNGZiYS04YmY2LTNhNjIwYWI3NjA3OSIsImMiOjEwfQ%3D%3D" allowfullscreen></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Styles --}}
    <style>
        /* ==============================================
        LOADER ANIMATIONS - Fixed to match edit.blade.php
        ============================================== */
        @keyframes loader-bounce {
            0%, 100% {
                transform: scaleY(0.3);
                opacity: 0.5;
            }
            50% {
                transform: scaleY(1.2);
                opacity: 1;
            }
        }

        @keyframes progress-fill {
            0% {
                width: 0%;
            }
            100% {
                width: 100%;
            }
        }

        @keyframes slide-up {
            from {
                opacity: 0;
                transform: translateY(60px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fade-in {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes shimmer {
            0% {
                transform: translateX(-100%);
            }
            100% {
                transform: translateX(100%);
            }
        }

        /* ==============================================
        LOADER CLASSES
        ============================================== */
        .loader-bar {
            animation: loader-bounce 1.4s infinite ease-in-out;
        }

        .loader-delay-1 {
            animation-delay: 0.16s;
        }

        .loader-delay-2 {
            animation-delay: 0.32s;
        }

        .progress-bar {
            animation: progress-fill 3s ease-in-out infinite;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 25px 45px rgba(0, 0, 0, 0.1);
        }

        /* ==============================================
        CUSTOM SCROLLBAR
        ============================================== */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: linear-gradient(to bottom, #1d4ed8, #059669);
            border-radius: 10px;
        }

        /* ==============================================
        RESPONSIVE DESIGN
        ============================================== */
        @media (max-width: 1024px) {
            #main-content {
                margin-left: 0;
            }

            #sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;
            }

            #sidebar.open {
                transform: translateX(0);
            }
        }

        /* ==============================================
        SIDEBAR ANIMATIONS
        ============================================== */
        #sidebar {
            will-change: transform, box-shadow, opacity;
            opacity: 0;
            transform: translateX(-40px) scale(0.98);
            transition: opacity 0.5s cubic-bezier(.4,2,.6,1), transform 0.5s cubic-bezier(.4,2,.6,1);
        }

        #sidebar.animated {
            opacity: 1;
            transform: translateX(0) scale(1);
            transition-delay: 0.05s;
        }

        /* Sidebar Slide In Animation */
        #sidebar.open {
            animation: sidebar-slide-in 0.5s cubic-bezier(.4,2,.6,1);
        }

        @keyframes sidebar-slide-in {
            0% {
                transform: translateX(-100%) scale(0.95) skewY(2deg);
                box-shadow: 0 0 0 rgba(0,0,0,0);
                opacity: 0.7;
            }
            60% {
                transform: translateX(10px) scale(1.02) skewY(-1deg);
                box-shadow: 0 8px 32px 0 rgba(16, 112, 202, 0.12);
                opacity: 1;
            }
            100% {
                transform: translateX(0) scale(1) skewY(0deg);
                box-shadow: 0 8px 32px 0 rgba(16, 112, 202, 0.16);
                opacity: 1;
            }
        }

        /* ==============================================
        SIDEBAR CONTENT ANIMATIONS
        ============================================== */
        .sidebar-logo-fade {
            opacity: 0;
            transform: translateY(30px) scale(0.98);
            transition: opacity 0.5s, transform 0.5s;
        }

        #sidebar.animated .sidebar-logo-fade {
            opacity: 1;
            transform: translateY(0) scale(1);
            transition-delay: 0.05s;
        }

        /* Sidebar Content Fade In */
        #sidebar-profile, #sidebar-info, #sidebar-links {
            opacity: 0;
            transform: translateY(30px) scale(0.98);
            transition: opacity 0.5s, transform 0.5s;
        }

        #sidebar.animated #sidebar-profile {
            opacity: 1;
            transform: translateY(0) scale(1);
            transition-delay: 0.15s;
        }

        #sidebar.animated #sidebar-info {
            opacity: 1;
            transform: translateY(0) scale(1);
            transition-delay: 0.3s;
        }

        #sidebar.animated #sidebar-links {
            opacity: 1;
            transform: translateY(0) scale(1);
            transition-delay: 0.45s;
        }

        /* ==============================================
        SIDEBAR LINK ANIMATIONS
        ============================================== */
        .sidebar-link {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .sidebar-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, rgba(59,130,246,0.08), rgba(16,185,129,0.06));
            opacity: 0;
            transition: opacity 0.3s;
            z-index: 0;
        }

        .sidebar-link:hover::before {
            opacity: 1;
        }

        .sidebar-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.15);
        }

        /* ==============================================
        DASHBOARD CONTENT ANIMATIONS
        ============================================== */
        .dashboard-fade {
            opacity: 0;
            transform: translateY(40px) scale(0.98);
            transition: opacity 0.7s cubic-bezier(.4,2,.6,1), transform 0.7s cubic-bezier(.4,2,.6,1);
        }

        .dashboard-fade.animated {
            opacity: 1;
            transform: translateY(0) scale(1);
        }

        .dashboard-delay-1 { transition-delay: 0.1s; }
        .dashboard-delay-2 { transition-delay: 0.25s; }
        .dashboard-delay-3 { transition-delay: 0.4s; }
        .dashboard-delay-4 { transition-delay: 0.55s; }

        /* ==============================================
        CARD HOVER EFFECTS
        ============================================== */
        .card-enhanced {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .card-enhanced::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(126, 187, 26, 0.1), transparent);
            transition: left 0.6s ease;
        }

        .card-enhanced:hover::before {
            left: 100%;
        }

        .card-enhanced:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 107, 161, 0.15);
        }

        /* ==============================================
        NAVIGATION CARD ANIMATIONS
        ============================================== */
        .nav-card {
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative;
            overflow: hidden;
        }

        .nav-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.8s ease;
        }

        .nav-card:hover::before {
            left: 100%;
        }

        .nav-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        }

        /* ==============================================
        BUTTON ENHANCEMENTS
        ============================================== */
        .btn-enhanced {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .btn-enhanced::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            transition: left 0.5s ease;
        }

        .btn-enhanced:hover::before {
            left: 100%;
        }

        .btn-enhanced:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 107, 161, 0.3);
        }

        /* ==============================================
        WELCOME CARD ANIMATIONS
        ============================================== */
        .welcome-card {
            position: relative;
            overflow: hidden;
            background-attachment: fixed;
            transition: all 0.5s ease;
        }

        .welcome-card:hover {
            transform: scale(1.01);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.2);
        }

        /* ==============================================
        POWER BI CARD ENHANCEMENTS
        ============================================== */
        .powerbi-card {
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }

        .powerbi-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 25px 50px rgba(29, 78, 216, 0.15);
        }

        /* ==============================================
        LOADING STATES
        ============================================== */
        .loading-shimmer {
            background: linear-gradient(90deg,
                rgba(255, 255, 255, 0) 0%,
                rgba(255, 255, 255, 0.4) 50%,
                rgba(255, 255, 255, 0) 100%);
            background-size: 200% 100%;
            animation: shimmer 2s infinite;
        }

        /* ==============================================
        MOBILE OPTIMIZATIONS
        ============================================== */
        @media (max-width: 768px) {
            .dashboard-fade {
                transform: translateY(20px) scale(0.98);
            }

            .card-enhanced:hover {
                transform: translateY(-2px);
                box-shadow: 0 15px 30px rgba(0, 107, 161, 0.1);
            }

            .nav-card:hover {
                transform: translateY(-4px) scale(1.01);
            }

            .sidebar-link:hover {
                transform: translateY(-1px);
            }
        }

        /* ==============================================
        ACCESSIBILITY ENHANCEMENTS
        ============================================== */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }

        /* ==============================================
        FOCUS STATES
        ============================================== */
        .sidebar-link:focus,
        .btn-enhanced:focus,
        .nav-card:focus {
            outline: 2px solid #3b82f6;
            outline-offset: 2px;
        }

        /* ==============================================
        HIGH CONTRAST MODE
        ============================================== */
        @media (prefers-contrast: high) {
            .glass-card {
                background: rgba(255, 255, 255, 0.95);
                border: 2px solid #000;
            }
        }
    </style>

    {{-- Scripts --}}
    <script>
        // DOM Ready Event
        document.addEventListener('DOMContentLoaded', () => {
            initializePage();
            setTimeout(() => {
                document.querySelectorAll('.dashboard-fade').forEach(el => {
                    el.classList.add('animated');
                });
            }, 200);
        });

        // Initialize Page Functions
        function initializePage() {
            updateClock();
            updateDateTime();
            updateDateTimeCards();
            updateLastUpdated();
        }

        // Update Welcome Card Clock
        function updateClock() {
            const now = new Date();
            const hari = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
            const bulan = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
            const fullTime = `${hari[now.getDay()]}, ${now.getDate()} ${bulan[now.getMonth()]} ${now.getFullYear()}<br>${now.getHours().toString().padStart(2, '0')}:${now.getMinutes().toString().padStart(2, '0')}:${now.getSeconds().toString().padStart(2, '0')}`;

            const clockElement = document.getElementById("clock");
            if (clockElement) {
                clockElement.innerHTML = fullTime;
            }
        }

        // Update Sidebar Date Time
        function updateDateTime() {
            const now = new Date();
            const dateOptions = { weekday: 'short', month: 'short', day: 'numeric' };
            const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false };

            const dateElement = document.getElementById('current-date');
            const timeElement = document.getElementById('current-time');

            if (dateElement) {
                dateElement.textContent = now.toLocaleDateString('en-US', dateOptions);
            }
            if (timeElement) {
                timeElement.textContent = now.toLocaleTimeString('en-US', timeOptions);
            }
        }

        // Update Info Cards
        function updateDateTimeCards() {
            const now = new Date();
            const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false };

            const dateCardElement = document.getElementById('current-date-card');
            const timeCardElement = document.getElementById('current-time-card');

            if (dateCardElement) {
                dateCardElement.textContent = now.toLocaleDateString('en-US', dateOptions);
            }
            if (timeCardElement) {
                timeCardElement.textContent = now.toLocaleTimeString('en-US', timeOptions);
            }
        }

        // Update Last Updated Time
        function updateLastUpdated() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID');
            const lastUpdatedElement = document.getElementById("last-updated");

            if (lastUpdatedElement) {
                lastUpdatedElement.textContent = timeString;
            }
        }

        // Set Intervals
        setInterval(() => {
            updateClock();
            updateDateTime();
            updateDateTimeCards();
        }, 1000);

        setInterval(updateLastUpdated, 30000); // Update every 30 seconds

        // Show Loader Function
        function showLoader(message = 'Loading Dashboard...') {
            const loaderText = document.getElementById('loader-text');
            const pageLoader = document.getElementById('page-loader');

            if (loaderText) loaderText.textContent = message;
            if (pageLoader) {
                pageLoader.classList.remove('hidden');
                pageLoader.classList.add('flex');
            }
        }

        // Navigation Function
        function fadeNavigate(url, isExternal = false) {
            showLoader(isExternal ? 'Opening external link...' : 'Navigating...');

            setTimeout(() => {
                if (isExternal) {
                    window.open(url, '_blank');
                    const pageLoader = document.getElementById('page-loader');
                    if (pageLoader) {
                        pageLoader.classList.add('hidden');
                        pageLoader.classList.remove('flex');
                    }
                } else {
                    window.location.href = url;
                }
            }, 600);
        }

        // Toggle Sidebar Function
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            if (sidebar) {
                sidebar.classList.toggle('open');
            }
        }

        // Enhanced loading animations for links without onclick
        document.querySelectorAll('a[href]:not([onclick])').forEach(link => {
            link.addEventListener('click', function(e) {
                if (!this.href || this.href.startsWith('#') || this.target === '_blank') return;
                e.preventDefault();
                fadeNavigate(this.href);
            });
        });
    </script>
</x-app-layout>
