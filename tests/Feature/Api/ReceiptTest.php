<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Receipt;

use App\Models\Client;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReceiptTest extends TestCase
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
    public function it_gets_receipts_list()
    {
        $receipts = Receipt::factory()
            ->count(5)
            ->create();

        $response = $this->getJson(route('api.receipts.index'));

        $response->assertOk()->assertSee($receipts[0]->description);
    }

    /**
     * @test
     */
    public function it_stores_the_receipt()
    {
        $data = Receipt::factory()
            ->make()
            ->toArray();

        $response = $this->postJson(route('api.receipts.store'), $data);

        $this->assertDatabaseHas('receipts', $data);

        $response->assertStatus(201)->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_updates_the_receipt()
    {
        $receipt = Receipt::factory()->create();

        $client = Client::factory()->create();

        $data = [
            'number' => $this->faker->randomNumber,
            'description' => $this->faker->sentence(15),
            'real_date' => $this->faker->date,
            'charged' => $this->faker->boolean,
            'reference_charged' => $this->faker->text(255),
            'date_charged' => $this->faker->dateTime,
            'client_id' => $client->id,
        ];

        $response = $this->putJson(
            route('api.receipts.update', $receipt),
            $data
        );

        $data['id'] = $receipt->id;

        $this->assertDatabaseHas('receipts', $data);

        $response->assertOk()->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_deletes_the_receipt()
    {
        $receipt = Receipt::factory()->create();

        $response = $this->deleteJson(route('api.receipts.destroy', $receipt));

        $this->assertSoftDeleted($receipt);

        $response->assertNoContent();
    }
}
