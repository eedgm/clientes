<!-- Top navbar -->
<nav class="sticky top-0 z-50 w-full text-black bg-gray-800 shadow-xl" x-data="{ mobilemenue: false, toggleMenu: false }">
    <div class="mx-auto ">
        <div class="flex items-stretch justify-between h-16">

            <div class="flex items-center md:hidden">
                <div class="flex mr-2" x-data>
                    <!-- Mobile menu button -->
                    <button type="button" @click="$dispatch('togglesidebar')"
                        class="inline-flex items-center justify-center p-2 text-gray-400 bg-gray-800 rounded-md hover:text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-white"
                        aria-controls="mobile-menu" aria-expanded="false">
                        <span class="sr-only">Open main menu</span>

                        <svg class="block w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>

                        <svg class="hidden w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="flex items-center">
                <div class="flex-shrink-0 md:hidden">


                </div>

                <div class="mr-5" x-show="!toggleMenu">
                    <a href="{{ route('dashboard') }}">
                        <img src="/storage/clientes.svg" alt="" class="h-auto w-44">
                    </a>
                </div>

                <!-- toggel sidebar -->
                <div class="hidden text-white cursor-pointer md:block" x-on:click="$dispatch('togglesidebar'); toggleMenu = ! toggleMenu">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h7" />
                    </svg>
                </div>


                <div class="hidden lg:block">
                    <!-- Search -->
                    <form action="" class="app-search" method="GET">
                        <div class="relative group ">
                            <input type="text"
                                class="form-input rounded-md bg-gray-700 text-sm text-gray-300 pl-10 py-1.5 ml-5 border-transparent border-none outline-none focus:ring-0 focus:text-white transition-all duration-300 ease-in-out focus:w-60 w-48"
                                placeholder="Search..." autocomplete="off">
                            <span
                                class="absolute text-gray-400 transition-all duration-300 ease-in-out left-44 bottom-2 group-focus-within:left-8">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </span>
                        </div>
                    </form>
                </div>
            </div>
            <div class="items-stretch hidden md:flex">
                <!-- Profile Menu DT -->
                <div class="flex ml-4 md:ml-6 ">
                    <div class="relative flex items-center justify-center mr-4">
                        <div class="block p-1 text-gray-400 bg-gray-700 rounded-full hover:text-white">
                            <span class="sr-only">View notifications</span>
                            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </div>
                    </div>

                    <!-- Profile dropdown -->
                    <div class="relative px-4 text-sm text-gray-400 bg-gray-700 cursor-pointer hover:text-white"
                        x-data="{open: false}">
                        <div class="flex items-center min-h-full" @click="open = !open">

                            @if(Auth::check())
                                <div class="flex flex-col ml-4">
                                    {{ Auth::user()->name }}
                                </div>
                            @endif

                            {{-- <div class="flex items-center max-w-xs text-sm bg-gray-800 rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-white"
                                id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                                <span class="sr-only">Open user menu</span>
                                <img class="w-8 h-8 rounded-full"
                                    src="https://assets.codepen.io/3321250/internal/avatars/users/default.png?fit=crop&format=auto&height=512&version=1646800353&width=512"
                                    alt="">
                            </div> --}}
                        </div>

                        <div x-show="open" @click.away="open = false"
                            class="absolute right-0 min-w-full py-1 mt-0 origin-top-right bg-white shadow rounded-b-md ring-1 ring-black ring-opacity-5 focus:outline-none"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95" role="menu"
                            aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
                            <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                role="menuitem" tabindex="-1" id="user-menu-item-0">Profile</a>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <x-jet-dropdown-link href="{{ route('logout') }}"
                                            onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-jet-dropdown-link>
                            </form>
                        </div>
                    </div>

                </div>
            </div>


            <div class="flex mr-2 md:hidden">
                <!-- Mobile menu button -->
                <button type="button" @click="mobilemenue = !mobilemenue"
                    class="inline-flex items-center justify-center p-2 text-gray-400 bg-gray-800 rounded-md hover:text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-white"
                    aria-controls="mobile-menu" aria-expanded="false">
                    <span class="sr-only">Open main menu</span>

                    <svg class="block w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg class="hidden w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu, show/hide based on menu state. -->
    <div class="absolute w-full bg-gray-800 md:hidden" id="mobile-menu" x-show="mobilemenue"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95" @click.away="mobilemenue = false

        ">

        <div class="pt-4 pb-3 border-t border-gray-700">
            <!-- profile menue for mobile -->
            <div class="flex items-center px-5">
                {{-- <div class="flex-shrink-0">
                    <img class="w-10 h-10 rounded-full"
                        src="https://assets.codepen.io/3321250/internal/avatars/users/default.png?fit=crop&format=auto&height=512&version=1646800353&width=512"
                        alt="">
                </div> --}}
                <div class="ml-3">
                    <div class="text-base font-medium leading-none text-white">
                        @if(Auth::check())
                                <div class="flex flex-col ml-4">
                                    {{ Auth::user()->name }}
                                </div>
                            @endif
                    </div>
                </div>
                <button type="button"
                    class="flex-shrink-0 p-1 ml-auto text-gray-400 bg-gray-800 rounded-full hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-white">
                    <span class="sr-only">View notifications</span>
                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </button>

            </div>
            <div class="px-2 mt-3 space-y-1">
                <a href="{{ route('profile.show') }}"
                    class="block px-4 py-2 text-base font-medium text-gray-400 rounded-md hover:text-white hover:bg-gray-700">Profile</a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-jet-dropdown-link href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                                    this.closest('form').submit();" class="text-gray-400">
                        {{ __('Log Out') }}
                    </x-jet-dropdown-link>
                </form>
            </div>
        </div>
    </div>
</nav>
<!-- End Top navbar -->
