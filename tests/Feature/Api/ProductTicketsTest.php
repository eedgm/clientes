<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Ticket;
use App\Models\Product;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductTicketsTest extends TestCase
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
    public function it_gets_product_tickets()
    {
        $product = Product::factory()->create();
        $tickets = Ticket::factory()
            ->count(2)
            ->create([
                'product_id' => $product->id,
            ]);

        $response = $this->getJson(
            route('api.products.tickets.index', $product)
        );

        $response->assertOk()->assertSee($tickets[0]->description);
    }

    /**
     * @test
     */
    public function it_stores_the_product_tickets()
    {
        $product = Product::factory()->create();
        $data = Ticket::factory()
            ->make([
                'product_id' => $product->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.products.tickets.store', $product),
            $data
        );

        $this->assertDatabaseHas('tickets', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $ticket = Ticket::latest('id')->first();

        $this->assertEquals($product->id, $ticket->product_id);
    }
}
