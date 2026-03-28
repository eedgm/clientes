<div>
    <a wire:click="seeVersions"><i class="bx bx-calculator"></i></a>

    <x-modal wire:model="showingVersionModal">
        <div class="px-6 py-6 text-left">
            <div class="flex flex-col gap-4 border-b border-gray-200 pb-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <div class="flex items-center gap-2 text-lg font-bold text-gray-900">
                        <i class="bx bx-calculator text-indigo-500"></i>
                        {{ $modalVersionTitle }}
                    </div>
                    <p class="mt-1 text-sm text-gray-500">
                        Resumen visual de las versiones calculadas para esta propuesta.
                    </p>
                </div>

                <button
                    type="button"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-indigo-700"
                    wire:click="addNewVersion"
                >
                    <i class="bx bx-plus text-base"></i>
                    New Version
                </button>
            </div>

            <div class="mt-5 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wider text-gray-600">
                            <tr>
                                <th class="px-4 py-3 text-left whitespace-nowrap">
                                    @lang('crud.proposal_versions.inputs.attachment')
                                </th>
                                <th class="px-4 py-3 text-left whitespace-nowrap">
                                    @lang('crud.proposal_versions.inputs.user_id')
                                </th>
                                <th class="px-4 py-3 text-right whitespace-nowrap">
                                    Horas totales
                                </th>
                                <th class="px-4 py-3 text-right whitespace-nowrap">
                                    Company Gain
                                </th>
                                <th class="px-4 py-3 text-right whitespace-nowrap">
                                    Company Gain %
                                </th>
                                <th class="px-4 py-3 text-right whitespace-nowrap">
                                    Seller Commission
                                </th>
                                <th class="px-4 py-3 text-right whitespace-nowrap">
                                    Seller Commission %
                                </th>
                                <th class="px-4 py-3 text-right whitespace-nowrap">
                                    @lang('crud.proposal_versions.inputs.months_to_pay')
                                </th>
                                <th class="px-4 py-3 text-right whitespace-nowrap">
                                    @lang('crud.proposal_versions.inputs.total')
                                </th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white text-sm text-gray-700">
                            @forelse ($versions as $version)
                            @php
                                $hours = (float) ($version->hours ?? 0);
                                $unexpectedPercentage = (float) ($version->unexpected ?? 0);
                                $totalHours = $hours + ($hours * ($unexpectedPercentage / 100));
                                $basePrice = $totalHours * (float) ($version->cost_per_hour ?? 0);
                                $companyGainPercentage = (float) ($version->company_gain ?? 0);
                                $companyGainAmount = $basePrice * ($companyGainPercentage / 100);
                                $priceWithGain = $basePrice + $companyGainAmount;
                                $priceWithBankTax = $priceWithGain + ((float) ($version->bank_tax ?? 0) * (float) ($version->months_to_pay ?? 0));
                                $sellerCommissionPercentage = (float) ($version->seller_commission_percentage ?? 0);
                                $sellerCommissionAmount = $priceWithBankTax * ($sellerCommissionPercentage / 100);
                            @endphp
                            <tr class="transition hover:bg-indigo-50/40">
                                <td class="px-4 py-4 text-left whitespace-nowrap">
                                    @if($version->attachment)
                                    <a
                                        href="{{ \Storage::url($version->attachment) }}"
                                        target="blank"
                                        class="inline-flex items-center gap-2 rounded-full bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-700 transition hover:bg-indigo-100"
                                        ><i class="icon ion-md-download"></i><span>See</span></a
                                    >
                                    @else
                                    <span class="rounded-full bg-gray-100 px-3 py-1 text-xs text-gray-500">Sin archivo</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-left whitespace-nowrap font-medium text-gray-900">
                                    {{ optional($version->user)->name ?? '-' }}
                                </td>
                                <td class="px-4 py-4 text-right whitespace-nowrap">
                                    {{ number_format($totalHours, 2) }}
                                </td>
                                <td class="px-4 py-4 text-right whitespace-nowrap font-medium text-emerald-700">
                                    $ {{ number_format($companyGainAmount, 2) }}
                                </td>
                                <td class="px-4 py-4 text-right whitespace-nowrap">
                                    <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                        {{ number_format($version->company_gain ?? 0, 2) }}%
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-right whitespace-nowrap font-medium text-amber-700">
                                    $ {{ number_format($sellerCommissionAmount, 2) }}
                                </td>
                                <td class="px-4 py-4 text-right whitespace-nowrap">
                                    <span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">
                                        {{ number_format($version->seller_commission_percentage ?? 0, 2) }}%
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-right whitespace-nowrap">
                                    {{ number_format($version->months_to_pay, 0) ?? '-' }}
                                </td>
                                <td class="px-4 py-4 text-right whitespace-nowrap font-semibold text-gray-900">
                                    $ {{ number_format($version->total ?? 0, 2) }}
                                </td>
                                <td class="px-4 py-4 text-right" style="width: 134px;">
                                    <div
                                        role="group"
                                        aria-label="Row Actions"
                                        class="relative inline-flex align-middle"
                                    >
                                        @can('update', $version)
                                        <button
                                            type="button"
                                            class="inline-flex items-center rounded-lg border border-gray-200 bg-white p-2 text-gray-600 shadow-sm transition hover:border-indigo-300 hover:text-indigo-600"
                                            wire:click="editVersion({{ $version->id }})"
                                        >
                                            <i class="bx bx-pencil"></i>
                                        </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-500">
                                        <i class="bx bx-spreadsheet text-4xl text-gray-300"></i>
                                        <p class="mt-3 text-sm font-medium">Todavía no hay versiones cargadas.</p>
                                        <p class="mt-1 text-xs text-gray-400">Creá una nueva versión para empezar a calcular.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-5 flex justify-end border-t border-gray-200 pt-4">
                <button
                    type="button"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
                    wire:click="$toggle('showingVersionModal')"
                >
                    <i class="icon ion-md-close"></i>
                    Cerrar
                </button>
            </div>
        </div>
    </x-modal>

    <x-modal wire:model="showingModal" class="mx-10">
        <div class="px-6 py-6 text-left">
            <div class="flex flex-col gap-3 border-b border-gray-200 pb-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <div class="flex items-center gap-2 text-lg font-bold text-gray-900">
                        <i class="bx bx-calculator text-indigo-500"></i>
                        {{ $modalTitle }}
                    </div>
                    <p class="mt-1 text-sm text-gray-500">
                        Configurá la versión, responsables y revisá el cálculo en tiempo real.
                    </p>
                </div>

                <div class="inline-flex items-center rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">
                    {{ $editing ? 'Editando versión' : 'Nueva versión' }}
                </div>
            </div>

            <div class="mt-6 grid gap-6 xl:grid-cols-[minmax(0,1.35fr)_minmax(320px,0.9fr)]">
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-600">Datos de la versión</h3>
                    </div>

                    <div class="grid grid-cols-1 gap-4 text-sm md:grid-cols-2">
                    <x-inputs.group class="w-full">
                        <x-inputs.number
                            name="version.hours"
                            label="Hours"
                            wire:model="version.hours"
                            step="0.01"
                            placeholder="hours"
                            disabled
                            class="bg-gray-100 cursor-not-allowed"
                        ></x-inputs.number>
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.number
                            name="version.unexpected"
                            label="Unexpected"
                            wire:model="version.unexpected"
                            step="0.01"
                            placeholder="Unexpected"
                        ></x-inputs.number>
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.number
                            name="version.cost_per_hour"
                            label="Cost Per Hour"
                            wire:model="version.cost_per_hour"
                            max="255"
                            step="0.01"
                            placeholder="Cost Per Hour"
                        ></x-inputs.number>
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.number
                            name="version.company_gain"
                            label="Company Gain"
                            wire:model="version.company_gain"
                            step="0.01"
                            placeholder="Company Gain"
                        ></x-inputs.number>
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.number
                            name="version.seller_commission_percentage"
                            label="Seller Commission %"
                            wire:model="version.seller_commission_percentage"
                            step="0.01"
                            placeholder="Seller Commission %"
                        ></x-inputs.number>
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.number
                            name="version.bank_tax"
                            label="Bank Tax"
                            wire:model="version.bank_tax"
                            step="0.01"
                            placeholder="Bank Tax"
                        ></x-inputs.number>
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.number
                            name="version.months_to_pay"
                            label="Months To Pay"
                            wire:model="version.months_to_pay"
                            max="255"
                            step="0.01"
                            placeholder="Months To Pay"
                        ></x-inputs.number>
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.number
                            name="version.first_payment"
                            label="First Payment"
                            wire:model="version.first_payment"
                            step="0.01"
                            placeholder="First Payment"
                        ></x-inputs.number>
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.number
                            name="version.total"
                            label="Total"
                            wire:model="version.total"
                            max="255"
                            step="0.01"
                            placeholder="Total"
                        ></x-inputs.number>
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.number
                            name="version.time"
                            label="Time"
                            wire:model="versionTime"
                            max="255"
                        ></x-inputs.number>
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.number
                            name="version.hour_per_day"
                            label="Hour Per Day"
                            wire:model="version.hour_per_day"
                            max="255"
                            step="0.01"
                            placeholder="Hour Per Day"
                        ></x-inputs.number>
                    </x-inputs.group>

                    <x-inputs.group class="w-full col-span-2">
                        <x-inputs.partials.label
                            name="responsiblePersonId"
                            label="Responsables"
                        ></x-inputs.partials.label>

                        <div class="flex flex-col gap-2 md:flex-row">
                            <x-inputs.select
                                name="responsiblePersonId"
                                wire:model="responsiblePersonId"
                            >
                                <option value="">Seleccioná un responsable</option>
                                @foreach($usersForSelect as $person)
                                <option value="{{ $person->id }}">
                                    {{ optional($person->user)->name ?? $person->description }}
                                </option>
                                @endforeach
                            </x-inputs.select>

                            <button
                                type="button"
                                class="inline-flex items-center justify-center gap-2 rounded-lg border border-indigo-200 bg-indigo-50 px-4 py-2 text-sm font-medium text-indigo-700 transition hover:bg-indigo-100"
                                wire:click="addResponsible"
                            >
                                <i class="bx bx-plus"></i>
                                Agregar
                            </button>

                            <button
                                type="button"
                                class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
                                wire:click="newResponsible"
                            >
                                <i class="bx bx-user-plus"></i>
                                Nuevo
                            </button>
                        </div>

                        @error('responsiblePersonId')
                        @include('components.inputs.partials.error')
                        @enderror

                        <div class="mt-3 space-y-2">
                            @forelse($this->selectedResponsiblePeople as $person)
                            <div class="flex items-center justify-between rounded-xl border border-gray-200 bg-gray-50 px-3 py-2">
                                <div>
                                    <p class="font-medium text-gray-800">{{ optional($person->user)->name ?? $person->description }}</p>
                                    @if($person->description)
                                    <p class="text-xs text-gray-500">{{ $person->description }}</p>
                                    @endif
                                </div>
                                <button
                                    type="button"
                                    class="text-sm font-medium text-red-500 transition hover:text-red-600"
                                    wire:click="removeResponsible({{ $person->id }})"
                                >
                                    Quitar
                                </button>
                            </div>
                            @empty
                            <p class="rounded-xl border border-dashed border-gray-300 bg-gray-50 px-4 py-3 text-sm text-gray-500">
                                Agregá al menos un responsable antes de guardar.
                            </p>
                            @endforelse
                        </div>

                        @error('selectedPeople')
                        @include('components.inputs.partials.error')
                        @enderror
                    </x-inputs.group>

                    <x-inputs.group class="w-full">
                        <x-inputs.partials.label
                            name="versionAttachment"
                            label="Attachment"
                        ></x-inputs.partials.label
                        ><br />

                        <input
                            type="file"
                            name="versionAttachment"
                            id="versionAttachment{{ $uploadIteration }}"
                            wire:model="versionAttachment"
                            class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 file:mr-4 file:rounded-md file:border-0 file:bg-indigo-50 file:px-3 file:py-2 file:text-sm file:font-medium file:text-indigo-700 hover:file:bg-indigo-100"
                        />

                        @if($editing && $version->attachment)
                        <div class="mt-2">
                            <a
                                href="{{ \Storage::url($version->attachment) }}"
                                target="_blank"
                                class="inline-flex items-center gap-2 rounded-full bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-700 transition hover:bg-indigo-100"
                                ><i class="icon ion-md-download"></i
                                ><span>Download</span></a
                            >
                        </div>
                        @endif @error('versionAttachment')
                        @include('components.inputs.partials.error') @enderror
                    </x-inputs.group>
                </div>

                </div>

                <div class="space-y-5">
                    <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="flex items-center gap-2 text-sm font-semibold uppercase tracking-wide text-gray-600">
                        <i class="bx bx-line-chart text-emerald-500"></i>
                        Calculation
                    </div>
                    <table class="mt-4 w-full overflow-hidden rounded-xl text-sm">
                        <tr>
                            <th class="rounded-tl-xl border border-gray-200 bg-gray-50 p-3 text-left font-medium text-gray-700">Horas:</th>
                            <td class="border-b border-r border-gray-200 p-3 text-right font-medium text-gray-900">{{ $hours }}</td>
                        </tr>
                        <tr>
                            <th class="border border-gray-200 bg-gray-50 p-3 text-left font-medium text-gray-700">Imprevistos:</th>
                            <td class="border-b border-r border-gray-200 p-3 text-right">{{ $unexpected }}</td>
                        </tr>
                        <tr>
                            <th class="border border-gray-200 bg-gray-50 p-3 text-left font-medium text-gray-700">Horas totales:</th>
                            <td class="border-b border-r border-gray-200 p-3 text-right font-medium text-gray-900">{{ $total_hours }}</td>
                        </tr>
                        <tr>
                            <th class="border border-gray-200 bg-gray-50 p-3 text-left font-medium text-gray-700">Total sin extras:</th>
                            <td class="border-b border-r border-gray-200 p-3 text-right">$ {{ number_format($price, 2) }}</td>
                        </tr>
                        <tr>
                            <th class="border border-gray-200 bg-gray-50 p-3 text-left font-medium text-gray-700">Ganancia de la compañia:</th>
                            <td class="border-b border-r border-gray-200 p-3 text-right text-emerald-700">$ {{ number_format($company_gain, 2) }}</td>
                        </tr>
                        <tr>
                            <th class="border border-gray-200 bg-gray-50 p-3 text-left font-medium text-gray-700">Costo con ganancia:</th>
                            <td class="border-b border-r border-gray-200 p-3 text-right">$ {{ number_format($price_with_gain, 2) }}</td>
                        </tr>
                        <tr>
                            <th class="border border-gray-200 bg-gray-50 p-3 text-left font-medium text-gray-700">Costo con gasto bancario:</th>
                            <td class="border-b border-r border-gray-200 p-3 text-right">$ {{ number_format($price_with_bank_tax, 2) }}</td>
                        </tr>
                        <tr>
                            <th class="border border-gray-200 bg-gray-50 p-3 text-left font-medium text-gray-700">Comisión vendedor:</th>
                            <td class="border-b border-r border-gray-200 p-3 text-right text-amber-700">$ {{ number_format($seller_commission, 2) }}</td>
                        </tr>
                        <tr>
                            <th class="border border-gray-200 bg-gray-50 p-3 text-left font-medium text-gray-700">Total final:</th>
                            <td class="border-b border-r border-gray-200 bg-indigo-50 p-3 text-right font-semibold text-indigo-700">$ {{ number_format($total_with_seller_commission, 2) }}</td>
                        </tr>
                        <tr>
                            <th class="rounded-bl-xl border border-gray-200 bg-gray-50 p-3 text-left font-medium text-gray-700">Pago por mes:</th>
                            <td class="rounded-br-xl border-r border-b border-gray-200 p-3 text-right font-semibold text-gray-900">$ {{ number_format($price_month_divided, 2) }}</td>
                        </tr>
                    </table>
                    </div>
                </div>
            </div>

            @if ($receipts_count > 0)
                <div class="mt-6 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <table class="table w-full table-list table-striped-primary">
                    <thead class="border-b border-gray-200 bg-gray-50 text-gray-700">
                        <tr class="">
                            <th class="px-4 py-3 text-left">
                                @lang('crud.receipts.date_dashboard')
                            </th>
                            <th class="px-4 py-3 text-right">
                                #
                            </th>
                            <th class="px-4 py-3 text-left">
                                @lang('crud.receipts.inputs.client_id')
                            </th>
                            <th class="px-4 py-3 text-right lg:w-32">
                                @lang('crud.receipts.total')
                            </th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600">
                            @foreach ($receipts as $receipt)
                            <tr class="hover:bg-gray-100 odd:bg-white even:bg-blue-50">
                                <td class="px-4 py-3 text-left">
                                    {{ $receipt->real_date->format('M d') ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    @can('edit', $receipt)
                                        <x-links href="{{ route('receipts.edit', $receipt->id) }}">{{ $receipt->number ?? '-' }}</x-links>
                                    @else
                                        {{ $receipt->number ?? '-' }}
                                    @endcan
                                </td>
                                <td class="px-4 py-3 text-left">
                                    {{ optional($receipt->client)->name ?? '-'
                                    }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    $ {{
                                        $receipt->manual_value ?? ($receipt->tickets->sum('total')) + ($receipt->payables->sum('total'));
                                    }}
                                </td>
                            </tr>
                            @endforeach
                    </tbody>
                </table>
                </div>
            @else
                <div class="mt-6 rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-5">
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-lg bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-100"
                        wire:click="generateReceipts"
                    >
                        <i class="bx bx-receipt"></i>
                        Generate Receipts
                    </button>
                </div>
            @endif
        </div>

        <div class="mt-6 flex justify-between border-t border-gray-200 bg-gray-50 px-6 py-4">
            <button
                type="button"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-100"
                wire:click="$toggle('showingModal')"
            >
                <i class="mr-1 icon ion-md-close"></i>
                @lang('crud.common.cancel')
            </button>

            <button
                type="button"
                class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-indigo-700"
                wire:click="save"
            >
                <i class="mr-1 icon ion-md-save"></i>
                @lang('crud.common.save')
            </button>
        </div>
    </x-modal>

    <x-modal wire:model="showingResponsibleModal">
        <div class="px-6 py-4 text-left">
            <div class="text-lg font-bold">{{ $modalResponsibleTitle }}</div>

            <div class="mt-5 grid grid-cols-2 gap-4">
                <x-inputs.group class="w-full col-span-2">
                    <x-inputs.text
                        name="responsibleUser.name"
                        wire:model="responsibleUser.name"
                        label="Name"
                        maxlength="255"
                        placeholder="Name"
                        required
                    ></x-inputs.text>
                </x-inputs.group>

                <x-inputs.group class="w-full col-span-2">
                    <x-inputs.email
                        name="responsibleUser.email"
                        wire:model="responsibleUser.email"
                        label="Email"
                        maxlength="255"
                        placeholder="Email"
                        required
                    ></x-inputs.email>
                </x-inputs.group>

                <x-inputs.group class="w-full col-span-2">
                    <x-inputs.password
                        name="responsiblePassword"
                        wire:model="responsiblePassword"
                        label="Password"
                        maxlength="255"
                        placeholder="Password"
                        required
                    ></x-inputs.password>
                </x-inputs.group>

                <x-inputs.group class="w-full col-span-2">
                    <x-inputs.textarea
                        name="responsiblePerson.description"
                        label="Description"
                        wire:model="responsiblePerson.description"
                        maxlength="255"
                    ></x-inputs.textarea>
                </x-inputs.group>

                <x-inputs.group class="w-full">
                    <x-inputs.text
                        name="responsiblePerson.phone"
                        label="Phone"
                        wire:model="responsiblePerson.phone"
                        maxlength="255"
                        placeholder="Phone"
                    ></x-inputs.text>
                </x-inputs.group>

                <x-inputs.group class="w-full">
                    <x-inputs.text
                        name="responsiblePerson.skype"
                        label="Skype"
                        wire:model="responsiblePerson.skype"
                        maxlength="255"
                        placeholder="Skype"
                    ></x-inputs.text>
                </x-inputs.group>

                <x-inputs.group class="w-full col-span-2">
                    <x-inputs.select
                        name="responsiblePerson.rol_id"
                        label="Rol"
                        wire:model="responsiblePerson.rol_id"
                    >
                        <option value="" disabled>Please select the Rol</option>
                        @foreach($rolsForSelect as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </x-inputs.select>
                </x-inputs.group>
            </div>
        </div>

        <div class="flex justify-between px-6 py-4 bg-gray-50">
            <button
                type="button"
                class="button"
                wire:click="$toggle('showingResponsibleModal')"
            >
                <i class="mr-1 icon ion-md-close"></i>
                @lang('crud.common.cancel')
            </button>

            <button
                type="button"
                class="button button-primary"
                wire:click="saveResponsible"
            >
                <i class="mr-1 icon ion-md-save"></i>
                @lang('crud.common.save')
            </button>
        </div>
    </x-modal>
</div>
