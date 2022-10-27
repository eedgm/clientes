<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Skill;
use App\Models\Developer;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeveloperSkillsTest extends TestCase
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
    public function it_gets_developer_skills()
    {
        $developer = Developer::factory()->create();
        $skill = Skill::factory()->create();

        $developer->skills()->attach($skill);

        $response = $this->getJson(
            route('api.developers.skills.index', $developer)
        );

        $response->assertOk()->assertSee($skill->name);
    }

    /**
     * @test
     */
    public function it_can_attach_skills_to_developer()
    {
        $developer = Developer::factory()->create();
        $skill = Skill::factory()->create();

        $response = $this->postJson(
            route('api.developers.skills.store', [$developer, $skill])
        );

        $response->assertNoContent();

        $this->assertTrue(
            $developer
                ->skills()
                ->where('skills.id', $skill->id)
                ->exists()
        );
    }

    /**
     * @test
     */
    public function it_can_detach_skills_from_developer()
    {
        $developer = Developer::factory()->create();
        $skill = Skill::factory()->create();

        $response = $this->deleteJson(
            route('api.developers.skills.store', [$developer, $skill])
        );

        $response->assertNoContent();

        $this->assertFalse(
            $developer
                ->skills()
                ->where('skills.id', $skill->id)
                ->exists()
        );
    }
}
