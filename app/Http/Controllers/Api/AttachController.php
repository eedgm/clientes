<?php

namespace App\Http\Controllers\Api;

use App\Models\Attach;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\AttachResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\AttachCollection;
use App\Http\Requests\AttachStoreRequest;
use App\Http\Requests\AttachUpdateRequest;

class AttachController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view-any', Attach::class);

        $search = $request->get('search', '');

        $attaches = Attach::search($search)
            ->latest()
            ->paginate();

        return new AttachCollection($attaches);
    }

    /**
     * @param \App\Http\Requests\AttachStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(AttachStoreRequest $request)
    {
        $this->authorize('create', Attach::class);

        $validated = $request->validated();
        if ($request->hasFile('attachment')) {
            $validated['attachment'] = $request
                ->file('attachment')
                ->store('public');
        }

        $attach = Attach::create($validated);

        return new AttachResource($attach);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Attach $attach
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Attach $attach)
    {
        $this->authorize('view', $attach);

        return new AttachResource($attach);
    }

    /**
     * @param \App\Http\Requests\AttachUpdateRequest $request
     * @param \App\Models\Attach $attach
     * @return \Illuminate\Http\Response
     */
    public function update(AttachUpdateRequest $request, Attach $attach)
    {
        $this->authorize('update', $attach);

        $validated = $request->validated();

        if ($request->hasFile('attachment')) {
            if ($attach->attachment) {
                Storage::delete($attach->attachment);
            }

            $validated['attachment'] = $request
                ->file('attachment')
                ->store('public');
        }

        $attach->update($validated);

        return new AttachResource($attach);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Attach $attach
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Attach $attach)
    {
        $this->authorize('delete', $attach);

        if ($attach->attachment) {
            Storage::delete($attach->attachment);
        }

        $attach->delete();

        return response()->noContent();
    }
}
