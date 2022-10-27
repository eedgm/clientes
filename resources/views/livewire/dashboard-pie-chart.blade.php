<x-partials.card class="w-4/12">
    <div class="h-80 ">
        <x-slot name="title">
            Tickets
        </x-slot>
        <livewire:livewire-pie-chart
                key="{{ $ticketsPieChartModel->reactiveKey() }}"
                :pie-chart-model="$ticketsPieChartModel"
            />
    </div>
</x-partials.card>

<x-partials.card class="w-4/12">
    <div class="h-80 ">
        <x-slot name="title">
            Tasks
        </x-slot>
        <livewire:livewire-pie-chart
                key="{{ $tasksPieChartModel->reactiveKey() }}"
                :pie-chart-model="$tasksPieChartModel"
            />
    </div>
</x-partials.card>
