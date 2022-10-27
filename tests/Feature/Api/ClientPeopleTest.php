<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Client;
use App\Models\Person;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClientPeopleTest extends TestCase
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
    public function it_gets_client_people()
    {
        $client = Client::factory()->create();
        $people = Person::factory()
            ->count(2)
            ->create([
                'client_id' => $client->id,
            ]);

        $response = $this->getJson(route('api.clients.people.index', $client));

        $response->assertOk()->assertSee($people[0]->description);
    }

    /**
     * @test
     */
    public function it_stores_the_client_people()
    {
        $client = Client::factory()->create();
        $data = Person::factory()
            ->make([
                'client_id' => $client->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.clients.people.store', $client),
            $data
        );

        $this->assertDatabaseHas('people', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $person = Person::latest('id')->first();

        $this->assertEquals($client->id, $person->client_id);
    }
}
