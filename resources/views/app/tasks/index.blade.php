<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            @lang('crud.tasks.index_title')
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-9xl sm:px-6 lg:px-8">
            <x-partials.card>
                <div class="mt-4 mb-5">
                    <div class="flex flex-wrap justify-between">
                        <div class="md:w-1/2">
                            <form>
                                <div class="flex items-center w-full">
                                    <x-inputs.text
                                        name="search"
                                        value="{{ $search ?? '' }}"
                                        placeholder="{{ __('crud.common.search') }}"
                                        autocomplete="off"
                                    ></x-inputs.text>

                                    <div class="ml-1">
                                        <button
                                            type="submit"
                                            class="button button-primary"
                                        >
                                            <i class="icon ion-md-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="text-right md:w-1/2">
                            @can('create', App\Models\Task::class)
                            <a
                                href="{{ route('tasks.create') }}"
                                class="button button-primary"
                            >
                                <i class="mr-1 icon ion-md-add"></i>
                                @lang('crud.common.create')
                            </a>
                            @endcan
                        </div>
                    </div>
                </div>

                <div class="block w-full overflow-auto scrolling-touch">
                    <table class="w-full max-w-full mb-4 bg-transparent">
                        <thead class="text-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left">
                                    @lang('crud.tasks.inputs.text')
                                </th>
                                <th class="px-4 py-3 text-right">
                                    @lang('crud.tasks.inputs.hours')
                                </th>
                                <th class="px-4 py-3 text-left">
                                    @lang('crud.tasks.inputs.statu_id')
                                </th>
                                <th class="px-4 py-3 text-left">
                                    @lang('crud.tasks.inputs.priority_id')
                                </th>
                                <th class="px-4 py-3 text-right">
                                    @lang('crud.tasks.inputs.real_hours')
                                </th>
                                <th class="px-4 py-3 text-left">
                                    @lang('crud.tasks.inputs.version_id')
                                </th>
                                <th class="px-4 py-3 text-left">
                                    @lang('crud.tasks.inputs.receipt_id')
                                </th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600">
                            @forelse($tasks as $task)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-left">
                                    {{ $task->text ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    {{ $task->hours ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-left">
                                    {{ optional($task->statu)->name ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-left">
                                    {{ optional($task->priority)->name ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    {{ $task->real_hours ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-left">
                                    {{ optional($task->version)->attachment ??
                                    '-' }}
                                </td>
                                <td class="px-4 py-3 text-left">
                                    {{ optional($task->receipt)->description ??
                                    '-' }}
                                </td>
                                <td
                                    class="px-4 py-3 text-center"
                                    style="width: 134px;"
                                >
                                    <div
                                        role="group"
                                        aria-label="Row Actions"
                                        class="relative inline-flex align-middle "
                                    >
                                        @can('update', $task)
                                        <a
                                            href="{{ route('tasks.edit', $task) }}"
                                            class="mr-1"
                                        >
                                            <button
                                                type="button"
                                                class="button"
                                            >
                                                <i
                                                    class="icon ion-md-create"
                                                ></i>
                                            </button>
                                        </a>
                                        @endcan @can('view', $task)
                                        <a
                                            href="{{ route('tasks.show', $task) }}"
                                            class="mr-1"
                                        >
                                            <button
                                                type="button"
                                                class="button"
                                            >
                                                <i class="icon ion-md-eye"></i>
                                            </button>
                                        </a>
                                        @endcan @can('delete', $task)
                                        <form
                                            action="{{ route('tasks.destroy', $task) }}"
                                            method="POST"
                                            onsubmit="return confirm('{{ __('crud.common.are_you_sure') }}')"
                                        >
                                            @csrf @method('DELETE')
                                            <button
                                                type="submit"
                                                class="button"
                                            >
                                                <i
                                                    class="text-red-600  icon ion-md-trash"
                                                ></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8">
                                    @lang('crud.common.no_items_found')
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="8">
                                    <div class="px-4 mt-10">
                                        {!! $tasks->render() !!}
                                    </div>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </x-partials.card>
        </div>
    </div>
</x-app-layout>
