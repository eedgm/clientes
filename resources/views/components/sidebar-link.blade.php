@props(['active', 'icon'])

@php
$classes = ($active ?? false)
            ? 'relative flex items-center w-full px-2 py-1 rounded text-white bg-gray-700'
            : 'relative flex items-center w-full px-2 py-1 rounded hover:text-white hover:bg-gray-700';
@endphp

<li class="text-sm text-gray-500 ">
    <a
        {{ $attributes->merge(['class' => $classes]) }}>
        <div class="pr-2">
            <i class="bx {{ $icon }} text-lg"></i>
        </div>
        <div class="text-lg">{{ $slot }}</div>
    </a>
</li>
