<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\Ticket;

use App\Models\Statu;
use App\Models\Person;
use App\Models\Product;
use App\Models\Receipt;
use App\Models\Priority;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TicketControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(
            User::factory()->create(['email' => 'admin@admin.com'])
        );

        $this->seed(\Database\Seeders\PermissionsSeeder::class);

        $this->withoutExceptionHandling();
    }

    /**
     * @test
     */
    public function it_displays_index_view_with_tickets()
    {
        $tickets = Ticket::factory()
            ->count(5)
            ->create();

        $response = $this->get(route('tickets.index'));

        $response
            ->assertOk()
            ->assertViewIs('app.tickets.index')
            ->assertViewHas('tickets');
    }

    /**
     * @test
     */
    public function it_displays_create_view_for_ticket()
    {
        $response = $this->get(route('tickets.create'));

        $response->assertOk()->assertViewIs('app.tickets.create');
    }

    /**
     * @test
     */
    public function it_stores_the_ticket()
    {
        $data = Ticket::factory()
            ->make()
            ->toArray();

        $response = $this->post(route('tickets.store'), $data);

        $this->assertDatabaseHas('tickets', $data);

        $ticket = Ticket::latest('id')->first();

        $response->assertRedirect(route('tickets.edit', $ticket));
    }

    /**
     * @test
     */
    public function it_displays_show_view_for_ticket()
    {
        $ticket = Ticket::factory()->create();

        $response = $this->get(route('tickets.show', $ticket));

        $response
            ->assertOk()
            ->assertViewIs('app.tickets.show')
            ->assertViewHas('ticket');
    }

    /**
     * @test
     */
    public function it_displays_edit_view_for_ticket()
    {
        $ticket = Ticket::factory()->create();

        $response = $this->get(route('tickets.edit', $ticket));

        $response
            ->assertOk()
            ->assertViewIs('app.tickets.edit')
            ->assertViewHas('ticket');
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

        $response = $this->put(route('tickets.update', $ticket), $data);

        $data['id'] = $ticket->id;

        $this->assertDatabaseHas('tickets', $data);

        $response->assertRedirect(route('tickets.edit', $ticket));
    }

    /**
     * @test
     */
    public function it_deletes_the_ticket()
    {
        $ticket = Ticket::factory()->create();

        $response = $this->delete(route('tickets.destroy', $ticket));

        $response->assertRedirect(route('tickets.index'));

        $this->assertSoftDeleted($ticket);
    }
}
