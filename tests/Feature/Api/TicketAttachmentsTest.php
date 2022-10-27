<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Ticket;
use App\Models\Attachment;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TicketAttachmentsTest extends TestCase
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
    public function it_gets_ticket_attachments()
    {
        $ticket = Ticket::factory()->create();
        $attachments = Attachment::factory()
            ->count(2)
            ->create([
                'ticket_id' => $ticket->id,
            ]);

        $response = $this->getJson(
            route('api.tickets.attachments.index', $ticket)
        );

        $response->assertOk()->assertSee($attachments[0]->attachment);
    }

    /**
     * @test
     */
    public function it_stores_the_ticket_attachments()
    {
        $ticket = Ticket::factory()->create();
        $data = Attachment::factory()
            ->make([
                'ticket_id' => $ticket->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.tickets.attachments.store', $ticket),
            $data
        );

        $this->assertDatabaseHas('attachments', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $attachment = Attachment::latest('id')->first();

        $this->assertEquals($ticket->id, $attachment->ticket_id);
    }
}
