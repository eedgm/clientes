<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Ticket;
use App\Models\Developer;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeveloperTicketsTest extends TestCase
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
    public function it_gets_developer_tickets()
    {
        $developer = Developer::factory()->create();
        $ticket = Ticket::factory()->create();

        $developer->tickets()->attach($ticket);

        $response = $this->getJson(
            route('api.developers.tickets.index', $developer)
        );

        $response->assertOk()->assertSee($ticket->description);
    }

    /**
     * @test
     */
    public function it_can_attach_tickets_to_developer()
    {
        $developer = Developer::factory()->create();
        $ticket = Ticket::factory()->create();

        $response = $this->postJson(
            route('api.developers.tickets.store', [$developer, $ticket])
        );

        $response->assertNoContent();

        $this->assertTrue(
            $developer
                ->tickets()
                ->where('tickets.id', $ticket->id)
                ->exists()
        );
    }

    /**
     * @test
     */
    public function it_can_detach_tickets_from_developer()
    {
        $developer = Developer::factory()->create();
        $ticket = Ticket::factory()->create();

        $response = $this->deleteJson(
            route('api.developers.tickets.store', [$developer, $ticket])
        );

        $response->assertNoContent();

        $this->assertFalse(
            $developer
                ->tickets()
                ->where('tickets.id', $ticket->id)
                ->exists()
        );
    }
}
