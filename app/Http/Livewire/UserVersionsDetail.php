<?php

namespace App\Http\Livewire;

use App\Models\User;
use Livewire\Component;
use App\Models\Version;
use App\Models\Proposal;
use Livewire\WithPagination;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserVersionsDetail extends Component
{
    use WithPagination;
    use AuthorizesRequests;

    public User $user;
    public Version $version;
    public $proposalsForSelect = [];
    public $versionTime;

    public $selected = [];
    public $editing = false;
    public $allSelected = false;
    public $showingModal = false;

    public $modalTitle = 'New Version';

    protected $rules = [
        'version.proposal_id' => ['required', 'exists:proposals,id'],
        'version.attachment' => ['nullable', 'max:255', 'string'],
        'version.total' => ['required', 'numeric'],
        'versionTime' => ['required', 'date'],
        'version.cost_per_hour' => ['required', 'numeric'],
        'version.hour_per_day' => ['required', 'numeric'],
        'version.months_to_pay' => ['required', 'numeric'],
        'version.unexpected' => ['required', 'numeric'],
        'version.company_gain' => ['required', 'numeric'],
        'version.bank_tax' => ['required', 'numeric'],
        'version.first_payment' => ['required', 'numeric'],
    ];

    public function mount(User $user)
    {
        $this->user = $user;
        $this->proposalsForSelect = Proposal::pluck('product_name', 'id');
        $this->resetVersionData();
    }

    public function resetVersionData()
    {
        $this->version = new Version();

        $this->versionTime = null;
        $this->version->proposal_id = null;

        $this->dispatchBrowserEvent('refresh');
    }

    public function newVersion()
    {
        $this->editing = false;
        $this->modalTitle = trans('crud.user_versions.new_title');
        $this->resetVersionData();

        $this->showModal();
    }

    public function editVersion(Version $version)
    {
        $this->editing = true;
        $this->modalTitle = trans('crud.user_versions.edit_title');
        $this->version = $version;

        $this->versionTime = $this->version->time->format('Y-m-d');

        $this->dispatchBrowserEvent('refresh');

        $this->showModal();
    }

    public function showModal()
    {
        $this->resetErrorBag();
        $this->showingModal = true;
    }

    public function hideModal()
    {
        $this->showingModal = false;
    }

    public function save()
    {
        $this->validate();

        if (!$this->version->user_id) {
            $this->authorize('create', Version::class);

            $this->version->user_id = $this->user->id;
        } else {
            $this->authorize('update', $this->version);
        }

        $this->version->time = \Carbon\Carbon::parse($this->versionTime);

        $this->version->save();

        $this->hideModal();
    }

    public function destroySelected()
    {
        $this->authorize('delete-any', Version::class);

        Version::whereIn('id', $this->selected)->delete();

        $this->selected = [];
        $this->allSelected = false;

        $this->resetVersionData();
    }

    public function toggleFullSelection()
    {
        if (!$this->allSelected) {
            $this->selected = [];
            return;
        }

        foreach ($this->user->versions as $version) {
            array_push($this->selected, $version->id);
        }
    }

    public function render()
    {
        return view('livewire.user-versions-detail', [
            'versions' => $this->user->versions()->paginate(20),
        ]);
    }
}
