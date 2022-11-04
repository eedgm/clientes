<x-guest-layout>
    <x-jet-authentication-card >
        <x-slot name="logo">
            <img src="/storage/clientes.svg" alt="" class="w-48 h-auto">
        </x-slot>

        <x-jet-validation-errors class="mb-4" />

        @if (session('status'))
            <div class="mb-4 text-sm font-medium text-green-600">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <h1 class="mb-5 text-xl font-bold leading-tight tracking-tight text-white md:text-2xl">
                Sign in to your account
            </h1>

            <div>
                <x-jet-label for="email" value="{{ __('Email') }}" class="mb-2 text-sm font-medium text-white" />
                <x-jet-input id="email" placeholder="email@email.com"
                    class="border sm:text-sm rounded-lg block w-full p-2.5 bg-gray-600 border-gray-500 placeholder-gray-400 text-white focus:ring-blue-500 focus:border-blue-500"
                    type="email" name="email" :value="old('email')" required autofocus />
            </div>

            <div class="mt-4">
                <x-jet-label for="password" value="{{ __('Password') }}" class="mb-2 text-sm font-medium text-white" />
                <x-jet-input id="password" placeholder="Password"
                    class="border sm:text-sm rounded-lg block w-full p-2.5 bg-gray-600 border-gray-500 placeholder-gray-400 text-white focus:ring-blue-500 focus:border-blue-500"
                    type="password" name="password" required autocomplete="current-password" />
            </div>

            <div class="block mt-4">
                <label for="remember_me" class="flex items-center ">
                    <x-jet-checkbox id="remember_me" name="remember" />
                    <span class="ml-2 text-sm text-white">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                @if (Route::has('password.request'))
                    <a class="text-sm text-white underline hover:text-gray-900" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif

                <x-jet-button class="ml-4 bg-blue-600 hover:bg-blue-800">
                    {{ __('Log in') }}
                </x-jet-button>
            </div>
        </form>
    </x-jet-authentication-card>
</x-guest-layout>
