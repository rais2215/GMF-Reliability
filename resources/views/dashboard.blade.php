<x-app-layout>
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mx-6 my-4">
            {{ session('error') }}
        </div>
    @endif

    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            Reliability Dashboard
        </h2>
    </x-slot>

    <div class="flex min-h-screen bg-gray-100">
        <!-- Sidebar -->
       <aside class="w-64 bg-white shadow-md p-4 hidden sm:block">
    <h3 class="text-lg font-bold text-gray-700 mb-4">Menu</h3>
    <ul class="space-y-2">
        <li>
            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                🏠 Dashboard
            </x-nav-link>
        </li>
        <li>
            <x-nav-link :href="route('report')" :active="request()->routeIs('report')">
                📊 Report
            </x-nav-link>
        </li>
        @if(Auth::user()->Position === 'Admin')
            <li>
                <x-nav-link :href="route('user-setting')" :active="request()->routeIs('user-setting')">
                    ⚙ User Setting
                </x-nav-link>
            </li>
        @endif
        <li>
            <x-nav-link :href="'https://dashboard-reliability.gmf-aeroasia.co.id/'" target="_blank">
                🛠 Techlog Delay
            </x-nav-link>
        </li>
    </ul>
</aside>



        <!-- Main Content -->
        <main class="flex-1 p-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
                <!-- Card 1 -->
                <div class="bg-white p-4 rounded-xl shadow">
                    <h4 class="text-sm text-gray-500">Total Penerbangan</h4>
                    <p class="text-2xl font-semibold text-blue-600">124</p>
                </div>
                <!-- Card 2 -->
                <div class="bg-white p-4 rounded-xl shadow">
                    <h4 class="text-sm text-gray-500">Delay Hari Ini</h4>
                    <p class="text-2xl font-semibold text-red-500">3</p>
                </div>
                <!-- Card 3 -->
                <div class="bg-white p-4 rounded-xl shadow">
                    <h4 class="text-sm text-gray-500">Dispatch Rate</h4>
                    <p class="text-2xl font-semibold text-green-600">98.5%</p>
                </div>
            </div>

            <!-- Embedded Report -->
            <div class="bg-white p-4 rounded-xl shadow">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Power BI Report</h3>
                <div class="w-full h-[600px]">
                    <iframe id="reportContainer"
                        src="https://app.powerbi.com/view?r=eyJrIjoiNWYxNjYxZGItZTVjZS00YmQxLWIxMTctNjU3NDU0YmM0ODI5IiwidCI6ImIxNTAxOTBhLTE2ZjMtNGZiYS04YmY2LTNhNjIwYWI3NjA3OSIsImMiOjEwfQ%3D%3D"
                        class="w-full h-full border-none"
                        allowfullscreen></iframe>
                </div>
            </div>
        </main>
    </div>
</x-app-layout>