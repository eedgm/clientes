@php $editing = isset($attach) @endphp

<div class="flex flex-wrap">
    <x-inputs.group class="w-full">
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

        @if($editing && $attach->attachment)
        <div class="mt-2">
            <a href="{{ \Storage::url($attach->attachment) }}" target="_blank"
                ><i class="icon ion-md-download"></i>&nbsp;Download</a
            >
        </div>
        @endif @error('attachment') @include('components.inputs.partials.error')
        @enderror
    </x-inputs.group>

    <x-inputs.group class="w-full">
        <x-inputs.textarea
            name="description"
            label="Description"
            maxlength="255"
            >{{ old('description', ($editing ? $attach->description : ''))
            }}</x-inputs.textarea
        >
    </x-inputs.group>

    <x-inputs.group class="w-full">
        <x-inputs.select name="task_id" label="Task" required>
            @php $selected = old('task_id', ($editing ? $attach->task_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Task</option>
            @foreach($tasks as $value => $label)
            <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }} >{{ $label }}</option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="w-full">
        <x-inputs.select name="user_id" label="User" required>
            @php $selected = old('user_id', ($editing ? $attach->user_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the User</option>
            @foreach($users as $value => $label)
            <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }} >{{ $label }}</option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>
</div>
