<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Ticket;

use App\Models\Statu;
use App\Models\Person;
use App\Models\Product;
use App\Models\Receipt;
use App\Models\Priority;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TicketTest extends TestCase
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
    public function it_gets_tickets_list()
    {
        $tickets = Ticket::factory()
            ->count(5)
            ->create();

        $response = $this->getJson(route('api.tickets.index'));

        $response->assertOk()->assertSee($tickets[0]->description);
    }

    /**
     * @test
     */
    public function it_stores_the_ticket()
    {
        $data = Ticket::factory()
            ->make()
            ->toArray();

        $response = $this->postJson(route('api.tickets.store'), $data);

        $this->assertDatabaseHas('tickets', $data);

        $response->assertStatus(201)->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_updates_the_ticket()
    {
        $ticket = Ticket::factory()->create();

        $statu = Statu::factory()->create();
        $priority = Priority::factory()->create();
        $product = Product::factory()->create();
        $person = Person::factory()->create();
        $receipt = Receipt::factory()->create();

        $data = [
            'description' => $this->faker->text,
            'hours' => $this->faker->randomNumber(1),
            'total' => $this->faker->randomFloat(2, 0, 9999),
            'finished_ticket' => $this->faker->date,
            'comments' => $this->faker->text,
            'statu_id' => $statu->id,
            'priority_id' => $priority->id,
            'product_id' => $product->id,
            'person_id' => $person->id,
            'receipt_id' => $receipt->id,
        ];

        $response = $this->putJson(route('api.tickets.update', $ticket), $data);

        $data['id'] = $ticket->id;

        $this->assertDatabaseHas('tickets', $data);

        $response->assertOk()->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_deletes_the_ticket()
    {
        $ticket = Ticket::factory()->create();

        $response = $this->deleteJson(route('api.tickets.destroy', $ticket));

        $this->assertSoftDeleted($ticket);

        $response->assertNoContent();
    }
}
