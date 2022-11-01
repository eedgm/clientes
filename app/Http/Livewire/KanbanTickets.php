<?php

namespace App\Http\Livewire;

use App\Models\Statu;
use Livewire\Component;

class KanbanTickets extends Component
{


    public function render()
    {
        $status = Statu::pluck('name', 'id');
        return view('livewire.dashboard.kanban-tickets', compact('status'));
    }
}
