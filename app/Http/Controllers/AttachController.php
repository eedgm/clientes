<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Models\Attach;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
            ->paginate(5)
            ->withQueryString();

        return view('app.attaches.index', compact('attaches', 'search'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->authorize('create', Attach::class);

        $tasks = Task::pluck('name', 'id');
        $users = User::pluck('name', 'id');

        return view('app.attaches.create', compact('tasks', 'users'));
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

        return redirect()
            ->route('attaches.edit', $attach)
            ->withSuccess(__('crud.common.created'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Attach $attach
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Attach $attach)
    {
        $this->authorize('view', $attach);

        return view('app.attaches.show', compact('attach'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Attach $attach
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Attach $attach)
    {
        $this->authorize('update', $attach);

        $tasks = Task::pluck('name', 'id');
        $users = User::pluck('name', 'id');

        return view('app.attaches.edit', compact('attach', 'tasks', 'users'));
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

        return redirect()
            ->route('attaches.edit', $attach)
            ->withSuccess(__('crud.common.saved'));
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

        return redirect()
            ->route('attaches.index')
            ->withSuccess(__('crud.common.removed'));
    }
}
