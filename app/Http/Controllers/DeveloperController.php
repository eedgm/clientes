<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeveloperQuickStoreRequest;
use App\Http\Requests\DeveloperStoreRequest;
use App\Http\Requests\DeveloperUpdateRequest;
use App\Models\Developer;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DeveloperController extends Controller
{
    /**
     * @return Response
     */
    public function index(Request $request)
    {
        $this->authorize('view-any', Developer::class);

        $search = $request->get('search', '');

        $developers = Developer::search($search)
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view('app.developers.index', compact('developers', 'search'));
    }

    /**
     * @return Response
     */
    public function create(Request $request)
    {
        $this->authorize('create', Developer::class);

        $users = User::pluck('name', 'id');
        $rols = Rol::pluck('name', 'id');

        return view('app.developers.create', compact('users', 'rols'));
    }

    /**
     * @return Response
     */
    public function store(DeveloperStoreRequest $request)
    {
        $this->authorize('create', Developer::class);

        $validated = $request->validated();

        $developer = Developer::create($validated);

        return redirect()
            ->route('developers.edit', $developer)
            ->withSuccess(__('crud.common.created'));
    }

    /**
     * @return Response
     */
    public function show(Request $request, Developer $developer)
    {
        $this->authorize('view', $developer);

        return view('app.developers.show', compact('developer'));
    }

    /**
     * @return Response
     */
    public function edit(Request $request, Developer $developer)
    {
        $this->authorize('update', $developer);

        $users = User::pluck('name', 'id');
        $rols = Rol::pluck('name', 'id');

        return view(
            'app.developers.edit',
            compact('developer', 'users', 'rols')
        );
    }

    /**
     * @return Response
     */
    public function update(
        DeveloperUpdateRequest $request,
        Developer $developer
    ) {
        $this->authorize('update', $developer);

        $validated = $request->validated();

        $developer->update($validated);

        return redirect()
            ->route('developers.edit', $developer)
            ->withSuccess(__('crud.common.saved'));
    }

    /**
     * @return Response
     */
    public function destroy(Request $request, Developer $developer)
    {
        $this->authorize('delete', $developer);

        $developer->delete();

        return redirect()
            ->route('developers.index')
            ->withSuccess(__('crud.common.removed'));
    }

    /**
     * JSON endpoint used by the gantt lightbox to find developers
     * by user name. Availability is "name only" by design.
     */
    public function search(Request $request)
    {
        $this->authorize('view-any', Developer::class);

        $name = trim((string) $request->get('q', $request->get('search', '')));

        $query = Developer::query()
            ->with('user:id,name,email')
            ->orderBy('id')
            ->limit(20);

        if ($name !== '') {
            $query->whereHas('user', function ($q) use ($name) {
                $q->where('name', 'like', "%{$name}%");
            });
        }

        $developers = $query
            ->get()
            ->map(function (Developer $developer) {
                $user = $developer->user;

                return [
                    'id' => $developer->id,
                    'name' => $user?->name ?? '',
                    'email' => $user?->email ?? '',
                    'rol_id' => $developer->rol_id,
                    'cost_per_hour' => $developer->cost_per_hour,
                ];
            })
            ->values();

        return response()->json([
            'data' => $developers,
        ]);
    }

    /**
     * Inline developer creation used by the gantt lightbox.
     *
     * Creates the underlying user when an email is provided and the
     * user does not exist yet, then creates the developer record.
     * Returns the new developer as a JSON payload shaped the same way
     * as the `search` endpoint so the UI can insert it seamlessly.
     */
    public function quickStore(DeveloperQuickStoreRequest $request)
    {
        $this->authorize('create', Developer::class);

        $data = $request->validated();

        $developer = DB::transaction(function () use ($data) {
            $user = User::where('email', $data['email'])->first();

            if (! $user) {
                $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make($data['password']),
                ]);
            }

            return Developer::create([
                'user_id' => $user->id,
                'rol_id' => (int) $data['rol_id'],
                'cost_per_hour' => $data['cost_per_hour'] ?? null,
            ]);
        });

        $developer->load('user:id,name,email');

        return response()->json([
            'data' => [
                'id' => $developer->id,
                'name' => $developer->user?->name ?? $data['name'],
                'email' => $developer->user?->email ?? $data['email'],
                'rol_id' => $developer->rol_id,
                'cost_per_hour' => $developer->cost_per_hour,
            ],
        ], 201);
    }
}
