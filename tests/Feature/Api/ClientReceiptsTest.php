<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Client;
use App\Models\Receipt;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClientReceiptsTest extends TestCase
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
    public function it_gets_client_receipts()
    {
        $client = Client::factory()->create();
        $receipts = Receipt::factory()
            ->count(2)
            ->create([
                'client_id' => $client->id,
            ]);

        $response = $this->getJson(
            route('api.clients.receipts.index', $client)
        );

        $response->assertOk()->assertSee($receipts[0]->description);
    }

    /**
     * @test
     */
    public function it_stores_the_client_receipts()
    {
        $client = Client::factory()->create();
        $data = Receipt::factory()
            ->make([
                'client_id' => $client->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.clients.receipts.store', $client),
            $data
        );

        $this->assertDatabaseHas('receipts', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $receipt = Receipt::latest('id')->first();

        $this->assertEquals($client->id, $receipt->client_id);
    }
}
