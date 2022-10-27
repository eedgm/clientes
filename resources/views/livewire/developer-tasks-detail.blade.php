<div>
    <div>
        @can('create', App\Models\Task::class)
        <button class="button" wire:click="newTask">
            <i class="mr-1 icon ion-md-add text-primary"></i>
            @lang('crud.common.attach')
        </button>
        @endcan
    </div>

    <x-modal wire:model="showingModal">
        <div class="px-6 py-4">
            <div class="text-lg font-bold">{{ $modalTitle }}</div>

            <div class="mt-5">
                <div>
                    <x-inputs.group class="w-full">
                        <x-inputs.select
                            name="task_id"
                            label="Task"
                            wire:model="task_id"
                        >
                            <option value="null" disabled>Please select the Task</option>
                            @foreach($tasksForSelect as $value => $label)
                            <option value="{{ $value }}"  >{{ $label }}</option>
                            @endforeach
                        </x-inputs.select>
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.textarea
                            name="comments"
                            label="Comments"
                            wire:model="comments"
                            maxlength="255"
                        ></x-inputs.textarea>
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.textarea
                            name="assignations"
                            label="Assignations"
                            wire:model="assignations"
                            maxlength="255"
                        ></x-inputs.textarea>
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.number
                            name="gain"
                            label="Gain"
                            wire:model="gain"
                            max="255"
                            step="0.01"
                            placeholder="Gain"
                        ></x-inputs.number>
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
                    <th class="px-4 py-3 text-left">
                        @lang('crud.developer_tasks.inputs.task_id')
                    </th>
                    <th class="px-4 py-3 text-left">
                        @lang('crud.developer_tasks.inputs.comments')
                    </th>
                    <th class="px-4 py-3 text-left">
                        @lang('crud.developer_tasks.inputs.assignations')
                    </th>
                    <th class="px-4 py-3 text-right">
                        @lang('crud.developer_tasks.inputs.gain')
                    </th>
                    <th></th>
                </tr>
            </thead>
            <tbody class="text-gray-600">
                @foreach ($developerTasks as $task)
                <tr class="hover:bg-gray-100">
                    <td class="px-4 py-3 text-left">
                        {{ $task->name ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-left">
                        {{ $task->pivot->comments ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-left">
                        {{ $task->pivot->assignations ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-right">
                        {{ $task->pivot->gain ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-right" style="width: 70px;">
                        <div
                            role="group"
                            aria-label="Row Actions"
                            class="relative inline-flex align-middle"
                        >
                            @can('delete-any', App\Models\Task::class)
                            <button
                                class="button button-danger"
                                onclick="confirm('Are you sure?') || event.stopImmediatePropagation()"
                                wire:click="detach({{ $task->id }})"
                            >
                                <i
                                    class="mr-1 icon ion-md-trash text-primary"
                                ></i>
                                @lang('crud.common.detach')
                            </button>
                            @endcan
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5">
                        <div class="mt-10 px-4">
                            {{ $developerTasks->render() }}
                        </div>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
