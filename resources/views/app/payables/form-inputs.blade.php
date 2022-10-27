@php $editing = isset($payable) @endphp

<div class="flex flex-wrap">
    <x-inputs.group class="w-full">
        <x-inputs.text
            name="name"
            label="Name"
            :value="old('name', ($editing ? $payable->name : ''))"
            maxlength="255"
            placeholder="Name"
            required
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="w-full">
        <x-inputs.date
            name="date"
            label="Date"
            value="{{ old('date', ($editing ? optional($payable->date)->format('Y-m-d') : '')) }}"
            max="255"
            required
        ></x-inputs.date>
    </x-inputs.group>

    <x-inputs.group class="w-full">
        <x-inputs.number
            name="cost"
            label="Cost"
            :value="old('cost', ($editing ? $payable->cost : ''))"
            max="255"
            step="0.01"
            placeholder="Cost"
            required
        ></x-inputs.number>
    </x-inputs.group>

    <x-inputs.group class="w-full">
        <x-inputs.number
            name="margin"
            label="Margin"
            :value="old('margin', ($editing ? $payable->margin : ''))"
            max="255"
            step="0.01"
            placeholder="Margin"
            required
        ></x-inputs.number>
    </x-inputs.group>

    <x-inputs.group class="w-full">
        <x-inputs.number
            name="total"
            label="Total"
            :value="old('total', ($editing ? $payable->total : ''))"
            max="255"
            step="0.01"
            placeholder="Total"
            required
        ></x-inputs.number>
    </x-inputs.group>

    <x-inputs.group class="w-full">
        <x-inputs.select name="product_id" label="Product" required>
            @php $selected = old('product_id', ($editing ? $payable->product_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Product</option>
            @foreach($products as $value => $label)
            <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }} >{{ $label }}</option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="w-full">
        <x-inputs.select name="supplier_id" label="Supplier" required>
            @php $selected = old('supplier_id', ($editing ? $payable->supplier_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Supplier</option>
            @foreach($suppliers as $value => $label)
            <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }} >{{ $label }}</option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="w-full">
        <x-inputs.text
            name="supplier_id_reference"
            label="Supplier Id Reference"
            :value="old('supplier_id_reference', ($editing ? $payable->supplier_id_reference : ''))"
            maxlength="255"
            placeholder="Supplier Id Reference"
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="w-full">
        <x-inputs.select name="periodicity" label="Periodicity">
            @php $selected = old('periodicity', ($editing ? $payable->periodicity : '')) @endphp
            <option value="month" {{ $selected == 'month' ? 'selected' : '' }} >Month</option>
            <option value="year" {{ $selected == 'year' ? 'selected' : '' }} >Year</option>
        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="w-full">
        <x-inputs.select name="receipt_id" label="Receipt">
            @php $selected = old('receipt_id', ($editing ? $payable->receipt_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Receipt</option>
            @foreach($receipts as $value => $label)
            <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }} >{{ $label }}</option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>
</div>
