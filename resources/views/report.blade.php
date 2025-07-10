<x-app-layout>
    <!-- Page Loader: 3 Bar Loader - Fixed to match register.blade.php -->
    <div id="page-loader" class="fixed inset-0 z-50 hidden flex-col items-center justify-center bg-[#112955]/90 backdrop-blur-lg transition-all duration-500">
        <div class="glass-card rounded-3xl shadow-2xl p-12 border border-white/20 max-w-sm w-full mx-4 bg-white/10 backdrop-blur-xl">

            <div class="text-center space-y-4">
                <span id="loader-text" class="text-xl font-semibold text-white block">Redirecting page...</span>
                <p class="text-sm text-gray-300">Please wait while we load the content</p>
            </div>

            <div class="mt-6 w-full h-2 bg-white/20 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-[#7EBB1A] to-[#8DC63F] rounded-full progress-bar"></div>
            </div>
        </div>
    </div>

    <!-- Loader Animation Style & Responsive Header -->
    <style>
        /* ==============================================
           LOADER ANIMATIONS - Fixed to match register.blade.php
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

        /* Legacy loader bar animation for backward compatibility */
        @keyframes bar-bounce {
            0%, 100% { transform: scaleY(0.4) translateY(0); opacity: 0.6; }
            25% { transform: scaleY(0.8) translateY(-8px); opacity: 0.8; }
            50% { transform: scaleY(1.2) translateY(-16px); opacity: 1; }
            75% { transform: scaleY(0.8) translateY(-8px); opacity: 0.8; }
        }
        .animate-loader-bar { animation: loader-bounce 1.4s infinite ease-in-out; }
        .delay-150 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.4s; }

        /* Tambahan animasi fade-in-up agar selaras dengan dashboard */
        @keyframes fade-in-up {
            0% { opacity: 0; transform: translateY(32px);}
            100% { opacity: 1; transform: translateY(0);}
        }
        .animate-fade-in-up {
            animation: fade-in-up 0.7s cubic-bezier(.4,2,.6,1) both;
        }
        .delay-100 { animation-delay: 0.1s !important; }
        .delay-200 { animation-delay: 0.2s !important; }
        .delay-300 { animation-delay: 0.3s !important; }
        .delay-400 { animation-delay: 0.4s !important; }
        .delay-500 { animation-delay: 0.5s !important; }
        .delay-600 { animation-delay: 0.6s !important; }
        .delay-700 { animation-delay: 0.7s !important; }

        /* Responsive Header */
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

    <!-- Header (Responsive & Animated) -->
    <x-slot name="header">
        <div class="flex items-center justify-between px-6 py-3 bg-white border-b animate-fade-in-up delay-100 header-responsive"
            style="border-color:#006ba1; border-bottom-width:1.5px; border-style:solid; border-bottom-left-radius:1rem; border-bottom-right-radius:1rem; box-shadow:0 1px 4px 0 rgba(0,107,161,0.07);">
            <div class="flex items-center gap-3 flex-wrap">
                <!-- Back Button (Minimal) -->
                <button id="back-button"
                    class="flex items-center gap-2 px-3 py-1 rounded"
                    style="background:#e3f2fd; color:#006ba1;"
                    onmouseover="this.style.background='#b3e0fc';this.style.color='#004466';"
                    onmouseout="this.style.background='#e3f2fd';this.style.color='#006ba1';"
                    title="Back to Dashboard">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    <span class="font-medium">Back</span>
                </button>
                <!-- Title -->
                <h2 class="font-bold text-2xl" style="color:#006ba1; letter-spacing:0.05em; display:flex; align-items:center; gap:0.5rem;">
                    <span class="inline-block w-1 h-6 rounded-full" style="background:#006ba1;"></span>
                    Report
                </h2>
            </div>
            <span class="text-base font-semibold self-center lg:self-auto" style="color:#006ba1;">GMF Reliability</span>
        </div>
    </x-slot>

    <div class="flex flex-col lg:flex-row min-h-screen mx-auto py-8 px-4 sm:px-8 gap-8 animate-fade-in-up delay-200" style="background:linear-gradient(135deg,#f8fafc 0%,#e3f2fd 50%,#f4f9ef 100%);">
        <!-- Sidebar -->
        <aside class="w-full lg:w-1/5 shadow-2xl border rounded-2xl p-6 flex flex-col gap-6 animate-fade-in-up delay-300" style="background:linear-gradient(to bottom,white,#e3f2fd,#f4f9ef);border-color:#006ba1;">
            <div class="mb-4">
                <a href="/report" class="font-bold border-b-2 w-full py-2 block text-lg"
                   style="border-color:#006ba1;color:#006ba1;"><strong>All Report</strong></a>
            </div>
            <ul class="space-y-2">
                <li>
                    <a href="#" class="flex items-center px-3 py-2 rounded-lg transition bg-blue-50 hover:bg-blue-100 text-blue-600 hover:text-blue-900 sidebar-item focus:outline-none focus:ring-2 focus:ring-blue-400 animate-fade-in-up delay-400"
                       data-url="{{ route('report.aos.index') }}">
                        <span class="mr-2 text-xl">✈</span> <strong>Aircraft Operation Summary</strong>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center px-3 py-2 rounded-lg transition bg-blue-50 hover:bg-blue-100 text-blue-600 hover:text-blue-900 sidebar-item focus:outline-none focus:ring-2 focus:ring-blue-400 animate-fade-in-up delay-500"
                       data-url="{{ route('report.pilot.index') }}">
                        <span class="mr-2 text-xl">✈</span> <strong>Pilot Report And Technical Delay</strong>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center px-3 py-2 rounded-lg transition bg-blue-50 hover:bg-blue-100 text-blue-600 hover:text-blue-900 sidebar-item focus:outline-none focus:ring-2 focus:ring-blue-400 animate-fade-in-up delay-600"
                       data-url="{{ route('report.cumulative.index') }}">
                        <span class="mr-2 text-xl">✈</span> <strong>Cumulative Flight Hours and Take Off</strong>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center px-3 py-2 rounded-lg transition bg-blue-50 hover:bg-blue-100 text-blue-600 hover:text-blue-900 sidebar-item focus:outline-none focus:ring-2 focus:ring-blue-400 animate-fade-in-up delay-700"
                       data-url="{{ route('report.etops.index') }}">
                        <span class="mr-2 text-xl">✈</span> <strong>Etops Reliability Report</strong>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center px-3 py-2 rounded-lg transition bg-blue-50 hover:bg-blue-100 text-blue-600 hover:text-blue-900 sidebar-item focus:outline-none focus:ring-2 focus:ring-blue-400 animate-fade-in-up delay-700">
                        <span class="mr-2 text-xl">✈</span> <strong>Etops Event</strong>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center px-3 py-2 rounded-lg transition bg-blue-50 hover:bg-blue-100 text-blue-600 hover:text-blue-900 sidebar-item focus:outline-none focus:ring-2 focus:ring-blue-400 animate-fade-in-up delay-700">
                        <span class="mr-2 text-xl">✈</span> <strong>Reliability Graph</strong>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center px-3 py-2 rounded-lg transition bg-blue-50 hover:bg-blue-100 text-blue-600 hover:text-blue-900 sidebar-item focus:outline-none focus:ring-2 focus:ring-blue-400 animate-fade-in-up delay-700">
                        <span class="mr-2 text-xl">✈</span> <strong>Engine Operation Summary</strong>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center px-3 py-2 rounded-lg transition bg-blue-50 hover:bg-blue-100 text-blue-600 hover:text-blue-900 sidebar-item focus:outline-none focus:ring-2 focus:ring-blue-400 animate-fade-in-up delay-700">
                        <span class="mr-2 text-xl">✈</span> <strong>Engine Removal & Shutdown</strong>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center px-3 py-2 rounded-lg transition bg-blue-50 hover:bg-blue-100 text-blue-600 hover:text-blue-900 sidebar-item focus:outline-none focus:ring-2 focus:ring-blue-400 animate-fade-in-up delay-700">
                        <span class="mr-2 text-xl">✈</span> <strong>Weekly Reliability Report</strong>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center px-3 py-2 rounded-lg transition bg-blue-50 hover:bg-blue-100 text-blue-600 hover:text-blue-900 sidebar-item focus:outline-none focus:ring-2 focus:ring-blue-400 animate-fade-in-up delay-700">
                        <span class="mr-2 text-xl">✈</span> <strong>Summary Report</strong>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center px-3 py-2 rounded-lg transition bg-blue-50 hover:bg-blue-100 text-blue-600 hover:text-blue-900 sidebar-item focus:outline-none focus:ring-2 focus:ring-blue-400 animate-fade-in-up delay-700">
                        <span class="mr-2 text-xl">✈</span> <strong>Graph ATA Pilot</strong>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center px-3 py-2 rounded-lg transition bg-blue-50 hover:bg-blue-100 text-blue-600 hover:text-blue-900 sidebar-item focus:outline-none focus:ring-2 focus:ring-blue-400 animate-fade-in-up delay-700">
                        <span class="mr-2 text-xl">✈</span> <strong>Graph ATA Delay</strong>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center px-3 py-2 rounded-lg transition bg-blue-50 hover:bg-blue-100 text-blue-600 hover:text-blue-900 sidebar-item focus:outline-none focus:ring-2 focus:ring-blue-400 animate-fade-in-up delay-700">
                        <span class="mr-2 text-xl">✈</span> <strong>APU Operation Summary</strong>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center px-3 py-2 rounded-lg transition bg-blue-50 hover:bg-blue-100 text-blue-600 hover:text-blue-900 sidebar-item focus:outline-none focus:ring-2 focus:ring-blue-400 animate-fade-in-up delay-700">
                        <span class="mr-2 text-xl">✈</span> <strong>APU Removal</strong>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center px-3 py-2 rounded-lg transition bg-blue-50 hover:bg-blue-100 text-blue-600 hover:text-blue-900 sidebar-item focus:outline-none focus:ring-2 focus:ring-blue-400 animate-fade-in-up delay-700">
                        <span class="mr-2 text-xl">✈</span> <strong>Cabin Reliability Report</strong>
                    </a>
                </li>
                <li class="my-4 animate-fade-in-up delay-700">
                    <hr class="border-t-2 border-gray-300 font-bold transition-all duration-700 ease-in-out">
                </li>
                <li>
                    <a href="#" class="flex items-center px-3 py-2 rounded-lg transition bg-red-50 hover:bg-red-100 text-red-700 hover:text-red-900 sidebar-item focus:outline-none focus:ring-2 focus:ring-red-400 animate-fade-in-up delay-400"
                       data-url="{{ route('report.combined.index') }}">
                        <i data-lucide="download" class="mr-2 w-5 h-5"></i> <strong>Export AOS & Pilot Report (PDF)</strong>
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Main Content Area -->
        <main
            class="flex-1 p-8 rounded-2xl shadow-xl border animate-fade-in-up delay-400"
            style="background:linear-gradient(135deg,#6ba539 0%,#8bc34a 100%);border-color:#6ba539;"
            id="main-content">
            <h1 class="text-3xl font-bold mb-4 animate-fade-in-up delay-500" style="color:#ffffff;">Main Content Area</h1>
            <p class="text-lg animate-fade-in-up delay-600" style="color:#222;">This is where the main content will go. You can place your reports, data, or any other content here.</p>
        </main>
    </div>

    <!-- Script -->
    <script src="{{ asset('js/report.js') }}"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
            const loader = document.getElementById('page-loader');

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
