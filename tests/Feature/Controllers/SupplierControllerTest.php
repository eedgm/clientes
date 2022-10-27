<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\Supplier;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SupplierControllerTest extends TestCase
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
    public function it_displays_index_view_with_suppliers()
    {
        $suppliers = Supplier::factory()
            ->count(5)
            ->create();

        $response = $this->get(route('suppliers.index'));

        $response
            ->assertOk()
            ->assertViewIs('app.suppliers.index')
            ->assertViewHas('suppliers');
    }

    /**
     * @test
     */
    public function it_displays_create_view_for_supplier()
    {
        $response = $this->get(route('suppliers.create'));

        $response->assertOk()->assertViewIs('app.suppliers.create');
    }

    /**
     * @test
     */
    public function it_stores_the_supplier()
    {
        $data = Supplier::factory()
            ->make()
            ->toArray();

        $response = $this->post(route('suppliers.store'), $data);

        $this->assertDatabaseHas('suppliers', $data);

        $supplier = Supplier::latest('id')->first();

        $response->assertRedirect(route('suppliers.edit', $supplier));
    }

    /**
     * @test
     */
    public function it_displays_show_view_for_supplier()
    {
        $supplier = Supplier::factory()->create();

        $response = $this->get(route('suppliers.show', $supplier));

        $response
            ->assertOk()
            ->assertViewIs('app.suppliers.show')
            ->assertViewHas('supplier');
    }

    /**
     * @test
     */
    public function it_displays_edit_view_for_supplier()
    {
        $supplier = Supplier::factory()->create();

        $response = $this->get(route('suppliers.edit', $supplier));

        $response
            ->assertOk()
            ->assertViewIs('app.suppliers.edit')
            ->assertViewHas('supplier');
    }

    /**
     * @test
     */
    public function it_updates_the_supplier()
    {
        $supplier = Supplier::factory()->create();

        $data = [
            'name' => $this->faker->name,
            'url' => $this->faker->url,
            'description' => $this->faker->sentence(15),
            'login' => $this->faker->userName,
        ];

        $response = $this->put(route('suppliers.update', $supplier), $data);

        $data['id'] = $supplier->id;

        $this->assertDatabaseHas('suppliers', $data);

        $response->assertRedirect(route('suppliers.edit', $supplier));
    }

    /**
     * @test
     */
    public function it_deletes_the_supplier()
    {
        $supplier = Supplier::factory()->create();

        $response = $this->delete(route('suppliers.destroy', $supplier));

        $response->assertRedirect(route('suppliers.index'));

        $this->assertModelMissing($supplier);
    }
}
