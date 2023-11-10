@props([
    'name',
    'label',
    'value'
])

<x-inputs.basic type="text" :name="$name" label="{{ $label ?? ''}}" :value="$value ?? ''" :attributes="$attributes"></x-inputs.basic>
<input class="w-full" type="range" wire:model="{{ $attributes['wire:model'] }}" min="0" max="100" step="5">

