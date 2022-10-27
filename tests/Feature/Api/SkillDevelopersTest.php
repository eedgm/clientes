<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Skill;
use App\Models\Developer;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SkillDevelopersTest extends TestCase
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
    public function it_gets_skill_developers()
    {
        $skill = Skill::factory()->create();
        $developer = Developer::factory()->create();

        $skill->developers()->attach($developer);

        $response = $this->getJson(
            route('api.skills.developers.index', $skill)
        );

        $response->assertOk()->assertSee($developer->id);
    }

    /**
     * @test
     */
    public function it_can_attach_developers_to_skill()
    {
        $skill = Skill::factory()->create();
        $developer = Developer::factory()->create();

        $response = $this->postJson(
            route('api.skills.developers.store', [$skill, $developer])
        );

        $response->assertNoContent();

        $this->assertTrue(
            $skill
                ->developers()
                ->where('developers.id', $developer->id)
                ->exists()
        );
    }

    /**
     * @test
     */
    public function it_can_detach_developers_from_skill()
    {
        $skill = Skill::factory()->create();
        $developer = Developer::factory()->create();

        $response = $this->deleteJson(
            route('api.skills.developers.store', [$skill, $developer])
        );

        $response->assertNoContent();

        $this->assertFalse(
            $skill
                ->developers()
                ->where('developers.id', $developer->id)
                ->exists()
        );
    }
}
