<?php

namespace App\Http\Controllers\Api;

use App\Models\Ticket;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\AttachmentResource;
use App\Http\Resources\AttachmentCollection;

class TicketAttachmentsController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Ticket $ticket
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Ticket $ticket)
    {
        $this->authorize('view', $ticket);

        $search = $request->get('search', '');

        $attachments = $ticket
            ->attachments()
            ->search($search)
            ->latest()
            ->paginate();

        return new AttachmentCollection($attachments);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Ticket $ticket
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Ticket $ticket)
    {
        $this->authorize('create', Attachment::class);

        $validated = $request->validate([
            'attachment' => ['file', 'max:1024', 'required'],
            'description' => ['nullable', 'max:255', 'string'],
            'user_id' => ['required', 'exists:users,id'],
        ]);

        if ($request->hasFile('attachment')) {
            $validated['attachment'] = $request
                ->file('attachment')
                ->store('public');
        }

        $attachment = $ticket->attachments()->create($validated);

        return new AttachmentResource($attachment);
    }
}
