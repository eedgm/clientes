<div>
    <div>
        @can('create', App\Models\Version::class)
        <button class="button" wire:click="newVersion">
            <i class="mr-1 icon ion-md-add text-primary"></i>
            @lang('crud.common.new')
        </button>
        @endcan @can('delete-any', App\Models\Version::class)
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
                        <x-inputs.select
                            name="version.proposal_id"
                            label="Proposal"
                            wire:model="version.proposal_id"
                        >
                            <option value="null" disabled>Please select the Proposal</option>
                            @foreach($proposalsForSelect as $value => $label)
                            <option value="{{ $value }}"  >{{ $label }}</option>
                            @endforeach
                        </x-inputs.select>
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.text
                            name="version.attachment"
                            label="Attachment"
                            wire:model="version.attachment"
                            maxlength="255"
                            placeholder="Attachment"
                        ></x-inputs.text>
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.number
                            name="version.total"
                            label="Total"
                            wire:model="version.total"
                            max="255"
                            step="0.01"
                            placeholder="Total"
                        ></x-inputs.number>
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.date
                            name="versionTime"
                            label="Time"
                            wire:model="versionTime"
                            max="255"
                        ></x-inputs.date>
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.number
                            name="version.cost_per_hour"
                            label="Cost Per Hour"
                            wire:model="version.cost_per_hour"
                            max="255"
                            step="0.01"
                            placeholder="Cost Per Hour"
                        ></x-inputs.number>
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.number
                            name="version.hour_per_day"
                            label="Hour Per Day"
                            wire:model="version.hour_per_day"
                            max="255"
                            step="0.01"
                            placeholder="Hour Per Day"
                        ></x-inputs.number>
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.number
                            name="version.months_to_pay"
                            label="Months To Pay"
                            wire:model="version.months_to_pay"
                            max="255"
                            step="0.01"
                            placeholder="Months To Pay"
                        ></x-inputs.number>
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.number
                            name="version.unexpected"
                            label="Unexpected"
                            wire:model="version.unexpected"
                            max="255"
                            step="0.01"
                            placeholder="Unexpected"
                        ></x-inputs.number>
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.number
                            name="version.company_gain"
                            label="Company Gain"
                            wire:model="version.company_gain"
                            max="255"
                            step="0.01"
                            placeholder="Company Gain"
                        ></x-inputs.number>
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.number
                            name="version.bank_tax"
                            label="Bank Tax"
                            wire:model="version.bank_tax"
                            max="255"
                            step="0.01"
                            placeholder="Bank Tax"
                        ></x-inputs.number>
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.number
                            name="version.first_payment"
                            label="First Payment"
                            wire:model="version.first_payment"
                            max="255"
                            step="0.01"
                            placeholder="First Payment"
                        ></x-inputs.number>
                    </x-inputs.group>
                </div>
            </div>

            @if($editing) @can('view-any', App\Models\Task::class)
            <x-partials.card class="mt-5 shadow-none bg-gray-50">
                <h4 class="text-sm text-gray-600 font-bold mb-3">Tasks</h4>

                <livewire:version-tasks-detail :version="$version" />
            </x-partials.card>
            @endcan @endif
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
                        @lang('crud.user_versions.inputs.proposal_id')
                    </th>
                    <th class="px-4 py-3 text-left">
                        @lang('crud.user_versions.inputs.attachment')
                    </th>
                    <th class="px-4 py-3 text-right">
                        @lang('crud.user_versions.inputs.total')
                    </th>
                    <th class="px-4 py-3 text-left">
                        @lang('crud.user_versions.inputs.time')
                    </th>
                    <th class="px-4 py-3 text-right">
                        @lang('crud.user_versions.inputs.cost_per_hour')
                    </th>
                    <th class="px-4 py-3 text-right">
                        @lang('crud.user_versions.inputs.hour_per_day')
                    </th>
                    <th class="px-4 py-3 text-right">
                        @lang('crud.user_versions.inputs.months_to_pay')
                    </th>
                    <th class="px-4 py-3 text-right">
                        @lang('crud.user_versions.inputs.unexpected')
                    </th>
                    <th class="px-4 py-3 text-right">
                        @lang('crud.user_versions.inputs.company_gain')
                    </th>
                    <th class="px-4 py-3 text-right">
                        @lang('crud.user_versions.inputs.bank_tax')
                    </th>
                    <th class="px-4 py-3 text-right">
                        @lang('crud.user_versions.inputs.first_payment')
                    </th>
                    <th></th>
                </tr>
            </thead>
            <tbody class="text-gray-600">
                @foreach ($versions as $version)
                <tr class="hover:bg-gray-100">
                    <td class="px-4 py-3 text-left">
                        <input
                            type="checkbox"
                            value="{{ $version->id }}"
                            wire:model="selected"
                        />
                    </td>
                    <td class="px-4 py-3 text-left">
                        {{ optional($version->proposal)->product_name ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-left">
                        {{ $version->attachment ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-right">
                        {{ $version->total ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-left">
                        {{ $version->time ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-right">
                        {{ $version->cost_per_hour ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-right">
                        {{ $version->hour_per_day ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-right">
                        {{ $version->months_to_pay ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-right">
                        {{ $version->unexpected ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-right">
                        {{ $version->company_gain ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-right">
                        {{ $version->bank_tax ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-right">
                        {{ $version->first_payment ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-right" style="width: 134px;">
                        <div
                            role="group"
                            aria-label="Row Actions"
                            class="relative inline-flex align-middle"
                        >
                            @can('update', $version)
                            <button
                                type="button"
                                class="button"
                                wire:click="editVersion({{ $version->id }})"
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
                    <td colspan="12">
                        <div class="mt-10 px-4">{{ $versions->render() }}</div>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
