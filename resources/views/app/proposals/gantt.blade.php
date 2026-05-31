<x-proposal-layout>

    @push('styles')
        @vite('resources/css/proposal-gantt.css')
        <style>
            [x-cloak] { display: none !important; }
        </style>
    @endpush

    @push('scripts')
        @vite('resources/js/proposal-gantt.js')
    @endpush

    <x-slot name="header">
        <div class="w-full flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 shadow-lg shadow-indigo-500/30">
                    <i class="bx bx-gantt text-white text-lg"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-800 leading-tight">Projects</h2>
                    <p class="text-xs text-gray-500 font-medium">{{ $proposal->product_name }}</p>
                </div>
            </div>

            <div x-data="{ showTasksModal: false }" class="flex items-center gap-4">
                <button
                    type="button"
                    class="group inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-indigo-500 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/30 transition-all duration-200 hover:from-indigo-500 hover:to-indigo-400 hover:shadow-xl hover:shadow-indigo-500/40 hover:-translate-y-0.5 active:translate-y-0"
                    @click="showTasksModal = true"
                >
                    <i class="bx bx-list-check text-lg group-hover:rotate-6 transition-transform"></i>
                    Tasks
                    <span class="ml-1 rounded-full bg-white/20 px-2 py-0.5 text-xs">{{ $proposal->tasks->count() }}</span>
                </button>

                <livewire:proposal-calculator :proposal="$proposal" />

                <div
                    x-show="showTasksModal"
                    x-init="$watch('showTasksModal', value => { if (value) document.body.classList.add('overflow-y-hidden'); else document.body.classList.remove('overflow-y-hidden') })"
                    x-on:close.stop="showTasksModal = false"
                    x-on:keydown.escape.window="showTasksModal = false"
                    class="fixed inset-0 z-50 w-full px-4 py-6 overflow-auto scrolling-touch jetstream-modal sm:px-0"
                    style="display: none;"
                    x-cloak
                >
                    <div
                        x-show="showTasksModal"
                        x-on:click="showTasksModal = false"
                        x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="fixed inset-0"
                    >
                        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
                    </div>

                    <div
                        x-show="showTasksModal"
                        x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                        x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                        x-transition:leave-end="opacity-0 translate-y-8 scale-95"
                        class="relative mx-auto max-w-5xl overflow-hidden rounded-2xl bg-white shadow-2xl"
                    >
                        <div class="bg-gradient-to-r from-indigo-600 via-indigo-500 to-purple-500 px-8 py-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20 backdrop-blur-sm">
                                        <i class="bx bx-list-check text-2xl text-white"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold text-white">Todas las Tareas</h3>
                                        <p class="mt-1 text-indigo-100">{{ $proposal->product_name }}</p>
                                    </div>
                                </div>
                                <button
                                    type="button"
                                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/10 text-white transition-all duration-200 hover:bg-white/20 hover:rotate-90"
                                    @click="showTasksModal = false"
                                >
                                    <i class="bx bx-x text-xl"></i>
                                </button>
                            </div>
                        </div>

                        <div class="p-6">
                            @php
                                $totalHours = $proposal->tasks->sum('hours');
                                $totalRealHours = $proposal->tasks->sum('real_hours');
                                $completedTasks = $proposal->tasks->filter(fn($t) => $t->statu && $t->statu->name === 'completado')->count();
                            @endphp
                            <div class="mb-6 grid grid-cols-3 gap-4">
                                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 text-center">
                                    <div class="text-2xl font-bold text-gray-900">{{ $proposal->tasks->count() }}</div>
                                    <div class="text-sm text-gray-500">Total Tareas</div>
                                </div>
                                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 text-center">
                                    <div class="text-2xl font-bold text-indigo-600">{{ number_format($totalHours, 1) }}</div>
                                    <div class="text-sm text-gray-500">Horas Planificadas</div>
                                </div>
                                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 text-center">
                                    <div class="text-2xl font-bold {{ $totalRealHours > $totalHours ? 'text-amber-600' : 'text-emerald-600' }}">{{ number_format($totalRealHours, 1) }}</div>
                                    <div class="text-sm text-gray-500">Horas Reales</div>
                                </div>
                            </div>

                            <div class="overflow-hidden rounded-xl border border-gray-200">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Tarea</th>
                                            <th class="px-4 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-gray-600">Horas</th>
                                            <th class="px-4 py-3.5 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Estado</th>
                                            <th class="px-4 py-3.5 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Prioridad</th>
                                            <th class="px-4 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-gray-600">Reales</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 bg-white">
                                        @forelse ($proposal->tasks as $task)
                                        <tr class="group transition-colors duration-200 hover:bg-indigo-50/50">
                                            <td class="px-4 py-4">
                                                <div class="flex items-center gap-3">
                                                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600">
                                                        <i class="bx bx-task"></i>
                                                    </div>
                                                    <span class="font-medium text-gray-900">{{ $task->text ?? '-' }}</span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-4 text-right">
                                                <span class="font-mono text-sm font-medium text-gray-700">{{ $task->hours ?? '-' }}</span>
                                            </td>
                                            <td class="px-4 py-4 text-center">
                                                @if($task->statu)
                                                    @php
                                                        $statusColors = [
                                                            'completado' => 'bg-emerald-100 text-emerald-700 ring-emerald-200',
                                                            'en progreso' => 'bg-blue-100 text-blue-700 ring-blue-200',
                                                            'pendiente' => 'bg-gray-100 text-gray-700 ring-gray-200',
                                                        ];
                                                        $colorClass = $statusColors[strtolower($task->statu->name)] ?? 'bg-gray-100 text-gray-700 ring-gray-200';
                                                    @endphp
                                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset {{ $colorClass }}">
                                                        {{ $task->statu->name }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-4 text-center">
                                                @if($task->priority)
                                                    @php
                                                        $priorityColors = [
                                                            'alta' => 'bg-red-100 text-red-700 ring-red-200',
                                                            'media' => 'bg-amber-100 text-amber-700 ring-amber-200',
                                                            'baja' => 'bg-green-100 text-green-700 ring-green-200',
                                                        ];
                                                        $pColorClass = $priorityColors[strtolower($task->priority->name)] ?? 'bg-gray-100 text-gray-700 ring-gray-200';
                                                    @endphp
                                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset {{ $pColorClass }}">
                                                        {{ $task->priority->name }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-4 text-right">
                                                <span class="font-mono text-sm font-medium {{ $task->real_hours > $task->hours ? 'text-amber-600' : 'text-gray-700' }}">
                                                    {{ $task->real_hours ?? '-' }}
                                                </span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-16 text-center">
                                                <div class="flex flex-col items-center justify-center">
                                                    <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-100">
                                                        <i class="bx bx-task text-3xl text-gray-400"></i>
                                                    </div>
                                                    <p class="text-sm font-medium text-gray-500">No hay tareas para este proyecto</p>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 border-t border-gray-100 bg-gray-50 px-6 py-4">
                            <button
                                type="button"
                                class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition-all duration-200 hover:bg-gray-50 hover:shadow-md"
                                @click="showTasksModal = false"
                            >
                                <i class="bx bx-x"></i>
                                Cerrar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="gantt-page">
        <div class="mb-6 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2 text-sm font-medium text-gray-500">
                    <i class="bx bx-sliders text-indigo-500"></i>
                    <span>Filtros</span>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <div class="inline-flex items-center gap-2 rounded-xl border border-gray-200/80 bg-white px-4 py-2 shadow-sm">
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Zoom</span>
                    <div class="h-5 w-px bg-gray-200"></div>
                    <button type="button" class="group flex h-8 w-8 items-center justify-center rounded-lg border border-transparent text-gray-500 transition-all duration-200 hover:border-gray-200 hover:bg-gray-50 hover:text-indigo-600" data-gantt-zoom-step="-1">
                        <i class="bx bx-minus text-sm group-hover:scale-110 transition-transform"></i>
                    </button>
                    <select id="gantt-zoom-level" class="h-8 rounded-lg border-gray-200 bg-transparent px-3 text-sm font-medium text-gray-700 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-300 cursor-pointer" data-gantt-zoom-select>
                        @foreach ($ganttConfig['zoom_levels'] as $zoomLevel)
                            <option value="{{ $zoomLevel['key'] }}" @selected($ganttConfig['default_zoom'] === $zoomLevel['key'])>
                                {{ $zoomLevel['label'] }}
                            </option>
                        @endforeach
                    </select>
                    <button type="button" class="group flex h-8 w-8 items-center justify-center rounded-lg border border-transparent text-gray-500 transition-all duration-200 hover:border-gray-200 hover:bg-gray-50 hover:text-indigo-600" data-gantt-zoom-step="1">
                        <i class="bx bx-plus text-sm group-hover:scale-110 transition-transform"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="gantt-wrapper rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div id="gantt_here" style="height: {{ $ganttConfig['height'] }}px;"></div>
        </div>
    </div>

    <script type="application/json" id="gantt-config">@json($ganttConfig)</script>

</x-proposal-layout>
