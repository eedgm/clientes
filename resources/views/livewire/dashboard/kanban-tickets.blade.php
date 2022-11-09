<div>
    <div class="antialiased sans-serif">
        <div class="flex flex-col min-h-screen">
            <div class="flex-1">

            <div class="container px-4 py-4 mx-auto">
                <div class="py-2 md:py-8">
                    <div class="flex pb-2 -mx-4 overflow-x-auto">
                        @foreach ($status as $st)
                        <div class="flex-shrink-0 w-1/2 px-4 md:w-1/4">
                            <div class="pb-4 overflow-x-hidden overflow-y-auto bg-gray-100 border-t-8 rounded-lg shadow" style="min-height: 100px">
                                <div class="sticky top-0 flex items-center justify-between px-4 py-2 bg-gray-100">
                                    <h2 class="font-medium text-gray-800">{{ $st->name }}</h2>
                                    <a wire:click.prevent="addTicket({{ $st->id }})" href="#" class="inline-flex items-center text-sm font-medium">
                                        <i class="bx bx-plus"></i>
                                        Add Task
                                    </a>
                                </div>

                                <div class="px-4">
                                    <div @dragover="onDragOver(event)" @drop="onDrop(event, board)" @dragenter="onDragEnter(event)" @dragleave="onDragLeave(event)" class="pt-2 pb-20 rounded-lg">
                                        @foreach ($tickets as $ticket)
                                            @if ($ticket->statu_id == $st->id)
                                            <div>
                                                <div class="p-2 mb-3 bg-white rounded-lg shadow" draggable="true" @dragstart="onDragStart(event, {{ $ticket->id }})">
                                                    <div class="text-xs text-gray-800">{{ $ticket->product->client->name }} / {{ $ticket->product->name }}</div>
                                                    <div class="text-gray-800">{{ $ticket->description }}</div>
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
                wire:click="$toggle('showingModal')"
            >
                <i class="mr-1 icon ion-md-save"></i>
                @lang('crud.common.save')
            </button>
        </div>
    </x-modal>

</div>


<!-- Component Start -->

