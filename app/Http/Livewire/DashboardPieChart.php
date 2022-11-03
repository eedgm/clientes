<?php

namespace App\Http\Livewire;

use App\Models\Task;
use App\Models\Statu;
use App\Models\Ticket;
use Livewire\Component;
use App\Models\Developer;
use Illuminate\Support\Facades\Auth;
use Asantibanez\LivewireCharts\Models\PieChartModel;

class DashboardPieChart extends Component
{

    public $developer;
    public $person;
    public $owner;
    public $user;
    public $status;
    public $model;
    public $tickets_show = null;

    public $showingModal = false;
    public $modalTitle = "Detalle";
    public $showDataLabels = true;

    public $colors = [
        'Created' => '#008000',
        'In Progress' => '#0000FF',
        'Completed' => '#90cdf4',
        'Waiting for client' => '#66DA26',
        'Late' => '#cbd5e0',
        'In Progress' => '#FF00FF',
        'Inactive' => '#FF0000',
    ];

    public function mount() {
        $this->user = Auth::user();
        $this->showingModal = false;
        if (!$this->userHasRole($this->user, 'super-admin')) {
            $this->developer = Developer::where('user_id', $this->user->id)->get();
        }
    }

    protected $listeners = [
        'onSliceClick' => 'handleOnSliceClick',
        'refreshComponent' => '$refresh'
    ];

    public function handleOnSliceClick($slice) {
        $this->status = $slice['extras']['status_id'];
        $this->model = $slice['extras']['model'];

        $this->showingModal = true;
    }

    private function userHasRole($user, $roles) {
        $rol = false;
        foreach ($user->roles as $role) {
            if ($role->name == $roles) {
                $rol = true;
            }
        }
        return $rol;
    }

    public function updateData($name, $id, $value) {
        $ticket = Ticket::where('id', $id)->first();
        if ($name == 'hours' && $value > 0) {
            $client_cost = $ticket->product->client->cost_per_hour;
            $total = $value * $client_cost;
            $ticket->hours = $value;
            $ticket->total = $total;
            $ticket->update();
        }
        if ($name == 'total' && $value > 0) {
            $ticket->total = $value;
            $ticket->hours = $value / $ticket->product->client->cost_per_hour;
            $ticket->update();
        }

        $this->dispatchBrowserEvent('refresh');
    }

    public function changeStatus($status, $id) {
        $ticket = Ticket::where('id', $id)->first();
        $ticket->statu_id = $status;
        $ticket->update();

        if ($status == 6)
            $this->emit('refreshComponent');
    }

    public function render()
    {
        $this->tickets_show = Ticket::where('statu_id', 1)->get();
        if ($this->model == 'tickets') {
            $this->tickets_show = Ticket::where('statu_id', $this->status)->where('receipt_id', null)->get();
        }

        $all_status = Statu::pluck('name', 'id');

        $tickets = Ticket::join('status', 'tickets.statu_id', '=', 'status.id')
            ->where('receipt_id', null)->where('statu_id', '!=', 6)
            ->select('status.id', 'status.name', Ticket::raw('count(statu_id) as total'))->groupBy('statu_id')->get();

        $ticketsPieChartModel = $tickets->reduce(function ($ticketsPieChartModel, $data, $key) {
                $type = $data->name;
                $value = $data->total;

                return $ticketsPieChartModel->addSlice($type, $value, $this->colors[$type], ['model' => 'tickets', 'status_id' => $data->id]);
            }, (new PieChartModel())
                ->withOnSliceClickEvent('onSliceClick')
                ->setDataLabelsEnabled($this->showDataLabels)
            );

        $tasks = Task::join('status', 'tasks.statu_id', '=', 'status.id')
            ->where('receipt_id', null)->where('statu_id', '!=', 6)
            ->select('status.id', 'status.name', Task::raw('count(statu_id) as total'))->groupBy('statu_id')->get();

        $tasksPieChartModel = $tasks->reduce(function ($tasksPieChartModel, $data, $key) {
                $type = $data->name;
                $value = $data->total;

                return $tasksPieChartModel->addSlice($type, $value, $this->colors[$type], ['model' => 'tasks', 'status_id' => $data->id]);
            }, (new PieChartModel())
                ->withOnSliceClickEvent('onSliceClick')
                ->setDataLabelsEnabled($this->showDataLabels)
            );

        return view('livewire.dashboard.dashboard-pie-chart',
            [
                'tasksPieChartModel' => $tasksPieChartModel,
                'ticketsPieChartModel' => $ticketsPieChartModel,
                'tickets_show' => $this->tickets_show,
                'all_status' => $all_status
            ]);
    }
}
