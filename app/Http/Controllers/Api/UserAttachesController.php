<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\AttachResource;
use App\Http\Resources\AttachCollection;

class UserAttachesController extends Controller
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

        $attaches = $user
            ->attaches()
            ->search($search)
            ->latest()
            ->paginate();

        return new AttachCollection($attaches);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, User $user)
    {
        $this->authorize('create', Attach::class);

        $validated = $request->validate([
            'attachment' => ['file', 'max:1024', 'required'],
            'description' => ['nullable', 'max:255', 'string'],
            'task_id' => ['required', 'exists:tasks,id'],
        ]);

        if ($request->hasFile('attachment')) {
            $validated['attachment'] = $request
                ->file('attachment')
                ->store('public');
        }

        $attach = $user->attaches()->create($validated);

        return new AttachResource($attach);
    }
}
