<div>
    <div>
        @can('create', App\Models\Ticket::class)
        <button class="button" wire:click="newTicket">
            <i class="mr-1 icon ion-md-add text-primary"></i>
            @lang('crud.common.new')
        </button>
        @endcan @can('delete-any', App\Models\Ticket::class)
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
                        <x-inputs.textarea
                            name="ticket.description"
                            label="Description"
                            wire:model="ticket.description"
                            maxlength="255"
                        ></x-inputs.textarea>
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.select
                            name="ticket.statu_id"
                            label="Statu"
                            wire:model="ticket.statu_id"
                        >
                            <option value="null" disabled>Please select the Statu</option>
                            @foreach($statusForSelect as $value => $label)
                            <option value="{{ $value }}"  >{{ $label }}</option>
                            @endforeach
                        </x-inputs.select>
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.select
                            name="ticket.priority_id"
                            label="Priority"
                            wire:model="ticket.priority_id"
                        >
                            <option value="null" disabled>Please select the Priority</option>
                            @foreach($prioritiesForSelect as $value => $label)
                            <option value="{{ $value }}"  >{{ $label }}</option>
                            @endforeach
                        </x-inputs.select>
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.number
                            name="ticket.hours"
                            label="Hours"
                            wire:model="ticket.hours"
                            max="255"
                            step="0.01"
                            placeholder="Hours"
                        ></x-inputs.number>
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.number
                            name="ticket.total"
                            label="Total"
                            wire:model="ticket.total"
                            max="255"
                            step="0.01"
                            placeholder="Total"
                        ></x-inputs.number>
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.date
                            name="ticketFinishedTicket"
                            label="Finished Ticket"
                            wire:model="ticketFinishedTicket"
                            max="255"
                        ></x-inputs.date>
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.textarea
                            name="ticket.comments"
                            label="Comments"
                            wire:model="ticket.comments"
                            maxlength="255"
                        ></x-inputs.textarea>
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.select
                            name="ticket.person_id"
                            label="Person"
                            wire:model="ticket.person_id"
                        >
                            <option value="null" disabled>Please select the Person</option>
                            @foreach($peopleForSelect as $value => $label)
                            <option value="{{ $value }}"  >{{ $label }}</option>
                            @endforeach
                        </x-inputs.select>
                    </x-inputs.group>
                </div>
            </div>

            @if($editing) @can('view-any', App\Models\Attachment::class)
            <x-partials.card class="mt-5 shadow-none bg-gray-50">
                <h4 class="mb-3 text-sm font-bold text-gray-600">
                    Attachments
                </h4>

                <livewire:ticket-attachments-detail :ticket="$ticket" />
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
                    <th class="px-4 py-3 text-left">
                        @lang('crud.product_tickets.inputs.comments')
                    </th>
                    <th class="px-4 py-3 text-left">
                        @lang('crud.product_tickets.inputs.receipt_id')
                    </th>
                    <th class="px-4 py-3 text-left">
                        @lang('crud.product_tickets.inputs.person_id')
                    </th>
                    <th></th>
                </tr>
            </thead>
            <tbody class="text-gray-600">
                @foreach ($tickets as $ticket)
                <tr class="hover:bg-gray-100">
                    <td class="px-4 py-3 text-left">
                        <input
                            type="checkbox"
                            value="{{ $ticket->id }}"
                            wire:model="selected"
                        />
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
                        {{ $ticket->finished_ticket ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-left">
                        {{ $ticket->comments ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-left">
                        {{ optional($ticket->receipt)->description ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-left">
                        {{ optional($ticket->person)->description ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-right" style="width: 134px;">
                        <div
                            role="group"
                            aria-label="Row Actions"
                            class="relative inline-flex align-middle"
                        >
                            @can('update', $ticket)
                            <button
                                type="button"
                                class="button"
                                wire:click="editTicket({{ $ticket->id }})"
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
                    <td colspan="10">
                        <div class="px-4 mt-10">{{ $tickets->render() }}</div>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
