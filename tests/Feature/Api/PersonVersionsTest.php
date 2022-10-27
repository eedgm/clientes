<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Person;
use App\Models\Version;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PersonVersionsTest extends TestCase
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
    public function it_gets_person_versions()
    {
        $person = Person::factory()->create();
        $version = Version::factory()->create();

        $person->versions()->attach($version);

        $response = $this->getJson(route('api.people.versions.index', $person));

        $response->assertOk()->assertSee($version->attachment);
    }

    /**
     * @test
     */
    public function it_can_attach_versions_to_person()
    {
        $person = Person::factory()->create();
        $version = Version::factory()->create();

        $response = $this->postJson(
            route('api.people.versions.store', [$person, $version])
        );

        $response->assertNoContent();

        $this->assertTrue(
            $person
                ->versions()
                ->where('versions.id', $version->id)
                ->exists()
        );
    }

    /**
     * @test
     */
    public function it_can_detach_versions_from_person()
    {
        $person = Person::factory()->create();
        $version = Version::factory()->create();

        $response = $this->deleteJson(
            route('api.people.versions.store', [$person, $version])
        );

        $response->assertNoContent();

        $this->assertFalse(
            $person
                ->versions()
                ->where('versions.id', $version->id)
                ->exists()
        );
    }
}
