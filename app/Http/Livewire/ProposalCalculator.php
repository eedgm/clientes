<?php

namespace App\Http\Livewire;

use App\Models\Receipt;
use App\Models\Version;
use Livewire\Component;
use App\Models\Proposal;
use Livewire\WithFileUploads;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProposalCalculator extends Component
{
    use WithFileUploads;
    use AuthorizesRequests;

    public Proposal $proposal;
    public Version $version;
    public $receipts;
    public $receipts_count;
    public $versionTime;

    public $versionAttachment;
    public $uploadIteration = 0;
    public $editing = false;
    public $usersForSelect = [];

    public $showingModal = false;
    public $modalTitle = "Calculator";
    public $modalVersionTitle = "Versions";
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
    ];

    public function mount(Proposal $proposal)
    {
        $this->showingModal = false;
        $this->showingVersionModal = false;
        $this->proposal = $proposal;
    }

    public function updated($name, $value)
    {
        switch ($name) {
            case 'version.cost_per_hour':
                $this->price = $value > 0 ? $this->total_hours * $value : 0;
                break;
            case 'version.company_gain':
                $this->company_gain = $value > 0 ? $this->price * ($value / 100) : 0;
                $this->price_with_gain = $this->price + $this->company_gain;
                break;
            case 'version.unexpected':
                $this->unexpected = $value > 0 ? $this->hours * ($value / 100) : 0;
                $this->total_hours = $this->hours + $this->unexpected;
                break;
            case 'version.bank_tax':
                $this->bank_tax = $value > 0 ? $value : 0;
                break;
            case 'version.months_to_pay':
                $this->price_with_bank_tax = $value > 0 ? $this->price_with_gain + ($this->bank_tax * $value) : 0;
                $this->price_month_divided = $value > 0 ? $this->price_with_bank_tax / $value : 0;
                $this->version->first_payment = $this->price_month_divided;
                $this->version->total = $this->price_with_bank_tax;
                break;
            case 'version.first_payment':

                break;
        }

        $this->dispatchBrowserEvent('refresh');
    }

    public function addNewVersion()
    {
        $this->version = new Version();
        $this->clearCalculator();
        $this->showingModal = true;
        $this->hours = $this->proposal->tasks->sum('hours');
        $this->version->hours = $this->hours;
    }

    public function save()
    {
        $this->validate();

        if (!$this->version->proposal_id) {
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

        $this->uploadIteration++;

        $this->showingModal = false;

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

        $this->calculateValues();

        $this->dispatchBrowserEvent('refresh');

        $this->showingModal = true;
    }

    private function calculateValues()
    {
        $this->unexpected = $this->version->hours * ($this->version->unexpected / 100);
        $this->total_hours = $this->version->hours + $this->unexpected;
        $this->price = $this->total_hours * $this->version->cost_per_hour;
        $this->company_gain = $this->price * ($this->version->company_gain / 100);
        $this->price_with_gain = $this->price + $this->company_gain;
        $this->price_with_bank_tax = $this->price_with_gain + ($this->version->bank_tax * $this->version->months_to_pay);
        $this->price_month_divided = $this->price_with_bank_tax / $this->version->months_to_pay;
    }

    private function clearCalculator()
    {
        $this->unexpected = 0;
        $this->total_hours = 0;
        $this->price = 0;
        $this->company_gain = 0;
        $this->price_with_gain = 0;
        $this->price_with_bank_tax = 0;
        $this->price_month_divided = 0;
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
            for ($i=0; $i < $number - 1; $i++) {
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
        $this->usersForSelect = $this->proposal->client->people;
        return view('livewire.proposal-calculator', ['proposal' => $this->proposal, 'hours' => $this->hours]);
    }
}
