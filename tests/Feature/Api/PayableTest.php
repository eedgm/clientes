<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Payable;

use App\Models\Product;
use App\Models\Receipt;
use App\Models\Supplier;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PayableTest extends TestCase
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
    public function it_gets_payables_list()
    {
        $payables = Payable::factory()
            ->count(5)
            ->create();

        $response = $this->getJson(route('api.payables.index'));

        $response->assertOk()->assertSee($payables[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_payable()
    {
        $data = Payable::factory()
            ->make()
            ->toArray();

        $response = $this->postJson(route('api.payables.store'), $data);

        $this->assertDatabaseHas('payables', $data);

        $response->assertStatus(201)->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_updates_the_payable()
    {
        $payable = Payable::factory()->create();

        $product = Product::factory()->create();
        $supplier = Supplier::factory()->create();
        $receipt = Receipt::factory()->create();

        $data = [
            'name' => $this->faker->name,
            'date' => $this->faker->date,
            'cost' => $this->faker->randomNumber(1),
            'margin' => $this->faker->randomNumber(1),
            'total' => $this->faker->randomFloat(2, 0, 9999),
            'supplier_id_reference' => $this->faker->text(255),
            'periodicity' => 'month',
            'product_id' => $product->id,
            'supplier_id' => $supplier->id,
            'receipt_id' => $receipt->id,
        ];

        $response = $this->putJson(
            route('api.payables.update', $payable),
            $data
        );

        $data['id'] = $payable->id;

        $this->assertDatabaseHas('payables', $data);

        $response->assertOk()->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_deletes_the_payable()
    {
        $payable = Payable::factory()->create();

        $response = $this->deleteJson(route('api.payables.destroy', $payable));

        $this->assertModelMissing($payable);

        $response->assertNoContent();
    }
}
