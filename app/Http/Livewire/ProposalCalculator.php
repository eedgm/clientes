<?php

namespace App\Http\Livewire;

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
    ];

    public function mount(Proposal $proposal)
    {
        $this->showingModal = false;
        $this->showingVersionModal = false;
        $this->showingResponsibleModal = false;
        $this->proposal = $proposal;
        $this->rolsForSelect = Rol::pluck('name', 'id');
        $this->versions = $proposal->versions;
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
        $this->hours = $this->proposal->tasks->sum('hours');
        $this->version->hours = $this->hours;
        $this->version->seller_commission_percentage = 0;

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

        $this->uploadIteration++;

        $this->showingModal = false;

        $this->versions = Version::where('proposal_id', $this->proposal->id)->get();

        $this->dispatchBrowserEvent('refresh');
    }

    public function editVersion(Version $version)
    {
        $this->editing = true;
        $this->modalTitle = trans('crud.proposal_versions.edit_title');
        $this->version = $version;

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
        $this->price = $this->total_hours * $costPerHour;
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
