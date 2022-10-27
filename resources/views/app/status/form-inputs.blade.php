@php $editing = isset($statu) @endphp

<div class="flex flex-wrap">
    <x-inputs.group class="w-full">
        <x-inputs.text
            name="name"
            label="Name"
            :value="old('name', ($editing ? $statu->name : ''))"
            maxlength="255"
            placeholder="Name"
            required
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="w-full">
        <x-inputs.number
            name="limit"
            label="Limit"
            :value="old('limit', ($editing ? $statu->limit : ''))"
            max="255"
            placeholder="Limit"
            required
        ></x-inputs.number>
    </x-inputs.group>

    <x-inputs.group class="w-full">
        <x-inputs.select name="color_id" label="Color" required>
            @php $selected = old('color_id', ($editing ? $statu->color_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Color</option>
            @foreach($colors as $value => $label)
            <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }} >{{ $label }}</option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="w-full">
        <x-inputs.select name="icon_id" label="Icon" required>
            @php $selected = old('icon_id', ($editing ? $statu->icon_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Icon</option>
            @foreach($icons as $value => $label)
            <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }} >{{ $label }}</option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>
</div>
