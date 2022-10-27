<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Icon;
use App\Models\Statu;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IconStatusTest extends TestCase
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
    public function it_gets_icon_status()
    {
        $icon = Icon::factory()->create();
        $status = Statu::factory()
            ->count(2)
            ->create([
                'icon_id' => $icon->id,
            ]);

        $response = $this->getJson(route('api.icons.status.index', $icon));

        $response->assertOk()->assertSee($status[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_icon_status()
    {
        $icon = Icon::factory()->create();
        $data = Statu::factory()
            ->make([
                'icon_id' => $icon->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.icons.status.store', $icon),
            $data
        );

        $this->assertDatabaseHas('status', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $statu = Statu::latest('id')->first();

        $this->assertEquals($icon->id, $statu->icon_id);
    }
}
