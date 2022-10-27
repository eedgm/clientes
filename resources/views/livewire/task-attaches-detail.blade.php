<div>
    <div>
        @can('create', App\Models\Attach::class)
        <button class="button" wire:click="newAttach">
            <i class="mr-1 icon ion-md-add text-primary"></i>
            @lang('crud.common.new')
        </button>
        @endcan @can('delete-any', App\Models\Attach::class)
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
                    <x-inputs.group class="w-full">
                        <x-inputs.partials.label
                            name="attachAttachment"
                            label="Attachment"
                        ></x-inputs.partials.label
                        ><br />

                        <input
                            type="file"
                            name="attachAttachment"
                            id="attachAttachment{{ $uploadIteration }}"
                            wire:model="attachAttachment"
                            class="form-control-file"
                        />

                        @if($editing && $attach->attachment)
                        <div class="mt-2">
                            <a
                                href="{{ \Storage::url($attach->attachment) }}"
                                target="_blank"
                                ><i class="icon ion-md-download"></i
                                >&nbsp;Download</a
                            >
                        </div>
                        @endif @error('attachAttachment')
                        @include('components.inputs.partials.error') @enderror
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.textarea
                            name="attach.description"
                            label="Description"
                            wire:model="attach.description"
                            maxlength="255"
                        ></x-inputs.textarea>
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.select
                            name="attach.user_id"
                            label="User"
                            wire:model="attach.user_id"
                        >
                            <option value="null" disabled>Please select the User</option>
                            @foreach($usersForSelect as $value => $label)
                            <option value="{{ $value }}"  >{{ $label }}</option>
                            @endforeach
                        </x-inputs.select>
                    </x-inputs.group>
                </div>
            </div>
        </div>

        <div class="px-6 py-4 bg-gray-50 flex justify-between">
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

    <div class="block w-full overflow-auto scrolling-touch mt-4">
        <table class="w-full max-w-full mb-4 bg-transparent">
            <thead class="text-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left w-1">
                        <input
                            type="checkbox"
                            wire:model="allSelected"
                            wire:click="toggleFullSelection"
                            title="{{ trans('crud.common.select_all') }}"
                        />
                    </th>
                    <th class="px-4 py-3 text-left">
                        @lang('crud.task_attaches.inputs.attachment')
                    </th>
                    <th class="px-4 py-3 text-left">
                        @lang('crud.task_attaches.inputs.description')
                    </th>
                    <th class="px-4 py-3 text-left">
                        @lang('crud.task_attaches.inputs.user_id')
                    </th>
                    <th></th>
                </tr>
            </thead>
            <tbody class="text-gray-600">
                @foreach ($attaches as $attach)
                <tr class="hover:bg-gray-100">
                    <td class="px-4 py-3 text-left">
                        <input
                            type="checkbox"
                            value="{{ $attach->id }}"
                            wire:model="selected"
                        />
                    </td>
                    <td class="px-4 py-3 text-left">
                        @if($attach->attachment)
                        <a
                            href="{{ \Storage::url($attach->attachment) }}"
                            target="blank"
                            ><i class="mr-1 icon ion-md-download"></i
                            >&nbsp;Download</a
                        >
                        @else - @endif
                    </td>
                    <td class="px-4 py-3 text-left">
                        {{ $attach->description ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-left">
                        {{ optional($attach->user)->name ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-right" style="width: 134px;">
                        <div
                            role="group"
                            aria-label="Row Actions"
                            class="relative inline-flex align-middle"
                        >
                            @can('update', $attach)
                            <button
                                type="button"
                                class="button"
                                wire:click="editAttach({{ $attach->id }})"
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
                    <td colspan="4">
                        <div class="mt-10 px-4">{{ $attaches->render() }}</div>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
