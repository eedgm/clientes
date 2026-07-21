<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClientCollection;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserClientsController extends Controller
{
    /**
     * @return Response
     */
    public function index(Request $request, User $user)
    {
        $this->authorize('view', $user);

        $search = $request->get('search', '');

        $clients = $user
            ->clients()
            ->search($search)
            ->latest()
            ->paginate();

        return new ClientCollection($clients);
    }

    /**
     * @return Response
     */
    public function store(Request $request, User $user, Client $client)
    {
        $this->authorize('update', $user);

        $user->clients()->syncWithoutDetaching([$client->id]);

        return response()->noContent();
    }

    /**
     * @return Response
     */
    public function destroy(Request $request, User $user, Client $client)
    {
        $this->authorize('update', $user);

        $user->clients()->detach($client);

        return response()->noContent();
    }
}
