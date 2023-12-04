<?php

namespace App\Http\Livewire;

use App\Models\Task;
use App\Models\Statu;
use Livewire\Component;
use App\Models\Priority;
use App\Models\Proposal;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class KanbanTasks extends Component
{
    use AuthorizesRequests;

    public $showingModal = false;
    public $newStatus;
    public $moveTask;
    public $colors = [];
    public $icons = [];
    public $task_client_id;
    public $status;
    public $proposal;

    public $taskStatusSelected = null;

    public $editing = false;
    public $modalTitle = 'New task';

    protected $listeners = ['refreshComponent' => '$refresh'];

    protected $rules = [
        'task.text' => ['required', 'string'],
        'task.statu_id' => ['required', 'exists:status,id'],
        'task.priority_id' => ['required', 'exists:priorities,id'],
        'task.hours' => ['nullable', 'numeric'],
        'task.total' => ['nullable', 'numeric'],
        'task.progress' => ['nullable', 'numeric'],
        'task.comments' => ['nullable', 'max:255', 'string'],
    ];

    public function mount(Proposal $proposal) {
        $this->proposal = $proposal;
        $this->showingModal = false;
        $this->statusForSelect = Statu::pluck('name', 'id');
        $this->prioritiesForSelect = Priority::pluck('name', 'id');
        $this->status = Statu::all();
        $this->colors = [
            1 => 'bg-blue-100',
            2 => 'bg-green-100',
            3 => 'bg-yellow-100',
            4 => 'bg-red-100',
            5 => 'bg-purple-100',
            6 => 'bg-sky-100'
        ];

        $this->icons = [
            1 => 'bx-file-blank text-blue-500',
            2 => 'bx-key text-green-500',
            3 => 'bx-alarm-off text-yellow-500',
            4 => 'bx-layer-minus text-red-500',
            5 => 'bx-bell text-purple-500',
            6 => 'bx-dollar text-sky-500'
        ];
    }

    public function addTask(Statu $status)
    {
        $this->task = new Task();
        $this->task->statu_id = $status->id;
        $this->task->progress = 0;
        $this->showingModal = true;
    }

    public function updated($name, $value)
    {
        if ($name == 'task.hours') {
            // $product = Product::where('id', $this->task->product_id)->first();
            // $this->task->total = $value ? $value * $product->client->cost_per_hour : '';
        }
    }

    public function edit(Task $task)
    {
        $this->editing = true;
        $this->task = $task;
        $this->task->progress = $this->task->progress ?? 0;
        $this->taskStatusSelected = $this->task->statu_id;
        $this->showingModal = true;
    }

    public function save()
    {
        $this->validate();

        $this->authorize('create', Task::class);

        $this->task->save();

        $this->showingModal = false;
    }

    public function delete(Task $task)
    {
        $this->authorize('delete-any', task::class);

        $task->delete();
    }

    public function onDragEnter($event, $status)
    {
        $this->newStatus = $status;
    }

    public function onDragEnd($event, task $task)
    {
        $task->statu_id = $this->newStatus;
        $task->save();
    }

    public function render()
    {
        $tasks = Task::whereNull('receipt_id')->where('proposal_id', $this->proposal->id)->get();
        return view('livewire.kanban-tasks', compact('tasks'));
    }
}
