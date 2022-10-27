<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Payable;
use App\Models\Supplier;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SupplierPayablesTest extends TestCase
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
    public function it_gets_supplier_payables()
    {
        $supplier = Supplier::factory()->create();
        $payables = Payable::factory()
            ->count(2)
            ->create([
                'supplier_id' => $supplier->id,
            ]);

        $response = $this->getJson(
            route('api.suppliers.payables.index', $supplier)
        );

        $response->assertOk()->assertSee($payables[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_supplier_payables()
    {
        $supplier = Supplier::factory()->create();
        $data = Payable::factory()
            ->make([
                'supplier_id' => $supplier->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.suppliers.payables.store', $supplier),
            $data
        );

        $this->assertDatabaseHas('payables', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $payable = Payable::latest('id')->first();

        $this->assertEquals($supplier->id, $payable->supplier_id);
    }
}
