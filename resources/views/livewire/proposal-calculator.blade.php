<div>
    <a wire:click="seeVersions"><i class="bx bx-calculator"></i></a>

    <x-modal wire:model="showingVersionModal">
        <div class="px-6 py-4 text-left">
            <div class="text-lg font-bold">{{ $modalVersionTitle }}</div>
            <div class="mt-5">
                <div class="">
                    <table class="w-full max-w-full mb-4 bg-transparent">
                        <thead class="text-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left">
                                    @lang('crud.proposal_versions.inputs.attachment')
                                </th>
                                <th class="px-4 py-3 text-left">
                                    @lang('crud.proposal_versions.inputs.user_id')
                                </th>
                                <th class="px-4 py-3 text-right">
                                    @lang('crud.proposal_versions.inputs.months_to_pay')
                                </th>
                                <th class="px-4 py-3 text-right">
                                    @lang('crud.proposal_versions.inputs.total')
                                </th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600">
                            @foreach ($proposal->versions as $version)
                            <tr class="hover:bg-gray-100">
                                <td class="px-4 py-3 text-left">
                                    @if($version->attachment)
                                    <a
                                        href="{{ \Storage::url($version->attachment) }}"
                                        target="blank"
                                        ><i class="mr-1 icon ion-md-download"></i
                                        >&nbsp;See</a
                                    >
                                    @else - @endif
                                </td>
                                <td class="px-4 py-3 text-left">
                                    {{ optional($version->user)->name }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    {{ number_format($version->months_to_pay, 0) ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    {{ $version->total ?? '-' }}
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
                                            <i class="bx bx-pencil"></i>
                                        </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <button
                        type="button"
                        class="button button-secundary"
                        wire:click="addNewVersion"
                    >
                        <i class="bx bx-plus"></i>
                        New Version
                    </button>
                </div>
            </div>
        </div>
    </x-modal>

    <x-modal wire:model="showingModal" class="mx-10">
        <div class="px-6 py-4 text-left">
            <div class="text-lg font-bold">{{ $modalTitle }}</div>
            <div class="grid grid-cols-2 gap-4 mt-5">
                <div class="grid grid-cols-2 text-sm">
                    <x-inputs.group class="w-full">
                        <x-inputs.number
                            name="version.hours"
                            label="Hours"
                            wire:model="version.hours"
                            step="0.01"
                            placeholder="hours"
                            disabled
                            class="bg-gray-100 cursor-not-allowed"
                        ></x-inputs.number>
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.number
                            name="version.unexpected"
                            label="Unexpected"
                            wire:model="version.unexpected"
                            step="0.01"
                            placeholder="Unexpected"
                        ></x-inputs.number>
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
                            name="version.company_gain"
                            label="Company Gain"
                            wire:model="version.company_gain"
                            step="0.01"
                            placeholder="Company Gain"
                        ></x-inputs.number>
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.number
                            name="version.bank_tax"
                            label="Bank Tax"
                            wire:model="version.bank_tax"
                            step="0.01"
                            placeholder="Bank Tax"
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
                            name="version.first_payment"
                            label="First Payment"
                            wire:model="version.first_payment"
                            step="0.01"
                            placeholder="First Payment"
                        ></x-inputs.number>
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
                        <x-inputs.number
                            name="version.time"
                            label="Time"
                            wire:model="versionTime"
                            max="255"
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
                        <x-inputs.select
                            name="version.user_id"
                            label="Client"
                            wire:model="version.user_id"
                        >
                            <option value="null" disabled>Please select the User</option>
                            @foreach($usersForSelect as $value => $label)
                            <option value="{{ $label->user->id }}"  >{{ $label->user->name }}</option>
                            @endforeach
                        </x-inputs.select>
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.partials.label
                            name="versionAttachment"
                            label="Attachment"
                        ></x-inputs.partials.label
                        ><br />

                        <input
                            type="file"
                            name="versionAttachment"
                            id="versionAttachment{{ $uploadIteration }}"
                            wire:model="versionAttachment"
                            class="form-control-file"
                        />

                        @if($editing && $version->attachment)
                        <div class="mt-2">
                            <a
                                href="{{ \Storage::url($version->attachment) }}"
                                target="_blank"
                                ><i class="icon ion-md-download"></i
                                >&nbsp;Download</a
                            >
                        </div>
                        @endif @error('versionAttachment')
                        @include('components.inputs.partials.error') @enderror
                    </x-inputs.group>
                </div>
                <div class="font-normal text-md">
                    <div class="text-red-500">Calculation <i class="bx bx-calculator"></i></div>
                    <table class="w-full mt-3">
                        <tr>
                            <th class="p-2 bg-gray-100 border border-gray-400 rounded">Horas:</th>
                            <td class="p-2 text-right border-b border-gray-400">{{ $hours }}</td>
                        </tr>
                        <tr>
                            <th class="p-2 bg-gray-100 border border-gray-400 rounded">Imprevistos:</th>
                            <td class="p-2 text-right border-b border-gray-400">{{ $unexpected }}</td>
                        </tr>
                        <tr>
                            <th class="p-2 bg-gray-100 border border-gray-400 rounded">Horas totales:</th>
                            <td class="p-2 text-right border-b border-gray-400">{{ $total_hours }}</td>
                        </tr>
                        <tr>
                            <th class="p-2 bg-gray-100 border border-gray-400 rounded">Total sin extras:</th>
                            <td class="p-2 text-right border-b border-gray-400">$ {{ number_format($price, 2) }}</td>
                        </tr>
                        <tr>
                            <th class="p-2 bg-gray-100 border border-gray-400 rounded">Ganancia de la compañia:</th>
                            <td class="p-2 text-right border-b border-gray-400">$ {{ number_format($company_gain, 2) }}</td>
                        </tr>
                        <tr>
                            <th class="p-2 bg-gray-100 border border-gray-400 rounded">Costo con ganancia:</th>
                            <td class="p-2 text-right border-b border-gray-400">$ {{ number_format($price_with_gain, 2) }}</td>
                        </tr>
                        <tr>
                            <th class="p-2 bg-gray-100 border border-gray-400 rounded">Costo con gasto bancario:</th>
                            <td class="p-2 text-right border-b border-gray-400">$ {{ number_format($price_with_bank_tax, 2) }}</td>
                        </tr>
                        <tr>
                            <th class="p-2 bg-gray-100 border border-gray-400 rounded">Pago por mes:</th>
                            <td class="p-2 text-right">$ {{ number_format($price_month_divided, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            @if ($receipts_count > 0)
                <table class="table w-full table-list table-striped-primary">
                    <thead class="text-gray-700 bg-gray-100 border-b-2 border-gray-300">
                        <tr class="">
                            <th class="px-4 py-3 text-left">
                                @lang('crud.receipts.date_dashboard')
                            </th>
                            <th class="px-4 py-3 text-right">
                                #
                            </th>
                            <th class="px-4 py-3 text-left">
                                @lang('crud.receipts.inputs.client_id')
                            </th>
                            <th class="px-4 py-3 text-right lg:w-32">
                                @lang('crud.receipts.total')
                            </th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600">
                            @foreach ($receipts as $receipt)
                            <tr class="hover:bg-gray-100 odd:bg-white even:bg-blue-50">
                                <td class="px-4 py-3 text-left">
                                    {{ $receipt->real_date->format('M d') ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    @can('edit', $receipt)
                                        <x-links href="{{ route('receipts.edit', $receipt->id) }}">{{ $receipt->number ?? '-' }}</x-links>
                                    @else
                                        {{ $receipt->number ?? '-' }}
                                    @endcan
                                </td>
                                <td class="px-4 py-3 text-left">
                                    {{ optional($receipt->client)->name ?? '-'
                                    }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    $ {{
                                        $receipt->manual_value ?? ($receipt->tickets->sum('total')) + ($receipt->payables->sum('total'));
                                    }}
                                </td>
                            </tr>
                            @endforeach
                    </tbody>
                </table>
            @else
                <div class="mt-5 ml-3">
                    <button
                        type="button"
                        class="button"
                        wire:click="generateReceipts"
                    >
                        <i class="bx bx-receipt"></i>
                        Generate Receipts
                    </button>
                </div>
            @endif
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
</div>
