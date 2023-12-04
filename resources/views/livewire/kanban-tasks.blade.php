<div>
    <div class="hidden">
        <div class="text-blue-500 bg-yellow-100"></div>
        <div class="text-purple-500 bg-purple-100"></div>
        <div class="bg-sky-100 text-sky-500"></div>
    </div>
    <div class="antialiased sans-serif">
        <h1 class="w-full p-2 -mb-5 bg-cyan-200">Tasks for {{ $proposal->description }}</h1>
        <div class="flex flex-col min-h-screen">
            <div class="px-4 py-4 mx-auto">
                <div class="py-2 md:py-8">
                    <div class="flex pb-2 -mx-4 overflow-x-auto">
                        @foreach ($status as $st)
                        <div class="flex-shrink-0 w-1/3 px-4 md:w-1/4 lg:w-1/6">
                            <div class="pb-4 overflow-x-hidden overflow-y-auto border-t-8 border-red-400 rounded-lg shadow bg-gray-50" style="min-height: 100px">
                                <div class="sticky top-0 flex items-center justify-between px-4 py-2">
                                    <h2 class="font-medium text-gray-800"><i class="bx {{ $icons[$st->id] }}"></i> {{ $st->name }}</h2>
                                    <a wire:click.prevent="addTask({{ $st->id }})" href="#" class="inline-flex items-center text-sm font-medium">
                                        <i class="bx bx-plus"></i>
                                    </a>
                                </div>

                                <div class="px-4">
                                    <div @dragenter="$wire.onDragEnter(event, {{ $st->id }})" class="py-2 rounded-lg">
                                        @foreach ($tasks as $task)
                                            @if ($task->statu_id == $st->id)
                                                <div
                                                    class="p-2 mb-2 rounded-lg shadow {{ $colors[$st->id] }}"
                                                    draggable="true"
                                                    @dragend="$wire.onDragEnd(event, {{ $task->id }})"
                                                >
                                                    <div class="text-xs text-gray-800">{{ optional($task->proposal)->product_name }}</div>
                                                    <div class="text-gray-800">{{ Str::limit($task->text, 150) }}</div>
                                                    <hr class="h-px my-2 bg-gray-400 border-0" />
                                                    <div class="text-xs">Hours: {{ $task->hours ?? '-' }} / Cost: {{ $task->total ?? '-' }}</div>
                                                    <div class="w-full bg-gray-200 rounded-full dark:bg-gray-500">
                                                        <div class="bg-blue-600 text-xs font-medium text-blue-100 text-center p-0.5 leading-none rounded-full" style="width: {{ $task->progress ?? 0 }}%"> {{ $task->progress ?? 0 }}%</div>
                                                    </div>
                                                    <div class="text-right">
                                                        <i class="text-xs bx bx-pencil" wire:click="edit({{ $task->id }})"></i>
                                                        <button
                                                            onclick="confirm('Are you sure?') || event.stopImmediatePropagation()"
                                                            wire:click="delete({{ $task->id }})"
                                                        >
                                                            <i class="text-xs bx bx-trash"></i>
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
                    <x-inputs.group class="w-full">
                        <x-inputs.textarea
                            name="task.text"
                            label="Name"
                            wire:model="task.text"
                            maxlength="255"
                        ></x-inputs.textarea>
                    </x-inputs.group>

                    <x-inputs.group class="w-full md:w-1/3">
                        <x-inputs.select
                            name="task.statu_id"
                            label="Statu"
                            wire:model="task.statu_id"
                        >
                            <option value="null" disabled>Please select the Statu</option>
                            @foreach($statusForSelect as $value => $label)
                            <option value="{{ $value }}"  >{{ $label }}</option>
                            @endforeach
                        </x-inputs.select>
                    </x-inputs.group>

                    <x-inputs.group class="w-full md:w-1/3">
                        <x-inputs.select
                            name="task.priority_id"
                            label="Priority"
                            wire:model="task.priority_id"
                        >
                            <option value="null" disabled>Please select the Priority</option>
                            @foreach($prioritiesForSelect as $value => $label)
                            <option value="{{ $value }}"  >{{ $label }}</option>
                            @endforeach
                        </x-inputs.select>
                    </x-inputs.group>

                    @if ($taskStatusSelected == 6)

                    <x-inputs.group class="w-full md:w-1/3">
                        <x-inputs.date
                            name="taskFinishedtask"
                            label="Finished task"
                            wire:model="taskFinishedtask"
                            max="255"
                        ></x-inputs.date>
                    </x-inputs.group>
                    @endif

                    <x-inputs.group class="w-full md:w-1/3">
                        <x-inputs.number
                            name="task.hours"
                            label="Hours"
                            wire:model="task.hours"
                            max="255"
                            step="0.01"
                            placeholder="Hours"
                        ></x-inputs.number>
                    </x-inputs.group>

                    <x-inputs.group class="w-full md:w-1/3">
                        <x-inputs.number
                            name="task.total"
                            label="Total"
                            wire:model="task.total"
                            step="0.01"
                            placeholder="Total"
                        ></x-inputs.number>
                    </x-inputs.group>

                    <x-inputs.group class="w-full md:w-1/3">
                        <x-inputs.progress
                            name="task.progress"
                            label="Progress"
                            wire:model="task.progress"
                            placeholder="progress"
                        ></x-inputs.progress>
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.textarea
                            name="task.comments"
                            label="Comments"
                            wire:model="task.comments"
                        ></x-inputs.textarea>
                    </x-inputs.group>
                </div>
            </div>

            @if($editing)
            <div class="flex flex-wrap">
            @can('view-any', App\Models\Attach::class)
            <x-partials.card class="w-full mt-5 md:w-1/2">
                <x-slot name="title"> Attaches </x-slot>

                <livewire:task-attaches-detail :task="$task" />
            </x-partials.card>
            @endcan @can('view-any', App\Models\Developer::class)
            <x-partials.card class="w-full mt-5 md:w-1/2">
                <x-slot name="title"> Developers </x-slot>

                <livewire:task-developers-detail :task="$task" />
            </x-partials.card>
            @endcan
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


<!-- Component Start -->

