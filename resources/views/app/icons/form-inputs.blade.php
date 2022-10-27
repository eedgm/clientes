@php $editing = isset($icon) @endphp

<div class="flex flex-wrap">
    <x-inputs.group class="w-full">
        <x-inputs.text
            name="name"
            label="Name"
            :value="old('name', ($editing ? $icon->name : ''))"
            maxlength="255"
            placeholder="Name"
            required
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="w-full">
        <x-inputs.text
            name="icon"
            label="Icon"
            :value="old('icon', ($editing ? $icon->icon : ''))"
            maxlength="255"
            placeholder="Icon"
            required
        ></x-inputs.text>
    </x-inputs.group>
</div>
