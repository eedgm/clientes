@php $editing = isset($receipt) @endphp

<div class="flex flex-wrap">
    <x-inputs.group class="w-full">
        <x-inputs.date
            name="real_date"
            label="Real Date"
            value="{{ old('real_date', ($editing ? optional($receipt->real_date)->format('Y-m-d') : '')) }}"
            max="255"
            required
        ></x-inputs.date>
    </x-inputs.group>

    <x-inputs.group class="w-full lg:w-6/12">
        <x-inputs.number
            name="number"
            label="Number"
            :value="old('number', ($editing ? $receipt->number : ''))"
            max="255"
            placeholder="Number"
            required
        ></x-inputs.number>
    </x-inputs.group>

    <x-inputs.group class="w-full lg:w-6/12">
        <x-inputs.number
            name="manual_value"
            label="Manual Value"
            :value="old('manual_value', ($editing ? $receipt->manual_value : ''))"
            max="255"
            placeholder="Manual Value"
            required
        ></x-inputs.number>
    </x-inputs.group>

    <x-inputs.group class="w-full">
        <x-inputs.textarea
            name="description"
            label="Description"
            maxlength="255"
            >{{ old('description', ($editing ? $receipt->description : ''))
            }}</x-inputs.textarea
        >
    </x-inputs.group>

    <x-inputs.group class="w-full lg:w-3/12">
        <x-inputs.select name="client_id" label="Client" required>
            @php $selected = old('client_id', ($editing ? $receipt->client_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Client</option>
            @foreach($clients as $value => $label)
            <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }} >{{ $label }}</option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="w-full lg:w-3/12">
        <x-inputs.checkbox
            name="charged"
            label="Charged"
            :checked="old('charged', ($editing ? $receipt->charged : 0))"
        ></x-inputs.checkbox>
    </x-inputs.group>

    <x-inputs.group class="w-full lg:w-3/12">
        <x-inputs.text
            name="reference_charged"
            label="Reference Charged"
            :value="old('reference_charged', ($editing ? $receipt->reference_charged : ''))"
            maxlength="255"
            placeholder="Reference Charged"
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="w-full lg:w-3/12">
        <x-inputs.datetime
            name="date_charged"
            label="Date Charged"
            value="{{ old('date_charged', ($editing ? optional($receipt->date_charged)->format('Y-m-d\TH:i:s') : '')) }}"
            max="255"
        ></x-inputs.datetime>
    </x-inputs.group>
</div>
