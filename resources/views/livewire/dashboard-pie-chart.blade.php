<div class="flex flex-wrap w-full gap-5">
    <x-partials.card class="w-4/12">
        <div class="h-80 ">
            <x-slot name="title">
                Tickets
            </x-slot>
            <livewire:livewire-pie-chart
                    key="{{ $ticketsPieChartModel->reactiveKey() }}"
                    :pie-chart-model="$ticketsPieChartModel"
                />
        </div>
    </x-partials.card>

    <x-partials.card class="w-4/12">
        <div class="h-80 ">
            <x-slot name="title">
                Tasks
            </x-slot>
            <livewire:livewire-pie-chart
                    key="{{ $tasksPieChartModel->reactiveKey() }}"
                    :pie-chart-model="$tasksPieChartModel"
                />
        </div>
    </x-partials.card>

    <x-modal wire:model="showingModal">
        <div class="px-6 py-4">
            <div class="text-lg font-bold">{{ $modalTitle }}</div>

            <div class="mt-5">
                <table class="w-full max-w-full mb-4 bg-transparent">
                    <thead class="text-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left">
                                @lang('crud.clients.name')
                            </th>
                            <th class="px-4 py-3 text-left">
                                @lang('crud.products.name')
                            </th>
                            <th class="px-4 py-3 text-left">
                                @lang('crud.product_tickets.inputs.description')
                            </th>
                            <th class="px-4 py-3 text-left">
                                @lang('crud.product_tickets.inputs.statu_id')
                            </th>
                            <th class="px-4 py-3 text-left">
                                @lang('crud.product_tickets.inputs.priority_id')
                            </th>
                            <th class="px-4 py-3 text-right">
                                @lang('crud.product_tickets.inputs.hours')
                            </th>
                            <th class="px-4 py-3 text-right">
                                @lang('crud.product_tickets.inputs.total')
                            </th>
                            <th class="px-4 py-3 text-left">
                                @lang('crud.product_tickets.inputs.finished_ticket')
                            </th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600">
                            @foreach ($tickets_show as $ticket)
                            <tr class="hover:bg-gray-100">
                                <td class="px-4 py-3 text-left">
                                    {{ $ticket->product->client->name ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-left">
                                    {{ $ticket->product->name ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-left">
                                    {{ $ticket->description ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-left">
                                    {{ optional($ticket->statu)->name ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-left">
                                    {{ optional($ticket->priority)->name ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    {{ $ticket->hours ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    {{ $ticket->total ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-left">
                                    {{ $ticket->finished_ticket ? date('Y-m-d', strtotime($ticket->finished_ticket)) : '-' }}
                                </td>
                            </tr>
                            @endforeach
                    </tbody>
                </table>
            </div>

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
