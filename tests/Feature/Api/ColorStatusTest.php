<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Color;
use App\Models\Statu;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ColorStatusTest extends TestCase
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
    public function it_gets_color_status()
    {
        $color = Color::factory()->create();
        $status = Statu::factory()
            ->count(2)
            ->create([
                'color_id' => $color->id,
            ]);

        $response = $this->getJson(route('api.colors.status.index', $color));

        $response->assertOk()->assertSee($status[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_color_status()
    {
        $color = Color::factory()->create();
        $data = Statu::factory()
            ->make([
                'color_id' => $color->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.colors.status.store', $color),
            $data
        );

        $this->assertDatabaseHas('status', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $statu = Statu::latest('id')->first();

        $this->assertEquals($color->id, $statu->color_id);
    }
}
