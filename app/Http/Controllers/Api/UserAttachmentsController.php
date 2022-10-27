<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\AttachmentResource;
use App\Http\Resources\AttachmentCollection;

class UserAttachmentsController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, User $user)
    {
        $this->authorize('view', $user);

        $search = $request->get('search', '');

        $attachments = $user
            ->attachments()
            ->search($search)
            ->latest()
            ->paginate();

        return new AttachmentCollection($attachments);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, User $user)
    {
        $this->authorize('create', Attachment::class);

        $validated = $request->validate([
            'attachment' => ['file', 'max:1024', 'required'],
            'description' => ['nullable', 'max:255', 'string'],
            'ticket_id' => ['required', 'exists:tickets,id'],
        ]);

        if ($request->hasFile('attachment')) {
            $validated['attachment'] = $request
                ->file('attachment')
                ->store('public');
        }

        $attachment = $user->attachments()->create($validated);

        return new AttachmentResource($attachment);
    }
}
