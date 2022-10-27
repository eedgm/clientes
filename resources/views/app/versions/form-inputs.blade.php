@php $editing = isset($version) @endphp

<div class="flex flex-wrap">
    <x-inputs.group class="w-full lg:w-4/12">
        <x-inputs.select name="proposal_id" label="Proposal" required>
            @php $selected = old('proposal_id', ($editing ? $version->proposal_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Proposal</option>
            @foreach($proposals as $value => $label)
            <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }} >{{ $label }}</option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="w-full lg:w-4/12">
        <x-inputs.select name="user_id" label="Client" required>
            @php $selected = old('user_id', ($editing ? $version->user_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the User</option>
            @foreach($users as $value => $label)
            <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }} >{{ $label }}</option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="w-full lg:w-4/12">
        <x-inputs.partials.label
            name="attachment"
            label="Attachment"
        ></x-inputs.partials.label
        ><br />

        <input
            type="file"
            name="attachment"
            id="attachment"
            class="form-control-file"
        />

        @if($editing && $version->attachment)
        <div class="mt-2">
            <a href="{{ \Storage::url($version->attachment) }}" target="_blank"
                ><i class="icon ion-md-download"></i>&nbsp;Download</a
            >
        </div>
        @endif @error('attachment') @include('components.inputs.partials.error')
        @enderror
    </x-inputs.group>

    <x-inputs.group class="w-full lg:w-4/12">
        <x-inputs.number
            name="total"
            label="Total"
            :value="old('total', ($editing ? $version->total : ''))"
            max="255"
            step="0.01"
            placeholder="Total"
            required
        ></x-inputs.number>
    </x-inputs.group>

    <x-inputs.group class="w-full lg:w-4/12">
        <x-inputs.date
            name="time"
            label="Time"
            value="{{ old('time', ($editing ? optional($version->time)->format('Y-m-d') : '')) }}"
            max="255"
            required
        ></x-inputs.date>
    </x-inputs.group>

    <x-inputs.group class="w-full lg:w-4/12">
        <x-inputs.number
            name="cost_per_hour"
            label="Cost Per Hour"
            :value="old('cost_per_hour', ($editing ? $version->cost_per_hour : ''))"
            max="255"
            step="0.01"
            placeholder="Cost Per Hour"
            required
        ></x-inputs.number>
    </x-inputs.group>

    <x-inputs.group class="w-full lg:w-4/12">
        <x-inputs.number
            name="hour_per_day"
            label="Hour Per Day"
            :value="old('hour_per_day', ($editing ? $version->hour_per_day : ''))"
            max="255"
            step="0.01"
            placeholder="Hour Per Day"
            required
        ></x-inputs.number>
    </x-inputs.group>

    <x-inputs.group class="w-full lg:w-4/12">
        <x-inputs.number
            name="months_to_pay"
            label="Months To Pay"
            :value="old('months_to_pay', ($editing ? $version->months_to_pay : ''))"
            max="255"
            step="0.01"
            placeholder="Months To Pay"
            required
        ></x-inputs.number>
    </x-inputs.group>

    <x-inputs.group class="w-full lg:w-4/12">
        <x-inputs.number
            name="unexpected"
            label="Unexpected"
            :value="old('unexpected', ($editing ? $version->unexpected : ''))"
            max="255"
            step="0.01"
            placeholder="Unexpected"
            required
        ></x-inputs.number>
    </x-inputs.group>

    <x-inputs.group class="w-full lg:w-4/12">
        <x-inputs.number
            name="company_gain"
            label="Company Gain"
            :value="old('company_gain', ($editing ? $version->company_gain : ''))"
            max="255"
            step="0.01"
            placeholder="Company Gain"
            required
        ></x-inputs.number>
    </x-inputs.group>

    <x-inputs.group class="w-full lg:w-4/12">
        <x-inputs.number
            name="bank_tax"
            label="Bank Tax"
            :value="old('bank_tax', ($editing ? $version->bank_tax : ''))"
            max="255"
            step="0.01"
            placeholder="Bank Tax"
            required
        ></x-inputs.number>
    </x-inputs.group>

    <x-inputs.group class="w-full lg:w-4/12">
        <x-inputs.number
            name="first_payment"
            label="First Payment"
            :value="old('first_payment', ($editing ? $version->first_payment : ''))"
            max="255"
            step="0.01"
            placeholder="First Payment"
            required
        ></x-inputs.number>
    </x-inputs.group>
</div>
