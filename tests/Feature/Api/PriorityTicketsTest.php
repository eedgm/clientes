<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Ticket;
use App\Models\Priority;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PriorityTicketsTest extends TestCase
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
    public function it_gets_priority_tickets()
    {
        $priority = Priority::factory()->create();
        $tickets = Ticket::factory()
            ->count(2)
            ->create([
                'priority_id' => $priority->id,
            ]);

        $response = $this->getJson(
            route('api.priorities.tickets.index', $priority)
        );

        $response->assertOk()->assertSee($tickets[0]->description);
    }

    /**
     * @test
     */
    public function it_stores_the_priority_tickets()
    {
        $priority = Priority::factory()->create();
        $data = Ticket::factory()
            ->make([
                'priority_id' => $priority->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.priorities.tickets.store', $priority),
            $data
        );

        $this->assertDatabaseHas('tickets', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $ticket = Ticket::latest('id')->first();

        $this->assertEquals($priority->id, $ticket->priority_id);
    }
}
