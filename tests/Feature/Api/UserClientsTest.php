<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Client;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserClientsTest extends TestCase
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
    public function it_gets_user_clients()
    {
        $user = User::factory()->create();
        $client = Client::factory()->create();

        $user->clients()->attach($client);

        $response = $this->getJson(route('api.users.clients.index', $user));

        $response->assertOk()->assertSee($client->name);
    }

    /**
     * @test
     */
    public function it_can_attach_clients_to_user()
    {
        $user = User::factory()->create();
        $client = Client::factory()->create();

        $response = $this->postJson(
            route('api.users.clients.store', [$user, $client])
        );

        $response->assertNoContent();

        $this->assertTrue(
            $user
                ->clients()
                ->where('clients.id', $client->id)
                ->exists()
        );
    }

    /**
     * @test
     */
    public function it_can_detach_clients_from_user()
    {
        $user = User::factory()->create();
        $client = Client::factory()->create();

        $response = $this->deleteJson(
            route('api.users.clients.store', [$user, $client])
        );

        $response->assertNoContent();

        $this->assertFalse(
            $user
                ->clients()
                ->where('clients.id', $client->id)
                ->exists()
        );
    }
}
