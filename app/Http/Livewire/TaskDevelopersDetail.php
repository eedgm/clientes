<?php

namespace App\Http\Livewire;

use App\Models\Task;
use Livewire\Component;
use App\Models\Developer;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TaskDevelopersDetail extends Component
{
    use AuthorizesRequests;

    public Task $task;
    public Developer $developer;
    public $developersForSelect = [];
    public $developer_id = null;
    public $comments;
    public $assignations;
    public $gain;

    public $showingModal = false;
    public $modalTitle = 'New Developer';

    protected $rules = [
        'developer_id' => ['required', 'exists:developers,id'],
        'comments' => ['nullable', 'max:255', 'string'],
        'assignations' => ['nullable', 'max:255', 'string'],
        'gain' => ['nullable', 'numeric'],
    ];

    public function mount(Task $task)
    {
        $this->task = $task;
        $this->developersForSelect = Developer::pluck('id', 'id');
        $this->resetDeveloperData();
    }

    public function resetDeveloperData()
    {
        $this->developer = new Developer();

        $this->developer_id = null;
        $this->comments = null;
        $this->assignations = null;
        $this->gain = null;

        $this->dispatchBrowserEvent('refresh');
    }

    public function newDeveloper()
    {
        $this->modalTitle = trans('crud.task_developers.new_title');
        $this->resetDeveloperData();

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

        $this->authorize('create', Developer::class);

        $this->task->developers()->attach($this->developer_id, [
            'comments' => $this->comments,
            'assignations' => $this->assignations,
            'gain' => $this->gain,
        ]);

        $this->hideModal();
    }

    public function detach($developer)
    {
        $this->authorize('delete-any', Developer::class);

        $this->task->developers()->detach($developer);

        $this->resetDeveloperData();
    }

    public function render()
    {
        return view('livewire.task-developers-detail', [
            'taskDevelopers' => $this->task
                ->developers()
                ->withPivot(['comments', 'assignations', 'gain'])
                ->paginate(20),
        ]);
    }
}
