<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Product;
use App\Models\Payable;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductPayablesTest extends TestCase
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
    public function it_gets_product_payables()
    {
        $product = Product::factory()->create();
        $payables = Payable::factory()
            ->count(2)
            ->create([
                'product_id' => $product->id,
            ]);

        $response = $this->getJson(
            route('api.products.payables.index', $product)
        );

        $response->assertOk()->assertSee($payables[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_product_payables()
    {
        $product = Product::factory()->create();
        $data = Payable::factory()
            ->make([
                'product_id' => $product->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.products.payables.store', $product),
            $data
        );

        $this->assertDatabaseHas('payables', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $payable = Payable::latest('id')->first();

        $this->assertEquals($product->id, $payable->product_id);
    }
}
