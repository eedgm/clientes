<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Person;
use App\Models\Ticket;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PersonTicketsTest extends TestCase
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
    public function it_gets_person_tickets()
    {
        $person = Person::factory()->create();
        $tickets = Ticket::factory()
            ->count(2)
            ->create([
                'person_id' => $person->id,
            ]);

        $response = $this->getJson(route('api.people.tickets.index', $person));

        $response->assertOk()->assertSee($tickets[0]->description);
    }

    /**
     * @test
     */
    public function it_stores_the_person_tickets()
    {
        $person = Person::factory()->create();
        $data = Ticket::factory()
            ->make([
                'person_id' => $person->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.people.tickets.store', $person),
            $data
        );

        $this->assertDatabaseHas('tickets', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $ticket = Ticket::latest('id')->first();

        $this->assertEquals($person->id, $ticket->person_id);
    }
}
