<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Person;
use App\Models\Version;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VersionPeopleTest extends TestCase
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
    public function it_gets_version_people()
    {
        $version = Version::factory()->create();
        $person = Person::factory()->create();

        $version->people()->attach($person);

        $response = $this->getJson(
            route('api.versions.people.index', $version)
        );

        $response->assertOk()->assertSee($person->description);
    }

    /**
     * @test
     */
    public function it_can_attach_people_to_version()
    {
        $version = Version::factory()->create();
        $person = Person::factory()->create();

        $response = $this->postJson(
            route('api.versions.people.store', [$version, $person])
        );

        $response->assertNoContent();

        $this->assertTrue(
            $version
                ->people()
                ->where('people.id', $person->id)
                ->exists()
        );
    }

    /**
     * @test
     */
    public function it_can_detach_people_from_version()
    {
        $version = Version::factory()->create();
        $person = Person::factory()->create();

        $response = $this->deleteJson(
            route('api.versions.people.store', [$version, $person])
        );

        $response->assertNoContent();

        $this->assertFalse(
            $version
                ->people()
                ->where('people.id', $person->id)
                ->exists()
        );
    }
}
