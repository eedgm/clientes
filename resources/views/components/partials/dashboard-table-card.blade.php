@props([
    'bodyClasses' => 'flex-auto p-6',
])

<div {{ $attributes->merge(['class' => 'relative flex flex-col rounded-lg bg-white break-words shadow-xl']) }}>
    <div class="{{ $bodyClasses }}">

        @if(isset($title))
        <h4 class="pt-6 pl-6 mb-3 text-lg font-bold">
            {{ $title }}
        </h4>
        @endif

        @if(isset($subtitle))
        <h5 class="text-sm text-gray-600">
            {{ $subtitle }}
        </h5>
        @endif

        {{ $slot }}
    </div>
</div>
