<div class="flex flex-col items-center min-h-screen pt-6 bg-gray-800 sm:justify-center sm:pt-0">
    <div>
        {{ $logo }}
    </div>

    <div class="w-full px-6 py-8 mt-6 overflow-hidden text-white bg-gray-700 shadow-md sm:max-w-md sm:rounded-lg">
        {{ $slot }}
    </div>
</div>
