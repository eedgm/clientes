<?php

namespace App\Http\Livewire;

use App\Models\Task;
use App\Models\Statu;
use Livewire\Component;
use App\Models\Version;
use App\Models\Receipt;
use App\Models\Priority;
use Livewire\WithPagination;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class VersionTasksDetail extends Component
{
    use WithPagination;
    use AuthorizesRequests;

    public Version $version;
    public Task $task;
    public $statusForSelect = [];
    public $prioritiesForSelect = [];
    public $receiptsForSelect = [];

    public $selected = [];
    public $editing = false;
    public $allSelected = false;
    public $showingModal = false;

    public $modalTitle = 'New Task';

    protected $rules = [
        'task.name' => ['required', 'max:255', 'string'],
        'task.hours' => ['required', 'numeric'],
        'task.statu_id' => ['required', 'exists:status,id'],
        'task.priority_id' => ['required', 'exists:priorities,id'],
        'task.real_hours' => ['nullable', 'numeric'],
        'task.receipt_id' => ['nullable', 'exists:receipts,id'],
    ];

    public function mount(Version $version)
    {
        $this->version = $version;
        $this->statusForSelect = Statu::pluck('name', 'id');
        $this->prioritiesForSelect = Priority::pluck('name', 'id');
        $this->receiptsForSelect = Receipt::pluck('description', 'id');
        $this->resetTaskData();
    }

    public function resetTaskData()
    {
        $this->task = new Task();

        $this->task->statu_id = null;
        $this->task->priority_id = null;
        $this->task->receipt_id = null;

        $this->dispatchBrowserEvent('refresh');
    }

    public function newTask()
    {
        $this->editing = false;
        $this->modalTitle = trans('crud.version_tasks.new_title');
        $this->resetTaskData();

        $this->showModal();
    }

    public function editTask(Task $task)
    {
        $this->editing = true;
        $this->modalTitle = trans('crud.version_tasks.edit_title');
        $this->task = $task;

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

        if (!$this->task->version_id) {
            $this->authorize('create', Task::class);

            $this->task->version_id = $this->version->id;
        } else {
            $this->authorize('update', $this->task);
        }

        $this->task->save();

        $this->hideModal();
    }

    public function destroySelected()
    {
        $this->authorize('delete-any', Task::class);

        Task::whereIn('id', $this->selected)->delete();

        $this->selected = [];
        $this->allSelected = false;

        $this->resetTaskData();
    }

    public function toggleFullSelection()
    {
        if (!$this->allSelected) {
            $this->selected = [];
            return;
        }

        foreach ($this->version->tasks as $task) {
            array_push($this->selected, $task->id);
        }
    }

    public function render()
    {
        return view('livewire.version-tasks-detail', [
            'tasks' => $this->version->tasks()->paginate(20),
        ]);
    }
}
