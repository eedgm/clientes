<header class="flex items-center justify-between px-6 py-2 bg-white border-b-4 border-green-600">
    <div class="flex items-center justify-center bg-white">
        <div class="flex items-center ">
            <a href="{{ route('dashboard') }}">
                <img src="/storage/clients.svg" alt="" class="w-32 h-auto">
            </a>
        </div>
        <button @click="sidebarOpen = true" class="ml-5 text-gray-500 focus:outline-none">
            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4 6H20M4 12H20M4 18H11" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round"></path>
            </svg>
        </button>

        <a class="ml-5 text-blue-700 hover:underline" href="{{ route('clients.index') }}">
            Clients
        </a>
    </div>

    <div class="flex items-center">

        {{-- <div x-data="{ dropdownOpen: false }" class="relative">
            <button @click="dropdownOpen = ! dropdownOpen"
                class="relative block w-8 h-8 overflow-hidden rounded-full shadow focus:outline-none">
                <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>

            <div x-show="dropdownOpen" @click="dropdownOpen = false" class="fixed inset-0 z-10 w-full h-full"
                style="display: none;"></div>

            <div x-show="dropdownOpen"
                class="absolute right-0 z-10 w-48 mt-2 overflow-hidden bg-white rounded-md shadow-xl"
                style="display: none;">
                <x-jet-dropdown-link href="{{ route('profile.show') }}">
                    {{ __('Profile') }}
                </x-jet-dropdown-link>
            </div>
        </div> --}}
        @if(Auth::check())
            <div class="relative text-sm">
                Welcome back: <a class="text-blue-500 hover:underline hover:text-blue-800" href="{{ route('profile.show') }}">{{ Auth::user()->name }}</a>
            </div>
        @endif
        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <x-jet-dropdown-link href="{{ route('logout') }}"
                        onclick="event.preventDefault();
                            this.closest('form').submit();">
                {{ __('Log Out') }}
            </x-jet-dropdown-link>
        </form>
    </div>

</header>
