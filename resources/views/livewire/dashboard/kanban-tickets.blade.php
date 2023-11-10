<div>
    <div class="hidden">
        <div class="text-blue-500 bg-yellow-100"></div>
        <div class="text-purple-500 bg-purple-100"></div>
        <div class="bg-sky-100 text-sky-500"></div>
    </div>
    <div class="antialiased sans-serif">
        <h1 class="w-full p-2 -mb-5 bg-gray-300">Kanban Tickets</h1>
        <div class="flex w-full min-h-screen">
            <div class="px-4 py-4 mx-auto">
                <div class="py-2 md:py-8">
                    <div class="flex flex-wrap pb-2 -mx-4 overflow-x-auto">
                        @foreach ($status as $st)
                        <div class="flex-shrink-0 w-full px-4 mb-5 md:w-1/3 lg:w-1/6 md:mb-0">
                            <div class="pb-4 overflow-x-hidden overflow-y-auto border-t-8 border-red-400 rounded-lg shadow bg-gray-50" style="min-height: 100px">
                                <div class="sticky top-0 flex items-center justify-between px-4 py-2">
                                    <h2 class="font-medium text-gray-800"><i class="bx {{ $icons[$st->id] }}"></i> {{ $st->name }}</h2>
                                    <a wire:click.prevent="addTicket({{ $st->id }})" href="#" class="inline-flex items-center text-sm font-medium">
                                        <i class="bx bx-plus"></i>
                                    </a>
                                </div>

                                <div class="px-4">
                                    <div @dragenter="$wire.onDragEnter(event, {{ $st->id }})" class="py-2 rounded-lg">
                                        @foreach ($tickets as $ticket)
                                            @if ($ticket->statu_id == $st->id)
                                                <div
                                                    class="p-2 mb-2 rounded-lg shadow {{ $colors[$st->id] }}"
                                                    draggable="true"
                                                    @dragend="$wire.onDragEnd(event, {{ $ticket->id }})"
                                                >
                                                    <div class="text-xs text-gray-800">{{ $ticket->product->client->name }} / {{ $ticket->product->name }}</div>
                                                    <div class="text-gray-800">{{ Str::limit($ticket->description, 150) }}</div>
                                                    <hr class="h-px my-2 bg-gray-400 border-0" />
                                                    <div class="text-xs">Hours: {{ $ticket->hours ?? '-' }} / Cost: {{ $ticket->total ?? '-' }}</div>
                                                    <div class="w-full bg-gray-200 rounded-full dark:bg-gray-500">
                                                        <div class="bg-blue-600 text-xs font-medium text-blue-100 text-center p-0.5 leading-none rounded-full" style="width: {{ $ticket->progress ?? 0 }}%"> {{ $ticket->progress ?? 0 }}%</div>
                                                    </div>
                                                    <div class="text-right">
                                                        <i class="text-lg bx bx-pencil" wire:click="edit({{ $ticket->id }})"></i>
                                                        <button
                                                            onclick="confirm('Are you sure?') || event.stopImmediatePropagation()"
                                                            wire:click="delete({{ $ticket->id }})"
                                                        >
                                                            <i class="text-lg bx bx-trash"></i>
                                                        </button>

                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>

                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>

    <x-modal wire:model="showingModal">
        <div class="px-6 py-4">
            <div class="text-lg font-bold">{{ $modalTitle }}</div>

            <div class="mt-5">
                <div class="flex flex-wrap">
                    <x-inputs.group class="w-full md:w-1/2">
                        <x-inputs.select
                            name="ticket_client_id"
                            label="Client"
                            wire:model="ticket_client_id"
                            wire:change="selectProducts"
                        >
                            <option value="null" disabled>Please select the Client</option>
                            @foreach($clients as $value => $label)
                            <option value="{{ $value }}"  >{{ $label }}</option>
                            @endforeach
                        </x-inputs.select>
                    </x-inputs.group>

                    <x-inputs.group class="w-full md:w-1/2">
                        <x-inputs.select
                            name="ticket.product_id"
                            label="Product"
                            wire:model="ticket.product_id"
                        >
                            <option value="null" disabled>Please select the Product</option>
                            @if (!is_null($products))
                                @foreach($products as $value => $label)
                                <option value="{{ $value }}"  >{{ $label }}</option>
                                @endforeach
                            @endif
                        </x-inputs.select>
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.textarea
                            name="ticket.description"
                            label="Description"
                            wire:model="ticket.description"
                            maxlength="255"
                        ></x-inputs.textarea>
                    </x-inputs.group>

                    <x-inputs.group class="w-full md:w-1/3">
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

                    <x-inputs.group class="w-full md:w-1/3">
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

                    @if ($ticketStatusSelected == 6)

                    <x-inputs.group class="w-full md:w-1/3">
                        <x-inputs.date
                            name="ticketFinishedTicket"
                            label="Finished Ticket"
                            wire:model="ticketFinishedTicket"
                            max="255"
                        ></x-inputs.date>
                    </x-inputs.group>
                    @endif

                    <x-inputs.group class="w-full md:w-1/3">
                        <x-inputs.number
                            name="ticket.hours"
                            label="Hours"
                            wire:model="ticket.hours"
                            max="255"
                            step="0.01"
                            placeholder="Hours"
                        ></x-inputs.number>
                    </x-inputs.group>

                    <x-inputs.group class="w-full md:w-1/3">
                        <x-inputs.number
                            name="ticket.total"
                            label="Total"
                            wire:model="ticket.total"
                            step="0.01"
                            placeholder="Total"
                        ></x-inputs.number>
                    </x-inputs.group>

                    <x-inputs.group class="w-full md:w-1/3">
                        <x-inputs.progress
                            name="ticket.progress"
                            label="Progress"
                            wire:model="ticket.progress"
                            placeholder="progress"
                        ></x-inputs.progress>
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.textarea
                            name="ticket.comments"
                            label="Comments"
                            wire:model="ticket.comments"
                        ></x-inputs.textarea>
                    </x-inputs.group>
                </div>
            </div>

            @if($editing) @can('view-any', App\Models\Attachment::class)
            <x-partials.card class="mt-5">
                <x-slot name="title"> Attachments </x-slot>

                <livewire:ticket-attachments-detail :ticket="$ticket" />
            </x-partials.card>
            @endcan @can('view-any', App\Models\Developer::class)
            <x-partials.card class="mt-5">
                <x-slot name="title"> Developers </x-slot>

                <livewire:ticket-developers-detail :ticket="$ticket" />
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

</div>


<!-- Component Start -->

