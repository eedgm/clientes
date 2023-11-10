@props(['active', 'icon'])

@php
$classes = ($active ?? false)
            ? 'relative flex items-center w-full px-2 py-1 rounded text-white bg-gray-700'
            : 'relative flex items-center w-full px-2 py-1 rounded hover:text-white hover:bg-gray-700';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}
    class="flex items-center h-12 px-4 overflow-hidden bg-gray-900 hover:text-gray-100 hover:bg-opacity-50 focus:outline-none focus:text-gray-100 focus:bg-opacity-50"
    >
    <i class="bx {{ $icon }} text-lg md:text-3xl h-6 w-4 md:h-10 md:w-10 flex-shrink-0"></i>
    <span class="ml-2 text-lg font-medium duration-300 ease-in-out" :class="isSidebarExpanded ? 'opacity-100' : 'opacity-0 group-hover:opacity-100'">{{ $slot }}</span>
</a>
