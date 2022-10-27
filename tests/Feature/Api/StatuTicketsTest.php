<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Statu;
use App\Models\Ticket;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StatuTicketsTest extends TestCase
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
    public function it_gets_statu_tickets()
    {
        $statu = Statu::factory()->create();
        $tickets = Ticket::factory()
            ->count(2)
            ->create([
                'statu_id' => $statu->id,
            ]);

        $response = $this->getJson(route('api.status.tickets.index', $statu));

        $response->assertOk()->assertSee($tickets[0]->description);
    }

    /**
     * @test
     */
    public function it_stores_the_statu_tickets()
    {
        $statu = Statu::factory()->create();
        $data = Ticket::factory()
            ->make([
                'statu_id' => $statu->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.status.tickets.store', $statu),
            $data
        );

        $this->assertDatabaseHas('tickets', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $ticket = Ticket::latest('id')->first();

        $this->assertEquals($statu->id, $ticket->statu_id);
    }
}
