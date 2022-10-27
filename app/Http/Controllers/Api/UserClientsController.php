<?php
namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ClientCollection;

class UserClientsController extends Controller
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

        $clients = $user
            ->clients()
            ->search($search)
            ->latest()
            ->paginate();

        return new ClientCollection($clients);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User $user
     * @param \App\Models\Client $client
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, User $user, Client $client)
    {
        $this->authorize('update', $user);

        $user->clients()->syncWithoutDetaching([$client->id]);

        return response()->noContent();
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User $user
     * @param \App\Models\Client $client
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, User $user, Client $client)
    {
        $this->authorize('update', $user);

        $user->clients()->detach($client);

        return response()->noContent();
    }
}
