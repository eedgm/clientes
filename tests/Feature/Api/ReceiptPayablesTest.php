<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Receipt;
use App\Models\Payable;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReceiptPayablesTest extends TestCase
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
    public function it_gets_receipt_payables()
    {
        $receipt = Receipt::factory()->create();
        $payables = Payable::factory()
            ->count(2)
            ->create([
                'receipt_id' => $receipt->id,
            ]);

        $response = $this->getJson(
            route('api.receipts.payables.index', $receipt)
        );

        $response->assertOk()->assertSee($payables[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_receipt_payables()
    {
        $receipt = Receipt::factory()->create();
        $data = Payable::factory()
            ->make([
                'receipt_id' => $receipt->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.receipts.payables.store', $receipt),
            $data
        );

        $this->assertDatabaseHas('payables', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $payable = Payable::latest('id')->first();

        $this->assertEquals($receipt->id, $payable->receipt_id);
    }
}
