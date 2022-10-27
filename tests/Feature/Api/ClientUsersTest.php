<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Client;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClientUsersTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create(['email' => 'admin@admin.com']);

        Sanctum::actingAs($user, [], 'web');

        $this->seed(\Database\Seeders\PermissionsSeeder::class);

        $this->withoutExceptionHandling();
    }

    /**
     * @test
     */
    public function it_gets_client_users()
    {
        $client = Client::factory()->create();
        $user = User::factory()->create();

        $client->users()->attach($user);

        $response = $this->getJson(route('api.clients.users.index', $client));

        $response->assertOk()->assertSee($user->name);
    }

    /**
     * @test
     */
    public function it_can_attach_users_to_client()
    {
        $client = Client::factory()->create();
        $user = User::factory()->create();

        $response = $this->postJson(
            route('api.clients.users.store', [$client, $user])
        );

        $response->assertNoContent();

        $this->assertTrue(
            $client
                ->users()
                ->where('users.id', $user->id)
                ->exists()
        );
    }

    /**
     * @test
     */
    public function it_can_detach_users_from_client()
    {
        $client = Client::factory()->create();
        $user = User::factory()->create();

        $response = $this->deleteJson(
            route('api.clients.users.store', [$client, $user])
        );

        $response->assertNoContent();

        $this->assertFalse(
            $client
                ->users()
                ->where('users.id', $user->id)
                ->exists()
        );
    }
}
