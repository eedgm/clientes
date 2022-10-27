<?php

namespace App\Http\Controllers\Api;

use App\Models\Attachment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\AttachmentResource;
use App\Http\Resources\AttachmentCollection;
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
            ->paginate();

        return new AttachmentCollection($attachments);
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

        return new AttachmentResource($attachment);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Attachment $attachment
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Attachment $attachment)
    {
        $this->authorize('view', $attachment);

        return new AttachmentResource($attachment);
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

        return new AttachmentResource($attachment);
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

        return response()->noContent();
    }
}
