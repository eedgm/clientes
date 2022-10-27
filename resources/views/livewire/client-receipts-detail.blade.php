<div>
    <div>
        @can('create', App\Models\Receipt::class)
        <button class="button" wire:click="newReceipt">
            <i class="mr-1 icon ion-md-add text-primary"></i>
            @lang('crud.common.new')
        </button>
        @endcan @can('delete-any', App\Models\Receipt::class)
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
                        <x-inputs.number
                            name="receipt.number"
                            label="Number"
                            wire:model="receipt.number"
                            max="255"
                            placeholder="Number"
                        ></x-inputs.number>
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.textarea
                            name="receipt.description"
                            label="Description"
                            wire:model="receipt.description"
                            maxlength="255"
                        ></x-inputs.textarea>
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.date
                            name="receiptRealDate"
                            label="Real Date"
                            wire:model="receiptRealDate"
                            max="255"
                        ></x-inputs.date>
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.checkbox
                            name="receipt.charged"
                            label="Charged"
                            wire:model="receipt.charged"
                        ></x-inputs.checkbox>
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.text
                            name="receipt.reference_charged"
                            label="Reference Charged"
                            wire:model="receipt.reference_charged"
                            maxlength="255"
                            placeholder="Reference Charged"
                        ></x-inputs.text>
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.datetime
                            name="receipt.date_charged"
                            label="Date Charged"
                            wire:model="receipt.date_charged"
                            max="255"
                        ></x-inputs.datetime>
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
                    <th class="px-4 py-3 text-right">
                        @lang('crud.client_receipts.inputs.number')
                    </th>
                    <th class="px-4 py-3 text-left">
                        @lang('crud.client_receipts.inputs.description')
                    </th>
                    <th class="px-4 py-3 text-left">
                        @lang('crud.client_receipts.inputs.real_date')
                    </th>
                    <th class="px-4 py-3 text-left">
                        @lang('crud.client_receipts.inputs.charged')
                    </th>
                    <th class="px-4 py-3 text-left">
                        @lang('crud.client_receipts.inputs.reference_charged')
                    </th>
                    <th class="px-4 py-3 text-left">
                        @lang('crud.client_receipts.inputs.date_charged')
                    </th>
                    <th></th>
                </tr>
            </thead>
            <tbody class="text-gray-600">
                @foreach ($receipts as $receipt)
                <tr class="hover:bg-gray-100">
                    <td class="px-4 py-3 text-left">
                        <input
                            type="checkbox"
                            value="{{ $receipt->id }}"
                            wire:model="selected"
                        />
                    </td>
                    <td class="px-4 py-3 text-right">
                        {{ $receipt->number ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-left">
                        {{ $receipt->description ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-left">
                        {{ $receipt->real_date ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-left">
                        {{ $receipt->charged ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-left">
                        {{ $receipt->reference_charged ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-left">
                        {{ $receipt->date_charged ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-right" style="width: 134px;">
                        <div
                            role="group"
                            aria-label="Row Actions"
                            class="relative inline-flex align-middle"
                        >
                            @can('update', $receipt)
                            <button
                                type="button"
                                class="button"
                                wire:click="editReceipt({{ $receipt->id }})"
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
                        <div class="mt-10 px-4">{{ $receipts->render() }}</div>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
