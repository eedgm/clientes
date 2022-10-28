<?php

namespace App\Http\Livewire;

use App\Models\Task;
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

    public $colors = [
        'Created' => '#008000',
        'In Progress' => '#FF0000',
        'Completed' => '#90cdf4',
        'Waiting for client' => '#66DA26',
        'Late' => '#cbd5e0',
        'In Progress' => '#FF00FF',
    ];

    public function mount() {
        $this->user = Auth::user();
        if (!$this->userHasRole($this->user, 'super-admin')) {
            $this->developer = Developer::where('user_id', $this->user->id)->get();
        }
    }

    protected $listeners = [
        'onSliceClick' => 'handleOnSliceClick',
    ];

    public $showDataLabels = true;

    public function handleOnSliceClick($slice) {
        dump($slice);
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

    public function render()
    {
        $tickets = Ticket::join('status', 'tickets.statu_id', '=', 'status.id')
            ->select('status.name', Ticket::raw('count(statu_id) as total'))->groupBy('statu_id')->get();

        $ticketsPieChartModel = $tickets->reduce(function ($ticketsPieChartModel, $data, $key) {
                $type = $data->name;
                $value = $data->total;

                return $ticketsPieChartModel->addSlice($type, $value, $this->colors[$type]);
            }, (new PieChartModel())
                ->withOnSliceClickEvent('onSliceClick')
                ->setDataLabelsEnabled($this->showDataLabels)
            );

        $tasks = Task::join('status', 'tasks.statu_id', '=', 'status.id')
            ->select('status.name', Task::raw('count(statu_id) as total'))->groupBy('statu_id')->get();

        $tasksPieChartModel = $tasks->reduce(function ($tasksPieChartModel, $data, $key) {
                $type = $data->name;
                $value = $data->total;

                return $tasksPieChartModel->addSlice($type, $value, $this->colors[$type]);
            }, (new PieChartModel())
                ->withOnSliceClickEvent('onSliceClick')
                ->setDataLabelsEnabled($this->showDataLabels)
            );
        return view('livewire.dashboard-pie-chart', compact('tasksPieChartModel', 'ticketsPieChartModel'));
    }
}
