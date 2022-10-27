<div>
    <div>
        @can('create', App\Models\Person::class)
        <button class="button" wire:click="newPerson">
            <i class="mr-1 icon ion-md-add text-primary"></i>
            @lang('crud.common.new')
        </button>
        @endcan @can('delete-any', App\Models\Person::class)
        <button
            class="button button-danger"
                {{ empty($selected) ? 'disabled' : '' }}
            onclick="confirm('Are you sure?') || event.stopImmediatePropagation()"
            wire:click="destroySelected"
        >
            <i class="mr-1 icon ion-md-trash text-primary"></i>
            @lang('crud.common.delete_selected')
        </button>
        @endcan
    </div>

    <x-modal wire:model="showingModal">
        <div class="px-6 py-4">
            <div class="text-lg font-bold">{{ $modalTitle }}</div>

            <div class="mt-5">
                <div>
                    <input type="hidden" wire.model="user_id">
                    <x-inputs.group class="w-full">
                        <x-inputs.text
                            name="user.name"
                            wire:model="user.name"
                            label="Name"
                            maxlength="255"
                            placeholder="Name"
                            required
                        ></x-inputs.text>
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.email
                            name="user.email"
                            wire:model="user.email"
                            label="Email"
                            maxlength="255"
                            placeholder="Email"
                            required
                        ></x-inputs.email>
                    </x-inputs.group>
                    <div x-show="$wire.userExist">
                        <label class="pl-4 text-sm text-red-600">This user already exist! Try with another one.</label>
                    </div>

                    <x-inputs.group class="w-full">
                        <x-inputs.password
                            name="password"
                            wire:model="password"
                            label="Password"
                            maxlength="255"
                            placeholder="Password"
                        ></x-inputs.password>
                    </x-inputs.group>
                    <x-inputs.group class="w-full">
                        <div
                            image-url="{{ $editing && $person->photo ? \Storage::url($person->photo) : '' }}"
                            x-data="imageViewer()"
                            @refresh.window="refreshUrl()"
                        >
                            <x-inputs.partials.label
                                name="personPhoto"
                                label="Photo"
                            ></x-inputs.partials.label
                            ><br />

                            <!-- Show the image -->
                            <template x-if="imageUrl">
                                <img
                                    :src="imageUrl"
                                    class="object-cover border border-gray-200 rounded "
                                    style="width: 100px; height: 100px;"
                                />
                            </template>

                            <!-- Show the gray box when image is not available -->
                            <template x-if="!imageUrl">
                                <div
                                    class="bg-gray-100 border border-gray-200 rounded "
                                    style="width: 100px; height: 100px;"
                                ></div>
                            </template>

                            <div class="mt-2">
                                <input
                                    type="file"
                                    name="personPhoto"
                                    id="personPhoto{{ $uploadIteration }}"
                                    wire:model="personPhoto"
                                    @change="fileChosen"
                                />
                            </div>

                            @error('personPhoto')
                            @include('components.inputs.partials.error')
                            @enderror
                        </div>
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.textarea
                            name="person.description"
                            label="Description"
                            wire:model="person.description"
                            maxlength="255"
                        ></x-inputs.textarea>
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.text
                            name="person.phone"
                            label="Phone"
                            wire:model="person.phone"
                            maxlength="255"
                            placeholder="Phone"
                        ></x-inputs.text>
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.text
                            name="person.skype"
                            label="Skype"
                            wire:model="person.skype"
                            maxlength="255"
                            placeholder="Skype"
                        ></x-inputs.text>
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.select
                            name="person.rol_id"
                            label="Rol"
                            wire:model="person.rol_id"
                        >
                            <option value="null" disabled>Please select the Rol</option>
                            @foreach($rolsForSelect as $value => $label)
                            <option value="{{ $value }}"  >{{ $label }}</option>
                            @endforeach
                        </x-inputs.select>
                    </x-inputs.group>
                </div>
            </div>

            @if($editing) @can('view-any', App\Models\Ticket::class)
            <x-partials.card class="mt-5 shadow-none bg-gray-50">
                <h4 class="mb-3 text-sm font-bold text-gray-600">Tickets</h4>

                <livewire:person-tickets-detail :person="$person" />
            </x-partials.card>
            @endcan @endif
        </div>

        <div class="flex justify-between px-6 py-4 bg-gray-50">
            <button
                type="button"
                class="button"
                wire:click="$toggle('showingModal')"
            >
                <i class="mr-1 icon ion-md-close"></i>
                @lang('crud.common.cancel')
            </button>

            <button
                type="button"
                class="button button-primary"
                wire:click="save"
            >
                <i class="mr-1 icon ion-md-save"></i>
                @lang('crud.common.save')
            </button>
        </div>
    </x-modal>

    <div class="block w-full mt-4 overflow-auto scrolling-touch">
        <table class="w-full max-w-full mb-4 bg-transparent">
            <thead class="text-gray-700">
                <tr>
                    <th class="w-1 px-4 py-3 text-left">
                        <input
                            type="checkbox"
                            wire:model="allSelected"
                            wire:click="toggleFullSelection"
                            title="{{ trans('crud.common.select_all') }}"
                        />
                    </th>
                    <th class="px-4 py-3 text-left">
                        @lang('crud.client_people.inputs.photo')
                    </th>
                    <th class="px-4 py-3 text-left">
                        @lang('crud.client_people.inputs.description')
                    </th>
                    <th class="px-4 py-3 text-left">
                        @lang('crud.client_people.inputs.phone')
                    </th>
                    <th class="px-4 py-3 text-left">
                        @lang('crud.client_people.inputs.skype')
                    </th>
                    <th class="px-4 py-3 text-left">
                        @lang('crud.client_people.inputs.rol_id')
                    </th>
                    <th class="px-4 py-3 text-left">
                        @lang('crud.client_people.inputs.user_id')
                    </th>
                    <th></th>
                </tr>
            </thead>
            <tbody class="text-gray-600">
                @foreach ($people as $person)
                <tr class="hover:bg-gray-100">
                    <td class="px-4 py-3 text-left">
                        <input
                            type="checkbox"
                            value="{{ $person->id }}"
                            wire:model="selected"
                        />
                    </td>
                    <td class="px-4 py-3 text-left">
                        <x-partials.thumbnail
                            src="{{ $person->photo ? \Storage::url($person->photo) : '' }}"
                        />
                    </td>
                    <td class="px-4 py-3 text-left">
                        {{ $person->description ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-left">
                        {{ $person->phone ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-left">
                        {{ $person->skype ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-left">
                        {{ optional($person->rol)->name ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-left">
                        {{ optional($person->user)->name ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-right" style="width: 134px;">
                        <div
                            role="group"
                            aria-label="Row Actions"
                            class="relative inline-flex align-middle"
                        >
                            @can('update', $person)
                            <button
                                type="button"
                                class="button"
                                wire:click="editPerson({{ $person->id }})"
                            >
                                <i class="icon ion-md-create"></i>
                            </button>
                            @endcan
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="7">
                        <div class="px-4 mt-10">{{ $people->render() }}</div>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
