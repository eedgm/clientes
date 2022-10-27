<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @lang('crud.tickets.show_title')
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-partials.card>
                <x-slot name="title">
                    <a href="{{ route('tickets.index') }}" class="mr-4"
                        ><i class="mr-1 icon ion-md-arrow-back"></i
                    ></a>
                </x-slot>

                <div class="mt-4 px-4">
                    <div class="mb-4">
                        <h5 class="font-medium text-gray-700">
                            @lang('crud.tickets.inputs.description')
                        </h5>
                        <span>{{ $ticket->description ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5 class="font-medium text-gray-700">
                            @lang('crud.tickets.inputs.statu_id')
                        </h5>
                        <span>{{ optional($ticket->statu)->name ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5 class="font-medium text-gray-700">
                            @lang('crud.tickets.inputs.priority_id')
                        </h5>
                        <span
                            >{{ optional($ticket->priority)->name ?? '-'
                            }}</span
                        >
                    </div>
                    <div class="mb-4">
                        <h5 class="font-medium text-gray-700">
                            @lang('crud.tickets.inputs.hours')
                        </h5>
                        <span>{{ $ticket->hours ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5 class="font-medium text-gray-700">
                            @lang('crud.tickets.inputs.total')
                        </h5>
                        <span>{{ $ticket->total ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5 class="font-medium text-gray-700">
                            @lang('crud.tickets.inputs.finished_ticket')
                        </h5>
                        <span>{{ $ticket->finished_ticket ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5 class="font-medium text-gray-700">
                            @lang('crud.tickets.inputs.comments')
                        </h5>
                        <span>{{ $ticket->comments ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5 class="font-medium text-gray-700">
                            @lang('crud.tickets.inputs.product_id')
                        </h5>
                        <span
                            >{{ optional($ticket->product)->name ?? '-' }}</span
                        >
                    </div>
                    <div class="mb-4">
                        <h5 class="font-medium text-gray-700">
                            @lang('crud.tickets.inputs.receipt_id')
                        </h5>
                        <span
                            >{{ optional($ticket->receipt)->description ?? '-'
                            }}</span
                        >
                    </div>
                    <div class="mb-4">
                        <h5 class="font-medium text-gray-700">
                            @lang('crud.tickets.inputs.person_id')
                        </h5>
                        <span
                            >{{ optional($ticket->person)->description ?? '-'
                            }}</span
                        >
                    </div>
                </div>

                <div class="mt-10">
                    <a href="{{ route('tickets.index') }}" class="button">
                        <i class="mr-1 icon ion-md-return-left"></i>
                        @lang('crud.common.back')
                    </a>

                    @can('create', App\Models\Ticket::class)
                    <a href="{{ route('tickets.create') }}" class="button">
                        <i class="mr-1 icon ion-md-add"></i>
                        @lang('crud.common.create')
                    </a>
                    @endcan
                </div>
            </x-partials.card>

            @can('view-any', App\Models\Attachment::class)
            <x-partials.card class="mt-5">
                <x-slot name="title"> Attachments </x-slot>

                <livewire:ticket-attachments-detail :ticket="$ticket" />
            </x-partials.card>
            @endcan @can('view-any', App\Models\developer_ticket::class)
            <x-partials.card class="mt-5">
                <x-slot name="title"> Developers </x-slot>

                <livewire:ticket-developers-detail :ticket="$ticket" />
            </x-partials.card>
            @endcan
        </div>
    </div>
</x-app-layout>
