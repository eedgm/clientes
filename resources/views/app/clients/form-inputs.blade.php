@php $editing = isset($client) @endphp

<div class="flex flex-wrap">
    <x-inputs.group class="w-full">
        <div
            x-data="imageViewer('{{ $editing && $client->logo ? \Storage::url($client->logo) : '' }}')"
        >
            <x-inputs.partials.label
                name="logo"
                label="Logo"
            ></x-inputs.partials.label
            ><br />

            <!-- Show the image -->
            <template x-if="imageUrl">
                <img
                    :src="imageUrl"
                    class="object-cover rounded border border-gray-200"
                    style="width: 100px; height: 100px;"
                />
            </template>

            <!-- Show the gray box when image is not available -->
            <template x-if="!imageUrl">
                <div
                    class="border rounded border-gray-200 bg-gray-100"
                    style="width: 100px; height: 100px;"
                ></div>
            </template>

            <div class="mt-2">
                <input type="file" name="logo" id="logo" @change="fileChosen" />
            </div>

            @error('logo') @include('components.inputs.partials.error')
            @enderror
        </div>
    </x-inputs.group>

    <x-inputs.group class="w-full md:w-6/12 lg:w-3/12">
        <x-inputs.text
            name="name"
            label="Name"
            :value="old('name', ($editing ? $client->name : ''))"
            maxlength="255"
            placeholder="Name"
            required
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="w-full md:w-6/12 lg:w-3/12">
        <x-inputs.number
            name="cost_per_hour"
            label="Cost Per Hour"
            :value="old('cost_per_hour', ($editing ? $client->cost_per_hour : ''))"
            max="255"
            step="0.01"
            placeholder="Cost Per Hour"
            required
        ></x-inputs.number>
    </x-inputs.group>

    <x-inputs.group class="w-full md:w-6/12 lg:w-3/12">
        <x-inputs.text
            name="owner"
            label="Owner"
            :value="old('owner', ($editing ? $client->owner : ''))"
            maxlength="255"
            placeholder="Owner"
            required
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="w-full md:w-6/12 lg:w-3/12">
        <x-inputs.text
            name="email_contact"
            label="Email Contact"
            :value="old('email_contact', ($editing ? $client->email_contact : ''))"
            maxlength="255"
            placeholder="Email Contact"
            required
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="w-full">
        <x-inputs.textarea
            name="description"
            label="Description"
            maxlength="255"
            >{{ old('description', ($editing ? $client->description : ''))
            }}</x-inputs.textarea
        >
    </x-inputs.group>
</div>
