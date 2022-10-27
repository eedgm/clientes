<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Ticket;
use App\Models\Developer;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TicketDevelopersTest extends TestCase
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
    public function it_gets_ticket_developers()
    {
        $ticket = Ticket::factory()->create();
        $developer = Developer::factory()->create();

        $ticket->developers()->attach($developer);

        $response = $this->getJson(
            route('api.tickets.developers.index', $ticket)
        );

        $response->assertOk()->assertSee($developer->id);
    }

    /**
     * @test
     */
    public function it_can_attach_developers_to_ticket()
    {
        $ticket = Ticket::factory()->create();
        $developer = Developer::factory()->create();

        $response = $this->postJson(
            route('api.tickets.developers.store', [$ticket, $developer])
        );

        $response->assertNoContent();

        $this->assertTrue(
            $ticket
                ->developers()
                ->where('developers.id', $developer->id)
                ->exists()
        );
    }

    /**
     * @test
     */
    public function it_can_detach_developers_from_ticket()
    {
        $ticket = Ticket::factory()->create();
        $developer = Developer::factory()->create();

        $response = $this->deleteJson(
            route('api.tickets.developers.store', [$ticket, $developer])
        );

        $response->assertNoContent();

        $this->assertFalse(
            $ticket
                ->developers()
                ->where('developers.id', $developer->id)
                ->exists()
        );
    }
}
