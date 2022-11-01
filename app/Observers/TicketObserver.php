<?php

namespace App\Observers;

use App\Models\Ticket;

class TicketObserver
{

    /**
     * Handle the Ticket "updated" event.
     *
     * @param  \App\Models\Ticket  $ticket
     * @return void
     */
    public function updating(Ticket $ticket)
    {
        if ($ticket->statu_id != 6) {
            $ticket->finished_ticket = null;
        }
    }

}
