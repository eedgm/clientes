<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Ticket;
use App\Models\Receipt;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReceiptTicketsTest extends TestCase
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
    public function it_gets_receipt_tickets()
    {
        $receipt = Receipt::factory()->create();
        $tickets = Ticket::factory()
            ->count(2)
            ->create([
                'receipt_id' => $receipt->id,
            ]);

        $response = $this->getJson(
            route('api.receipts.tickets.index', $receipt)
        );

        $response->assertOk()->assertSee($tickets[0]->description);
    }

    /**
     * @test
     */
    public function it_stores_the_receipt_tickets()
    {
        $receipt = Receipt::factory()->create();
        $data = Ticket::factory()
            ->make([
                'receipt_id' => $receipt->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.receipts.tickets.store', $receipt),
            $data
        );

        $this->assertDatabaseHas('tickets', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $ticket = Ticket::latest('id')->first();

        $this->assertEquals($receipt->id, $ticket->receipt_id);
    }
}
