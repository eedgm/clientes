<?php

namespace App\Http\Livewire;

use App\Models\Task;
use Livewire\Component;
use App\Models\Developer;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DeveloperTasksDetail extends Component
{
    use AuthorizesRequests;

    public Developer $developer;
    public Task $task;
    public $tasksForSelect = [];
    public $task_id = null;
    public $comments;
    public $assignations;
    public $gain;

    public $showingModal = false;
    public $modalTitle = 'New Task';

    protected $rules = [
        'task_id' => ['required', 'exists:tasks,id'],
        'comments' => ['nullable', 'max:255', 'string'],
        'assignations' => ['nullable', 'max:255', 'string'],
        'gain' => ['nullable', 'numeric'],
    ];

    public function mount(Developer $developer)
    {
        $this->developer = $developer;
        $this->tasksForSelect = Task::pluck('name', 'id');
        $this->resetTaskData();
    }

    public function resetTaskData()
    {
        $this->task = new Task();

        $this->task_id = null;
        $this->comments = null;
        $this->assignations = null;
        $this->gain = null;

        $this->dispatchBrowserEvent('refresh');
    }

    public function newTask()
    {
        $this->modalTitle = trans('crud.developer_tasks.new_title');
        $this->resetTaskData();

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

        $this->authorize('create', Task::class);

        $this->developer->tasks()->attach($this->task_id, [
            'comments' => $this->comments,
            'assignations' => $this->assignations,
            'gain' => $this->gain,
        ]);

        $this->hideModal();
    }

    public function detach($task)
    {
        $this->authorize('delete-any', Task::class);

        $this->developer->tasks()->detach($task);

        $this->resetTaskData();
    }

    public function render()
    {
        return view('livewire.developer-tasks-detail', [
            'developerTasks' => $this->developer
                ->tasks()
                ->withPivot(['comments', 'assignations', 'gain'])
                ->paginate(20),
        ]);
    }
}
