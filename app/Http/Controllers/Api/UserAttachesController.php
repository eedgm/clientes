<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AttachCollection;
use App\Http\Resources\AttachResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserAttachesController extends Controller
{
    /**
     * @return Response
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
     * @return Response
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
