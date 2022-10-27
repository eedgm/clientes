<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Skill;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SkillTest extends TestCase
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
    public function it_gets_skills_list()
    {
        $skills = Skill::factory()
            ->count(5)
            ->create();

        $response = $this->getJson(route('api.skills.index'));

        $response->assertOk()->assertSee($skills[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_skill()
    {
        $data = Skill::factory()
            ->make()
            ->toArray();

        $response = $this->postJson(route('api.skills.store'), $data);

        $this->assertDatabaseHas('skills', $data);

        $response->assertStatus(201)->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_updates_the_skill()
    {
        $skill = Skill::factory()->create();

        $data = [
            'name' => $this->faker->name,
        ];

        $response = $this->putJson(route('api.skills.update', $skill), $data);

        $data['id'] = $skill->id;

        $this->assertDatabaseHas('skills', $data);

        $response->assertOk()->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_deletes_the_skill()
    {
        $skill = Skill::factory()->create();

        $response = $this->deleteJson(route('api.skills.destroy', $skill));

        $this->assertModelMissing($skill);

        $response->assertNoContent();
    }
}
