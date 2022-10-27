@php $editing = isset($supplier) @endphp

<div class="flex flex-wrap">
    <x-inputs.group class="w-full">
        <x-inputs.text
            name="name"
            label="Name"
            :value="old('name', ($editing ? $supplier->name : ''))"
            maxlength="255"
            placeholder="Name"
            required
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="w-full">
        <x-inputs.url
            name="url"
            label="Url"
            :value="old('url', ($editing ? $supplier->url : ''))"
            maxlength="255"
            placeholder="Url"
        ></x-inputs.url>
    </x-inputs.group>

    <x-inputs.group class="w-full lg:w-6/12">
        <x-inputs.text
            name="login"
            label="Login"
            :value="old('login', ($editing ? $supplier->login : ''))"
            maxlength="255"
            placeholder="Login"
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="w-full lg:w-6/12">
        <x-inputs.password
            name="password"
            label="Password"
            maxlength="255"
            placeholder="Password"
        ></x-inputs.password>
    </x-inputs.group>

    <x-inputs.group class="w-full">
        <x-inputs.textarea
            name="description"
            label="Description"
            maxlength="255"
            >{{ old('description', ($editing ? $supplier->description : ''))
            }}</x-inputs.textarea
        >
    </x-inputs.group>
</div>
