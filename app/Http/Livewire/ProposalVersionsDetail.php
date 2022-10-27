<?php

namespace App\Http\Livewire;

use App\Models\User;
use Livewire\Component;
use App\Models\Version;
use App\Models\Proposal;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProposalVersionsDetail extends Component
{
    use WithPagination;
    use WithFileUploads;
    use AuthorizesRequests;

    public Proposal $proposal;
    public Version $version;
    public $usersForSelect = [];
    public $versionAttachment;
    public $uploadIteration = 0;
    public $versionTime;

    public $selected = [];
    public $editing = false;
    public $allSelected = false;
    public $showingModal = false;

    public $modalTitle = 'New Version';

    protected $rules = [
        'versionAttachment' => ['file', 'max:1024', 'nullable'],
        'version.user_id' => ['required', 'exists:users,id'],
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

    public function mount(Proposal $proposal)
    {
        $this->proposal = $proposal;
        $this->usersForSelect = User::pluck('name', 'id');
        $this->resetVersionData();
    }

    public function resetVersionData()
    {
        $this->version = new Version();

        $this->versionAttachment = null;
        $this->versionTime = null;
        $this->version->user_id = null;

        $this->dispatchBrowserEvent('refresh');
    }

    public function newVersion()
    {
        $this->editing = false;
        $this->modalTitle = trans('crud.proposal_versions.new_title');
        $this->resetVersionData();

        $this->showModal();
    }

    public function editVersion(Version $version)
    {
        $this->editing = true;
        $this->modalTitle = trans('crud.proposal_versions.edit_title');
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

        $this->version->time = \Carbon\Carbon::parse($this->versionTime);

        $this->version->save();

        $this->uploadIteration++;

        $this->hideModal();
    }

    public function destroySelected()
    {
        $this->authorize('delete-any', Version::class);

        collect($this->selected)->each(function (string $id) {
            $version = Version::findOrFail($id);

            if ($version->attachment) {
                Storage::delete($version->attachment);
            }

            $version->delete();
        });

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

        foreach ($this->proposal->versions as $version) {
            array_push($this->selected, $version->id);
        }
    }

    public function render()
    {
        return view('livewire.proposal-versions-detail', [
            'versions' => $this->proposal->versions()->paginate(20),
        ]);
    }
}
