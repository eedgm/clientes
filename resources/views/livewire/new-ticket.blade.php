<div>
    <div>
        @can('create', App\Models\Ticket::class)
        <button class="text-white" wire:click="createTicket">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 mt-1 icon icon-tabler icon-tabler-ticket" viewBox="0 0 26 26" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M15 5l0 2"></path>
                <path d="M15 11l0 2"></path>
                <path d="M15 17l0 2"></path>
                <path d="M5 5h14a2 2 0 0 1 2 2v3a2 2 0 0 0 0 4v3a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-3a2 2 0 0 0 0 -4v-3a2 2 0 0 1 2 -2"></path>
            </svg>
        </button>
        @endcan
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

                    <x-inputs.group class="w-full md:w-1/4">
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

                    <x-inputs.group class="w-full md:w-1/4">
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

                    <x-inputs.group class="w-full md:w-1/4">
                        <x-inputs.number
                            name="ticket.hours"
                            label="Hours"
                            wire:model="ticket.hours"
                            max="255"
                            step="0.01"
                            placeholder="Hours"
                        ></x-inputs.number>
                    </x-inputs.group>

                    <x-inputs.group class="w-full md:w-1/4">
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
                        <x-inputs.textarea
                            name="ticket.comments"
                            label="Comments"
                            wire:model="ticket.comments"
                            maxlength="255"
                        ></x-inputs.textarea>
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
</div>
