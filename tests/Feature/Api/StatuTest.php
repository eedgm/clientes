<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Statu;

use App\Models\Icon;
use App\Models\Color;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StatuTest extends TestCase
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
    public function it_gets_status_list()
    {
        $status = Statu::factory()
            ->count(5)
            ->create();

        $response = $this->getJson(route('api.status.index'));

        $response->assertOk()->assertSee($status[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_statu()
    {
        $data = Statu::factory()
            ->make()
            ->toArray();

        $response = $this->postJson(route('api.status.store'), $data);

        $this->assertDatabaseHas('status', $data);

        $response->assertStatus(201)->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_updates_the_statu()
    {
        $statu = Statu::factory()->create();

        $color = Color::factory()->create();
        $icon = Icon::factory()->create();

        $data = [
            'name' => $this->faker->name,
            'limit' => $this->faker->randomNumber(0),
            'color_id' => $color->id,
            'icon_id' => $icon->id,
        ];

        $response = $this->putJson(route('api.status.update', $statu), $data);

        $data['id'] = $statu->id;

        $this->assertDatabaseHas('status', $data);

        $response->assertOk()->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_deletes_the_statu()
    {
        $statu = Statu::factory()->create();

        $response = $this->deleteJson(route('api.status.destroy', $statu));

        $this->assertSoftDeleted($statu);

        $response->assertNoContent();
    }
}
