<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Proposals
        </h2>
    </x-slot>
    <div class="grid grid-cols-1 gap-4 m-5 sm:grid-cols-1 lg:grid-cols-3">
        @foreach ($proposals as $proposal)
            <x-partials.card>
                <x-slot name="title">
                    {{ $proposal->client->name }} / {{ $proposal->product_name }}
                </x-slot>

                <div>
                    {{ $proposal->description }}
                </div>

                <div>
                    <a href="{{ route('gantt', $proposal->id) }}"><i class="bx bx-objects-horizontal-left"></i></a>
                    <a href="{{ route('gantt', $proposal->id) }}"><i class="bx bx-calculator"></i></a>
                </div>

            </x-partials.card>
        @endforeach
    </div>
</x-app-layout>
