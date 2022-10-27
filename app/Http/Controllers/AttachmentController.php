<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Ticket;
use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\AttachmentStoreRequest;
use App\Http\Requests\AttachmentUpdateRequest;

class AttachmentController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view-any', Attachment::class);

        $search = $request->get('search', '');

        $attachments = Attachment::search($search)
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view('app.attachments.index', compact('attachments', 'search'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->authorize('create', Attachment::class);

        $users = User::pluck('name', 'id');
        $tickets = Ticket::pluck('description', 'id');

        return view('app.attachments.create', compact('users', 'tickets'));
    }

    /**
     * @param \App\Http\Requests\AttachmentStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(AttachmentStoreRequest $request)
    {
        $this->authorize('create', Attachment::class);

        $validated = $request->validated();
        if ($request->hasFile('attachment')) {
            $validated['attachment'] = $request
                ->file('attachment')
                ->store('public');
        }

        $attachment = Attachment::create($validated);

        return redirect()
            ->route('attachments.edit', $attachment)
            ->withSuccess(__('crud.common.created'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Attachment $attachment
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Attachment $attachment)
    {
        $this->authorize('view', $attachment);

        return view('app.attachments.show', compact('attachment'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Attachment $attachment
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Attachment $attachment)
    {
        $this->authorize('update', $attachment);

        $users = User::pluck('name', 'id');
        $tickets = Ticket::pluck('description', 'id');

        return view(
            'app.attachments.edit',
            compact('attachment', 'users', 'tickets')
        );
    }

    /**
     * @param \App\Http\Requests\AttachmentUpdateRequest $request
     * @param \App\Models\Attachment $attachment
     * @return \Illuminate\Http\Response
     */
    public function update(
        AttachmentUpdateRequest $request,
        Attachment $attachment
    ) {
        $this->authorize('update', $attachment);

        $validated = $request->validated();
        if ($request->hasFile('attachment')) {
            if ($attachment->attachment) {
                Storage::delete($attachment->attachment);
            }

            $validated['attachment'] = $request
                ->file('attachment')
                ->store('public');
        }

        $attachment->update($validated);

        return redirect()
            ->route('attachments.edit', $attachment)
            ->withSuccess(__('crud.common.saved'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Attachment $attachment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Attachment $attachment)
    {
        $this->authorize('delete', $attachment);

        if ($attachment->attachment) {
            Storage::delete($attachment->attachment);
        }

        $attachment->delete();

        return redirect()
            ->route('attachments.index')
            ->withSuccess(__('crud.common.removed'));
    }
}
