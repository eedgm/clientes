@php $editing = isset($ticket) @endphp

<div class="flex flex-wrap">
    <x-inputs.group class="w-full">
        <x-inputs.textarea
            name="description"
            label="Description"
            required
            >{{ old('description', ($editing ? $ticket->description : ''))
            }}</x-inputs.textarea
        >
    </x-inputs.group>

    <x-inputs.group class="w-full lg:w-4/12">
        <x-inputs.select name="statu_id" label="Statu" required>
            @php $selected = old('statu_id', ($editing ? $ticket->statu_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Statu</option>
            @foreach($status as $value => $label)
            <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }} >{{ $label }}</option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="w-full lg:w-4/12">
        <x-inputs.select name="priority_id" label="Priority" required>
            @php $selected = old('priority_id', ($editing ? $ticket->priority_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Priority</option>
            @foreach($priorities as $value => $label)
            <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }} >{{ $label }}</option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="w-full lg:w-4/12">
        <x-inputs.number
            name="hours"
            label="Hours"
            :value="old('hours', ($editing ? $ticket->hours : ''))"
            step="0.01"
            placeholder="Hours"
        ></x-inputs.number>
    </x-inputs.group>

    <x-inputs.group class="w-full lg:w-4/12">
        <x-inputs.number
            name="total"
            label="Total"
            :value="old('total', ($editing ? $ticket->total : ''))"
            step="0.01"
            placeholder="Total"
        ></x-inputs.number>
    </x-inputs.group>

    <x-inputs.group class="w-full lg:w-4/12">
        <x-inputs.date
            name="finished_ticket"
            label="Finished Ticket"
            value="{{ old('finished_ticket', ($editing ? optional($ticket->finished_ticket)->format('Y-m-d') : '')) }}"
        ></x-inputs.date>
    </x-inputs.group>

    <x-inputs.group class="w-full">
        <x-inputs.textarea name="comments" label="Comments" maxlength="255"
            >{{ old('comments', ($editing ? $ticket->comments : ''))
            }}</x-inputs.textarea
        >
    </x-inputs.group>

    <x-inputs.group class="w-full lg:w-4/12">
        <x-inputs.select name="product_id" label="Product" required>
            @php $selected = old('product_id', ($editing ? $ticket->product_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Product</option>
            @foreach($products as $value => $label)
            <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }} >{{ $label }}</option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="w-full lg:w-4/12">
        <x-inputs.select name="receipt_id" label="Receipt">
            @php $selected = old('receipt_id', ($editing ? $ticket->receipt_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Receipt</option>
            @foreach($receipts as $value => $label)
            <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }} >{{ $label }}</option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="w-full lg:w-4/12">
        <x-inputs.select name="person_id" label="Person">
            @php $selected = old('person_id', ($editing ? $ticket->person_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Person</option>
            @foreach($people as $value => $label)
            <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }} >{{ $label }}</option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>
</div>
