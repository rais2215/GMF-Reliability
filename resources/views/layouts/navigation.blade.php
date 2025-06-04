<!-- Skeleton Loader -->
<div id="loader" class="fixed inset-0 bg-white bg-opacity-90 flex flex-col gap-4 items-center justify-center z-50" style="display: none;">
    <div class="w-1/2 h-6 bg-gray-300 rounded animate-pulse"></div>
    <div class="w-1/3 h-6 bg-gray-300 rounded animate-pulse"></div>
    <div class="w-1/4 h-6 bg-gray-300 rounded animate-pulse"></div>
</div>

<nav x-data="{ open: false }" style="background-color: #112955; border-bottom: 10px solid #112955;">
    <!-- Primary Navigation Menu -->
    <div class="mx-10 px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center" style="padding-top: 8px;">
                    <img class="h-14 w-auto" src="{{ asset('images/gmfwhite.png') }}" alt="logo">
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @if(Auth::user()->Position === 'Admin')
                        <x-nav-link :href="route('user-setting')" :active="request()->routeIs('user-setting')" style="color: #fff;">
                            User Setting
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md" style="color: #fff; background-color: #112955;">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" style="color: #fff;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link href="{{ route('profile.edit') }}" onclick="event.preventDefault(); showLoaderAndGo('{{ route('profile.edit') }}');" style="color: #112955;">
                            Profile
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" onclick="showLoader()" class="w-full text-left px-4 py-2 text-sm" style="color: #112955;">
                                Log Out
                            </button>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md" style="color: #fff;">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden" style="background-color: #112955;">
        <div class="pt-2 pb-3 space-y-1">
            @if(Auth::user()->Position === 'Admin')
                <x-responsive-nav-link :href="route('user-setting')" :active="request()->routeIs('user-setting')" style="color: #fff;">
                    User Setting
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t" style="border-color: #112955;">
            <div class="px-4">
                <div class="font-medium text-base" style="color: #fff;">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm" style="color: #fff;">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link href="{{ route('profile.edit') }}" onclick="event.preventDefault(); showLoaderAndGo('{{ route('profile.edit') }}');" style="color: #112955;">
                    Profile
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" onclick="showLoader()" class="w-full text-left px-4 py-2 text-sm" style="color: #112955;">
                        Log Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

<!-- JavaScript -->
<script>
    function showLoader() {
        document.getElementById('loader').style.display = 'flex';
    }

    function showLoaderAndGo(url) {
        showLoader();
        setTimeout(function () {
            window.location.href = url;
        }, 300); // bisa disesuaikan dengan kebutuhan UX
    }
</script>