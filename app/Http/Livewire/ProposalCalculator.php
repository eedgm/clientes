<?php

namespace App\Http\Livewire;

use App\Models\Developer;
use App\Models\Person;
use App\Models\Proposal;
use App\Models\Receipt;
use App\Models\Rol;
use App\Models\User;
use App\Models\Version;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProposalCalculator extends Component
{
    use AuthorizesRequests;
    use WithFileUploads;

    public Proposal $proposal;

    public Version $version;

    public $receipts;

    public $receipts_count;

    public $versionTime;

    public $versionAttachment;

    public $uploadIteration = 0;

    public $editing = false;

    public $usersForSelect = [];

    public $rolsForSelect = [];

    public $showingModal = false;

    public $showingResponsibleModal = false;

    public $modalTitle = 'Calculator';

    public $modalVersionTitle = 'Versions';

    public $modalResponsibleTitle = 'New Responsible';

    public $showingVersionModal = false;

    public $price = 0;

    public $hours = 0;

    public $unexpected = 0;

    public $total_hours = 0;

    public $company_gain = 0;

    public $price_with_gain = 0;

    public $price_month_divided = 0;

    public $price_with_bank_tax = 0;

    public $bank_tax = 0;

    public $seller_commission = 0;

    public $total_with_seller_commission = 0;

    public $responsiblePersonId = null;

    public $selectedPeople = [];

    public User $responsibleUser;

    public Person $responsiblePerson;

    public $responsiblePassword = null;

    public $versions;

    /**
     * Per-developer cost override for the current version, keyed by
     * developer id. Synced to the `developer_version` pivot on save
     * and applied with the same precedence as the calculator.
     */
    public $developerOverrides = [];

    protected $rules = [
        'versionAttachment' => ['file', 'max:1024', 'nullable'],
        'version.user_id' => ['required', 'exists:users,id'],
        'version.total' => ['required', 'numeric'],
        'version.time' => ['nullable', 'numeric'],
        'version.hours' => ['nullable', 'numeric'],
        'version.cost_per_hour' => ['required', 'numeric'],
        'version.hour_per_day' => ['required', 'numeric'],
        'version.months_to_pay' => ['required', 'numeric'],
        'version.unexpected' => ['required', 'numeric'],
        'version.company_gain' => ['required', 'numeric'],
        'version.bank_tax' => ['required', 'numeric'],
        'version.first_payment' => ['required', 'numeric'],
        'version.seller_commission_percentage' => ['nullable', 'numeric', 'min:0'],
        'selectedPeople' => ['required', 'array', 'min:1'],
        'selectedPeople.*' => ['required', 'exists:people,id'],
        'responsibleUser.name' => ['nullable', 'max:255', 'string'],
        'responsibleUser.email' => ['nullable', 'max:255', 'string', 'email'],
        'responsiblePassword' => ['nullable', 'max:255', 'string'],
        'responsiblePerson.description' => ['nullable', 'max:255', 'string'],
        'responsiblePerson.phone' => ['nullable', 'max:255', 'string'],
        'responsiblePerson.skype' => ['nullable', 'max:255', 'string'],
        'responsiblePerson.rol_id' => ['nullable', 'exists:rols,id'],
        'developerOverrides' => ['nullable', 'array'],
        'developerOverrides.*' => ['nullable', 'numeric', 'min:0'],
    ];

    public function mount(Proposal $proposal)
    {
        $this->showingModal = false;
        $this->showingVersionModal = false;
        $this->showingResponsibleModal = false;
        $this->proposal = $proposal;
        $this->rolsForSelect = Rol::pluck('name', 'id');
        $this->versions = $proposal->versions;
        $this->developerOverrides = [];
        $this->resetResponsibleData();
    }

    public function updated($name, $value)
    {
        if (in_array($name, [
            'version.hours',
            'version.unexpected',
            'version.cost_per_hour',
            'version.company_gain',
            'version.bank_tax',
            'version.months_to_pay',
            'version.seller_commission_percentage',
        ])) {
            $this->calculateValues();
        }

        $this->dispatchBrowserEvent('refresh');
    }

    /**
     * Recalculate when any per-developer cost override changes. Livewire
     * fires this for nested `wire:model` updates like
     * `developerOverrides.5`, which do not trigger `updated()`.
     */
    public function updatedDeveloperOverrides()
    {
        $this->calculateValues();

        $this->dispatchBrowserEvent('refresh');
    }

    public function addNewVersion()
    {
        $this->editing = false;
        $this->modalTitle = trans('crud.proposal_versions.new_title');
        $this->version = new Version;
        $this->clearCalculator();
        $this->selectedPeople = [];
        $this->responsiblePersonId = null;
        $this->receipts_count = 0;
        $this->showingModal = true;
        $this->hours = $this->proposalTotalHours();
        $this->version->hours = $this->hours;
        $this->version->seller_commission_percentage = 0;
        $this->developerOverrides = [];

        $this->calculateValues();
    }

    public function addResponsible()
    {
        if (! $this->responsiblePersonId) {
            $this->addError('responsiblePersonId', 'Seleccioná un responsable.');

            return;
        }

        $availablePeople = $this->availablePeople()->pluck('id')->map(function ($personId) {
            return (int) $personId;
        });

        if (! $availablePeople->contains((int) $this->responsiblePersonId)) {
            $this->addError('responsiblePersonId', 'El responsable seleccionado no es válido.');

            return;
        }

        $this->selectedPeople = collect($this->selectedPeople)
            ->push((int) $this->responsiblePersonId)
            ->unique()
            ->values()
            ->all();

        $this->responsiblePersonId = null;
        $this->resetValidation(['responsiblePersonId', 'selectedPeople']);
    }

    public function removeResponsible($personId)
    {
        $this->selectedPeople = collect($this->selectedPeople)
            ->reject(function ($selectedPersonId) use ($personId) {
                return (int) $selectedPersonId === (int) $personId;
            })
            ->values()
            ->all();
    }

    public function newResponsible()
    {
        $this->modalResponsibleTitle = 'Nuevo responsable';
        $this->resetResponsibleData();
        $this->resetValidation([
            'responsibleUser.name',
            'responsibleUser.email',
            'responsiblePassword',
            'responsiblePerson.description',
            'responsiblePerson.phone',
            'responsiblePerson.skype',
            'responsiblePerson.rol_id',
        ]);
        $this->showingResponsibleModal = true;

        $this->dispatchBrowserEvent('refresh');
    }

    public function saveResponsible()
    {
        $this->authorize('create', Person::class);

        $validated = $this->validate([
            'responsibleUser.name' => ['required', 'max:255', 'string'],
            'responsibleUser.email' => [
                'required',
                'max:255',
                'string',
                'email',
                'unique:users,email',
            ],
            'responsiblePassword' => ['required', 'max:255', 'string'],
            'responsiblePerson.description' => ['nullable', 'max:255', 'string'],
            'responsiblePerson.phone' => ['nullable', 'max:255', 'string'],
            'responsiblePerson.skype' => ['nullable', 'max:255', 'string'],
            'responsiblePerson.rol_id' => ['required', 'exists:rols,id'],
        ]);

        $newUser = User::create([
            'name' => $validated['responsibleUser']['name'],
            'email' => $validated['responsibleUser']['email'],
            'password' => Hash::make($this->responsiblePassword),
        ]);

        $newPerson = Person::create([
            'description' => $validated['responsiblePerson']['description'] ?? null,
            'phone' => $validated['responsiblePerson']['phone'] ?? null,
            'skype' => $validated['responsiblePerson']['skype'] ?? null,
            'rol_id' => (int) $validated['responsiblePerson']['rol_id'],
            'client_id' => $this->proposal->client_id,
            'user_id' => $newUser->id,
        ]);

        $this->selectedPeople = collect($this->selectedPeople)
            ->push((int) $newPerson->id)
            ->unique()
            ->values()
            ->all();

        $this->showingResponsibleModal = false;
        $this->responsiblePersonId = null;
        $this->resetValidation(['selectedPeople', 'responsiblePersonId']);
        $this->resetResponsibleData();

        $this->dispatchBrowserEvent('refresh');
    }

    public function save()
    {
        $this->assignVersionUserFromResponsible();

        $this->validate();

        $availablePeople = $this->availablePeople()->pluck('id')->map(function ($personId) {
            return (int) $personId;
        });
        $invalidSelectedPeople = collect($this->selectedPeople)
            ->map(function ($personId) {
                return (int) $personId;
            })
            ->diff($availablePeople);

        if ($invalidSelectedPeople->isNotEmpty()) {
            $this->addError('selectedPeople', 'Hay responsables inválidos para este cliente.');

            return;
        }

        if (! $this->version->proposal_id) {
            $this->authorize('create', Version::class);

            $this->version->proposal_id = $this->proposal->id;
        } else {
            $this->authorize('update', $this->version);
        }

        if ($this->versionAttachment) {
            $this->version->attachment = $this->versionAttachment->store(
                'public'
            );
        }

        $this->version->save();
        $this->syncResponsiblePeople();
        $this->syncDeveloperOverrides();

        $this->uploadIteration++;

        $this->showingModal = false;

        $this->versions = Version::where('proposal_id', $this->proposal->id)->get();

        $this->dispatchBrowserEvent('refresh');
    }

    public function editVersion(Version $version)
    {
        $this->editing = true;
        $this->modalTitle = trans('crud.proposal_versions.edit_title');
        $this->version = $version->load('developers');

        $this->receipts = $this->version->receipts;
        $this->receipts_count = $this->receipts->count();

        $this->hours = $version->hours;
        $this->selectedPeople = $this->version
            ->people()
            ->pluck('people.id')
            ->map(function ($personId) {
                return (int) $personId;
            })
            ->toArray();
        $this->responsiblePersonId = null;
        $this->developerOverrides = $this->version
            ->developers
            ->mapWithKeys(function ($developer) {
                return [
                    (int) $developer->id => $developer->pivot->cost_per_hour,
                ];
            })
            ->all();

        $this->calculateValues();

        $this->dispatchBrowserEvent('refresh');

        $this->showingModal = true;
    }

    private function calculateValues()
    {
        $hours = $this->toFloat($this->version->hours ?: $this->hours);
        $unexpectedPercentage = $this->toFloat($this->version->unexpected);
        $costPerHour = $this->toFloat($this->version->cost_per_hour);
        $companyGainPercentage = $this->toFloat($this->version->company_gain);
        $bankTax = $this->toFloat($this->version->bank_tax);
        $monthsToPay = $this->toFloat($this->version->months_to_pay);
        $sellerCommissionPercentage = $this->toFloat(
            $this->version->seller_commission_percentage
        );

        $this->version->hours = $hours;

        $this->unexpected = $hours * ($unexpectedPercentage / 100);
        $this->total_hours = $hours + $this->unexpected;

        $perDeveloperBaseCost = $this->perDeveloperAssignmentCost(
            $this->version,
            $this->proposal
        );

        $this->price = $perDeveloperBaseCost > 0
            ? $perDeveloperBaseCost
            : $this->total_hours * $costPerHour;

        $this->company_gain = $this->price * ($companyGainPercentage / 100);
        $this->price_with_gain = $this->price + $this->company_gain;
        $this->price_with_bank_tax = $this->price_with_gain +
            ($bankTax * $monthsToPay);
        $this->seller_commission = $this->price_with_bank_tax *
            ($sellerCommissionPercentage / 100);
        $this->total_with_seller_commission =
            $this->price_with_bank_tax + $this->seller_commission;
        $this->price_month_divided = $monthsToPay > 0
            ? $this->total_with_seller_commission / $monthsToPay
            : 0;

        $this->version->first_payment = $this->price_month_divided;
        $this->version->total = $this->total_with_seller_commission;
    }

    /**
     * Sum of (assignment.hours * effective developer cost) for every
     * developer assigned to a task of the proposal. The effective
     * cost prefers the per-version override and falls back to the
     * developer's base cost.
     *
     * Returns 0 when no task has developer assignment hours, so the
     * caller can keep the legacy "version cost_per_hour" math in
     * place.
     */
    private function perDeveloperAssignmentCost(
        Version $version,
        Proposal $proposal
    ): float {
        $overrides = $this->currentDeveloperOverrides();

        $baseQuery = $proposal->tasks()
            ->whereHas('developers', function ($query) {
                $query->where('developer_task.hours', '!=', null);
            });

        if ($baseQuery->doesntExist()) {
            return 0.0;
        }

        $total = 0.0;

        $proposal->tasks()->with('developers')->each(function ($task) use ($overrides, $version, &$total) {
            foreach ($task->developers as $developer) {
                $hours = $developer->pivot->hours;

                if ($hours === null) {
                    continue;
                }

                $effectiveCost = $this->effectiveDeveloperCost(
                    $developer,
                    $overrides,
                    (float) ($version->cost_per_hour ?? 0)
                );

                $total += (float) $hours * $effectiveCost;
            }
        });

        return $total;
    }

    /**
     * Per-developer internal-cost summary for the current proposal
     * and version.
     *
     * The effective hourly cost follows the documented precedence:
     * version-specific override → developer base cost → version
     * `cost_per_hour` legacy fallback. Subtotal is the developer's
     * accumulated proposal hours multiplied by the effective cost.
     */
    public function getDeveloperSummariesProperty(): Collection
    {
        $overrides = $this->currentDeveloperOverrides();
        $fallbackCost = (float) ($this->version->cost_per_hour ?? 0);

        $hoursByDeveloper = [];
        $hasAssignments = false;

        $this->proposal->tasks()->with('developers')->each(function ($task) use (&$hoursByDeveloper, &$hasAssignments) {
            foreach ($task->developers as $developer) {
                if ($developer->pivot->hours === null) {
                    continue;
                }

                $developerId = (int) $developer->id;
                $hoursByDeveloper[$developerId] =
                    ($hoursByDeveloper[$developerId] ?? 0.0) + (float) $developer->pivot->hours;
                $hasAssignments = true;
            }
        });

        if (! $hasAssignments) {
            return collect();
        }

        $developers = Developer::with('user:id,name')
            ->whereIn('id', array_keys($hoursByDeveloper))
            ->get()
            ->keyBy('id');

        return collect($hoursByDeveloper)
            ->map(function (float $hours, int $developerId) use ($developers, $overrides, $fallbackCost) {
                $developer = $developers->get($developerId);

                $baseCost = $developer ? (float) ($developer->cost_per_hour ?? 0) : 0.0;
                $overrideRaw = $overrides[$developerId] ?? null;
                $override = $overrideRaw === null || $overrideRaw === ''
                    ? null
                    : (float) $overrideRaw;

                $effectiveCost = $override ?? ($baseCost > 0 ? $baseCost : $fallbackCost);

                return [
                    'id' => $developerId,
                    'name' => optional($developer?->user)->name ?? '—',
                    'proposal_hours' => round($hours, 2),
                    'base_cost_per_hour' => $baseCost,
                    'version_cost_per_hour' => $override,
                    'effective_cost_per_hour' => (float) $effectiveCost,
                    'subtotal' => round($hours * (float) $effectiveCost, 2),
                ];
            })
            ->sortBy('name')
            ->values();
    }

    /**
     * Map of current override values keyed by developer id. Reads
     * from the form state when editing/creating a version so the
     * calculator reactively tracks user changes, and falls back to
     * the persisted pivot on the loaded version otherwise.
     */
    private function currentDeveloperOverrides(): array
    {
        if (is_array($this->developerOverrides) && ! empty($this->developerOverrides)) {
            return collect($this->developerOverrides)
                ->mapWithKeys(function ($value, $key) {
                    return [(int) $key => $value];
                })
                ->all();
        }

        if (! isset($this->version) || ! $this->version->exists) {
            return [];
        }

        return $this->version
            ->developers()
            ->get()
            ->mapWithKeys(function ($developer) {
                return [
                    (int) $developer->id => $developer->pivot->cost_per_hour,
                ];
            })
            ->all();
    }

    /**
     * Resolve the effective hourly cost for a developer following the
     * override → base → version fallback precedence.
     */
    private function effectiveDeveloperCost(
        $developer,
        array $overrides,
        float $fallbackCost
    ): float {
        $developerId = (int) $developer->id;
        $override = $overrides[$developerId] ?? null;

        if ($override !== null && $override !== '') {
            return (float) $override;
        }

        $base = (float) ($developer->cost_per_hour ?? 0);

        return $base > 0 ? $base : $fallbackCost;
    }

    /**
     * Effective proposal hours using the same fallback rule that the
     * gantt lightbox uses. Legacy tasks without assignment hours keep
     * their `tasks.hours` value.
     */
    private function proposalTotalHours(): float
    {
        $total = 0.0;
        $hasAssignmentHours = false;

        $this->proposal->tasks()->with('developers')->each(function ($task) use (&$total, &$hasAssignmentHours) {
            $sum = 0.0;
            $taskHasHours = false;

            foreach ($task->developers as $developer) {
                if ($developer->pivot->hours !== null) {
                    $sum += (float) $developer->pivot->hours;
                    $taskHasHours = true;
                }
            }

            if ($taskHasHours) {
                $total += $sum;
                $hasAssignmentHours = true;
            } else {
                $total += (float) $task->hours;
            }
        });

        return $hasAssignmentHours ? $total : (float) $this->proposal->tasks->sum('hours');
    }

    private function clearCalculator()
    {
        $this->unexpected = 0;
        $this->total_hours = 0;
        $this->price = 0;
        $this->company_gain = 0;
        $this->price_with_gain = 0;
        $this->price_with_bank_tax = 0;
        $this->seller_commission = 0;
        $this->total_with_seller_commission = 0;
        $this->price_month_divided = 0;
    }

    private function toFloat($value): float
    {
        return is_numeric($value) ? (float) $value : 0.0;
    }

    private function availablePeople(): Collection
    {
        return $this->proposal->client->people()->with('user')->get();
    }

    private function assignVersionUserFromResponsible(): void
    {
        $responsible = $this->selectedResponsiblePeople
            ->first(function ($person) {
                return ! is_null($person->user_id);
            });

        $this->version->user_id = $responsible?->user_id;
    }

    private function resetResponsibleData(): void
    {
        $this->responsibleUser = new User;
        $this->responsiblePerson = new Person;
        $this->responsiblePassword = null;
        $this->responsiblePerson->rol_id = null;
    }

    private function syncResponsiblePeople(): void
    {
        if (! $this->version->exists) {
            return;
        }

        $selectedPeople = collect($this->selectedPeople)
            ->map(function ($personId) {
                return (int) $personId;
            })
            ->unique()
            ->values();

        $currentPeople = $this->version
            ->people()
            ->pluck('people.id')
            ->map(function ($personId) {
                return (int) $personId;
            });

        $peopleToAttach = $selectedPeople->diff($currentPeople);
        $peopleToDetach = $currentPeople->diff($selectedPeople);

        foreach ($peopleToAttach as $personId) {
            $this->version->people()->attach($personId, ['comments' => '']);
        }

        if ($peopleToDetach->isNotEmpty()) {
            $this->version->people()->detach($peopleToDetach->all());
        }
    }

    /**
     * Persist the per-developer cost overrides for the current
     * version. Entries with an empty value clear the override and
     * fall back to the developer's base cost.
     */
    private function syncDeveloperOverrides(): void
    {
        if (! $this->version->exists) {
            return;
        }

        $overrides = collect($this->developerOverrides ?? [])
            ->mapWithKeys(function ($value, $key) {
                return [(int) $key => $value];
            })
            ->all();

        if (empty($overrides)) {
            return;
        }

        $sync = [];

        foreach ($overrides as $developerId => $cost) {
            $sync[$developerId] = $cost === null || $cost === ''
                ? ['cost_per_hour' => null]
                : ['cost_per_hour' => (float) $cost];
        }

        $this->version->developers()->syncWithoutDetaching($sync);
    }

    public function getSelectedResponsiblePeopleProperty(): Collection
    {
        $selectedPeople = collect($this->selectedPeople)
            ->map(function ($personId) {
                return (int) $personId;
            });

        return $this->availablePeople()
            ->whereIn('id', $selectedPeople)
            ->values();
    }

    public function generateReceipts()
    {
        $number = $this->version->months_to_pay;
        $first_payment = $this->version->first_payment;
        $total = $this->version->total;
        $subtotal = $total - $first_payment;
        if ($subtotal == 0) {
            Receipt::create([
                'number' => 0,
                'version_id' => $this->version->id,
                'client_id' => $this->version->proposal->client->id,
                'real_date' => date('Y-m-d'),
                'manual_value' => $total]
            );
        } else {
            Receipt::create([
                'number' => 0,
                'version_id' => $this->version->id,
                'client_id' => $this->version->proposal->client->id,
                'real_date' => date('Y-m-d'),
                'manual_value' => $first_payment]
            );
            for ($i = 0; $i < $number - 1; $i++) {
                Receipt::create([
                    'number' => 0,
                    'version_id' => $this->version->id,
                    'client_id' => $this->version->proposal->client->id,
                    'real_date' => date('Y-m-d'),
                    'manual_value' => $subtotal]
                );
            }
        }

        $this->receipts = Receipt::where('version_id', $this->version->id)->get();
        $this->receipts_count = $this->receipts->count();
    }

    public function seeVersions()
    {
        $this->showingVersionModal = true;
    }

    public function render()
    {
        $this->usersForSelect = $this->proposal->client->people()->with('user')->get();

        return view('livewire.proposal-calculator', ['proposal' => $this->proposal, 'hours' => $this->hours]);
    }
}
