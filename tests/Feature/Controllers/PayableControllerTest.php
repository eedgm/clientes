<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\Payable;

use App\Models\Product;
use App\Models\Receipt;
use App\Models\Supplier;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PayableControllerTest extends TestCase
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
    public function it_displays_index_view_with_payables()
    {
        $payables = Payable::factory()
            ->count(5)
            ->create();

        $response = $this->get(route('payables.index'));

        $response
            ->assertOk()
            ->assertViewIs('app.payables.index')
            ->assertViewHas('payables');
    }

    /**
     * @test
     */
    public function it_displays_create_view_for_payable()
    {
        $response = $this->get(route('payables.create'));

        $response->assertOk()->assertViewIs('app.payables.create');
    }

    /**
     * @test
     */
    public function it_stores_the_payable()
    {
        $data = Payable::factory()
            ->make()
            ->toArray();

        $response = $this->post(route('payables.store'), $data);

        $this->assertDatabaseHas('payables', $data);

        $payable = Payable::latest('id')->first();

        $response->assertRedirect(route('payables.edit', $payable));
    }

    /**
     * @test
     */
    public function it_displays_show_view_for_payable()
    {
        $payable = Payable::factory()->create();

        $response = $this->get(route('payables.show', $payable));

        $response
            ->assertOk()
            ->assertViewIs('app.payables.show')
            ->assertViewHas('payable');
    }

    /**
     * @test
     */
    public function it_displays_edit_view_for_payable()
    {
        $payable = Payable::factory()->create();

        $response = $this->get(route('payables.edit', $payable));

        $response
            ->assertOk()
            ->assertViewIs('app.payables.edit')
            ->assertViewHas('payable');
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

        $response = $this->put(route('payables.update', $payable), $data);

        $data['id'] = $payable->id;

        $this->assertDatabaseHas('payables', $data);

        $response->assertRedirect(route('payables.edit', $payable));
    }

    /**
     * @test
     */
    public function it_deletes_the_payable()
    {
        $payable = Payable::factory()->create();

        $response = $this->delete(route('payables.destroy', $payable));

        $response->assertRedirect(route('payables.index'));

        $this->assertModelMissing($payable);
    }
}
