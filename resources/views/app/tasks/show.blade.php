<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @lang('crud.tasks.show_title')
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-9xl mx-auto sm:px-6 lg:px-8">
            <x-partials.card>
                <x-slot name="title">
                    <a href="{{ route('tasks.index') }}" class="mr-4"
                        ><i class="mr-1 icon ion-md-arrow-back"></i
                    ></a>
                </x-slot>

                <div class="mt-4 px-4">
                    <div class="mb-4">
                        <h5 class="font-medium text-gray-700">
                            @lang('crud.tasks.inputs.name')
                        </h5>
                        <span>{{ $task->name ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5 class="font-medium text-gray-700">
                            @lang('crud.tasks.inputs.hours')
                        </h5>
                        <span>{{ $task->hours ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5 class="font-medium text-gray-700">
                            @lang('crud.tasks.inputs.statu_id')
                        </h5>
                        <span>{{ optional($task->statu)->name ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5 class="font-medium text-gray-700">
                            @lang('crud.tasks.inputs.priority_id')
                        </h5>
                        <span
                            >{{ optional($task->priority)->name ?? '-' }}</span
                        >
                    </div>
                    <div class="mb-4">
                        <h5 class="font-medium text-gray-700">
                            @lang('crud.tasks.inputs.real_hours')
                        </h5>
                        <span>{{ $task->real_hours ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5 class="font-medium text-gray-700">
                            @lang('crud.tasks.inputs.version_id')
                        </h5>
                        <span
                            >{{ optional($task->version)->attachment ?? '-'
                            }}</span
                        >
                    </div>
                    <div class="mb-4">
                        <h5 class="font-medium text-gray-700">
                            @lang('crud.tasks.inputs.receipt_id')
                        </h5>
                        <span
                            >{{ optional($task->receipt)->description ?? '-'
                            }}</span
                        >
                    </div>
                </div>

                <div class="mt-10">
                    <a href="{{ route('tasks.index') }}" class="button">
                        <i class="mr-1 icon ion-md-return-left"></i>
                        @lang('crud.common.back')
                    </a>

                    @can('create', App\Models\Task::class)
                    <a href="{{ route('tasks.create') }}" class="button">
                        <i class="mr-1 icon ion-md-add"></i>
                        @lang('crud.common.create')
                    </a>
                    @endcan
                </div>
            </x-partials.card>

            @can('view-any', App\Models\Attach::class)
            <x-partials.card class="mt-5">
                <x-slot name="title"> Attaches </x-slot>

                <livewire:task-attaches-detail :task="$task" />
            </x-partials.card>
            @endcan @can('view-any', App\Models\developer_task::class)
            <x-partials.card class="mt-5">
                <x-slot name="title"> Developers </x-slot>

                <livewire:task-developers-detail :task="$task" />
            </x-partials.card>
            @endcan
        </div>
    </div>
</x-app-layout>
