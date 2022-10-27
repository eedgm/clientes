@php $editing = isset($task) @endphp

<div class="flex flex-wrap">
    <x-inputs.group class="w-full">
        <x-inputs.text
            name="name"
            label="Name"
            :value="old('name', ($editing ? $task->name : ''))"
            maxlength="255"
            placeholder="Name"
            required
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="w-full">
        <x-inputs.number
            name="hours"
            label="Hours"
            :value="old('hours', ($editing ? $task->hours : ''))"
            max="255"
            placeholder="Hours"
            required
        ></x-inputs.number>
    </x-inputs.group>

    <x-inputs.group class="w-full">
        <x-inputs.select name="statu_id" label="Statu" required>
            @php $selected = old('statu_id', ($editing ? $task->statu_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Statu</option>
            @foreach($status as $value => $label)
            <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }} >{{ $label }}</option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="w-full">
        <x-inputs.select name="priority_id" label="Priority" required>
            @php $selected = old('priority_id', ($editing ? $task->priority_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Priority</option>
            @foreach($priorities as $value => $label)
            <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }} >{{ $label }}</option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="w-full">
        <x-inputs.number
            name="real_hours"
            label="Real Hours"
            :value="old('real_hours', ($editing ? $task->real_hours : ''))"
            max="255"
            step="0.01"
            placeholder="Real Hours"
        ></x-inputs.number>
    </x-inputs.group>

    <x-inputs.group class="w-full">
        <x-inputs.select name="version_id" label="Version" required>
            @php $selected = old('version_id', ($editing ? $task->version_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Version</option>
            @foreach($versions as $value => $label)
            <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }} >{{ $label }}</option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="w-full">
        <x-inputs.select name="receipt_id" label="Receipt">
            @php $selected = old('receipt_id', ($editing ? $task->receipt_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Receipt</option>
            @foreach($receipts as $value => $label)
            <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }} >{{ $label }}</option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>
</div>
