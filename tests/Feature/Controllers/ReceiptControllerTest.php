<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\Receipt;

use App\Models\Client;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReceiptControllerTest extends TestCase
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
    public function it_displays_index_view_with_receipts()
    {
        $receipts = Receipt::factory()
            ->count(5)
            ->create();

        $response = $this->get(route('receipts.index'));

        $response
            ->assertOk()
            ->assertViewIs('app.receipts.index')
            ->assertViewHas('receipts');
    }

    /**
     * @test
     */
    public function it_displays_create_view_for_receipt()
    {
        $response = $this->get(route('receipts.create'));

        $response->assertOk()->assertViewIs('app.receipts.create');
    }

    /**
     * @test
     */
    public function it_stores_the_receipt()
    {
        $data = Receipt::factory()
            ->make()
            ->toArray();

        $response = $this->post(route('receipts.store'), $data);

        $this->assertDatabaseHas('receipts', $data);

        $receipt = Receipt::latest('id')->first();

        $response->assertRedirect(route('receipts.edit', $receipt));
    }

    /**
     * @test
     */
    public function it_displays_show_view_for_receipt()
    {
        $receipt = Receipt::factory()->create();

        $response = $this->get(route('receipts.show', $receipt));

        $response
            ->assertOk()
            ->assertViewIs('app.receipts.show')
            ->assertViewHas('receipt');
    }

    /**
     * @test
     */
    public function it_displays_edit_view_for_receipt()
    {
        $receipt = Receipt::factory()->create();

        $response = $this->get(route('receipts.edit', $receipt));

        $response
            ->assertOk()
            ->assertViewIs('app.receipts.edit')
            ->assertViewHas('receipt');
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

        $response = $this->put(route('receipts.update', $receipt), $data);

        $data['id'] = $receipt->id;

        $this->assertDatabaseHas('receipts', $data);

        $response->assertRedirect(route('receipts.edit', $receipt));
    }

    /**
     * @test
     */
    public function it_deletes_the_receipt()
    {
        $receipt = Receipt::factory()->create();

        $response = $this->delete(route('receipts.destroy', $receipt));

        $response->assertRedirect(route('receipts.index'));

        $this->assertSoftDeleted($receipt);
    }
}
