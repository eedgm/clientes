<?php

namespace App\Http\Livewire;

use App\Models\Task;
use App\Models\User;
use App\Models\Attach;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TaskAttachesDetail extends Component
{
    use WithPagination;
    use WithFileUploads;
    use AuthorizesRequests;

    public Task $task;
    public Attach $attach;
    public $usersForSelect = [];
    public $attachAttachment;
    public $uploadIteration = 0;

    public $selected = [];
    public $editing = false;
    public $allSelected = false;
    public $showingModal = false;

    public $modalTitle = 'New Attach';

    protected $rules = [
        'attachAttachment' => ['file', 'max:1024', 'required'],
        'attach.description' => ['nullable', 'max:255', 'string'],
        'attach.user_id' => ['required', 'exists:users,id'],
    ];

    public function mount(Task $task)
    {
        $this->task = $task;
        $this->usersForSelect = User::pluck('name', 'id');
        $this->resetAttachData();
    }

    public function resetAttachData()
    {
        $this->attach = new Attach();

        $this->attachAttachment = null;
        $this->attach->user_id = null;

        $this->dispatchBrowserEvent('refresh');
    }

    public function newAttach()
    {
        $this->editing = false;
        $this->modalTitle = trans('crud.task_attaches.new_title');
        $this->resetAttachData();

        $this->showModal();
    }

    public function editAttach(Attach $attach)
    {
        $this->editing = true;
        $this->modalTitle = trans('crud.task_attaches.edit_title');
        $this->attach = $attach;

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

        if (!$this->attach->task_id) {
            $this->authorize('create', Attach::class);

            $this->attach->task_id = $this->task->id;
        } else {
            $this->authorize('update', $this->attach);
        }

        if ($this->attachAttachment) {
            $this->attach->attachment = $this->attachAttachment->store(
                'public'
            );
        }

        $this->attach->save();

        $this->uploadIteration++;

        $this->hideModal();
    }

    public function destroySelected()
    {
        $this->authorize('delete-any', Attach::class);

        collect($this->selected)->each(function (string $id) {
            $attach = Attach::findOrFail($id);

            if ($attach->attachment) {
                Storage::delete($attach->attachment);
            }

            $attach->delete();
        });

        $this->selected = [];
        $this->allSelected = false;

        $this->resetAttachData();
    }

    public function toggleFullSelection()
    {
        if (!$this->allSelected) {
            $this->selected = [];
            return;
        }

        foreach ($this->task->attaches as $attach) {
            array_push($this->selected, $attach->id);
        }
    }

    public function render()
    {
        return view('livewire.task-attaches-detail', [
            'attaches' => $this->task->attaches()->paginate(20),
        ]);
    }
}
