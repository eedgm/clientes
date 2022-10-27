@props(['active', 'icon'])

@php
$classes = ($active ?? false)
            ? 'flex items-center px-6 py-2 mt-4 text-white bg-white bg-opacity-25'
            : 'flex items-center px-6 py-2 mt-4 text-white hover:bg-white hover:bg-opacity-25 hover:text-gray-900';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    <i class="bx {{ $icon }}"></i>
    <span class="mx-3">{{ $slot }}</span>
</a>
