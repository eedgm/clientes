<?php

namespace Tests\Feature\Livewire;

use App\Http\Livewire\ProposalCalculator;
use App\Models\Client;
use App\Models\Person;
use App\Models\Proposal;
use App\Models\Rol;
use App\Models\User;
use App\Models\Version;
use Database\Seeders\PermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProposalCalculatorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        User::factory()->create(['email' => 'admin@admin.com']);
        $this->seed(PermissionsSeeder::class);

        $this->actingAs(User::where('email', 'admin@admin.com')->first());
    }

    /** @test */
    public function it_recalculates_from_current_state_without_input_order_dependency()
    {
        $client = Client::factory()->create();
        $sellerUser = User::factory()->create();
        Person::factory()->create([
            'client_id' => $client->id,
            'user_id' => $sellerUser->id,
        ]);

        $proposal = Proposal::factory()->create([
            'client_id' => $client->id,
        ]);

        Livewire::test(ProposalCalculator::class, ['proposal' => $proposal])
            ->call('addNewVersion')
            ->set('version.months_to_pay', 2)
            ->set('version.bank_tax', 50)
            ->set('version.seller_commission_percentage', 10)
            ->set('version.company_gain', 20)
            ->set('version.unexpected', 10)
            ->set('version.cost_per_hour', 100)
            ->set('version.hours', 10)
            ->assertSet('unexpected', 1.0)
            ->assertSet('total_hours', 11.0)
            ->assertSet('price', 1100.0)
            ->assertSet('company_gain', 220.0)
            ->assertSet('price_with_gain', 1320.0)
            ->assertSet('price_with_bank_tax', 1420.0)
            ->assertSet('seller_commission', 142.0)
            ->assertSet('total_with_seller_commission', 1562.0)
            ->assertSet('price_month_divided', 781.0)
            ->assertSet('version.total', 1562.0);
    }

    /** @test */
    public function it_requires_at_least_one_responsible_and_syncs_them_on_save()
    {
        $client = Client::factory()->create();
        $sellerUser = User::factory()->create();
        $person = Person::factory()->create([
            'client_id' => $client->id,
            'user_id' => $sellerUser->id,
        ]);

        $proposal = Proposal::factory()->create([
            'client_id' => $client->id,
        ]);

        $component = Livewire::test(ProposalCalculator::class, ['proposal' => $proposal])
            ->call('addNewVersion')
            ->set('version.hours', 10)
            ->set('version.unexpected', 10)
            ->set('version.cost_per_hour', 100)
            ->set('version.company_gain', 20)
            ->set('version.seller_commission_percentage', 10)
            ->set('version.bank_tax', 50)
            ->set('version.months_to_pay', 2)
            ->set('version.hour_per_day', 8)
            ->set('version.user_id', $sellerUser->id)
            ->call('save')
            ->assertHasErrors(['selectedPeople']);

        $component
            ->set('responsiblePersonId', $person->id)
            ->call('addResponsible')
            ->call('save')
            ->assertHasNoErrors();

        $version = Version::latest('id')->first();

        $this->assertNotNull($version);
        $this->assertDatabaseHas('versions', [
            'id' => $version->id,
            'proposal_id' => $proposal->id,
            'seller_commission_percentage' => 10,
        ]);
        $this->assertTrue(
            $version
                ->people()
                ->where('people.id', $person->id)
                ->exists()
        );
    }

    /** @test */
    public function it_creates_a_new_responsible_from_modal_and_adds_it_to_current_version()
    {
        $client = Client::factory()->create();
        $rol = Rol::factory()->create();

        $proposal = Proposal::factory()->create([
            'client_id' => $client->id,
        ]);

        $component = Livewire::test(ProposalCalculator::class, ['proposal' => $proposal])
            ->call('addNewVersion')
            ->set('version.hours', 10)
            ->set('version.unexpected', 10)
            ->set('version.cost_per_hour', 100)
            ->set('version.company_gain', 20)
            ->set('version.seller_commission_percentage', 10)
            ->set('version.bank_tax', 50)
            ->set('version.months_to_pay', 2)
            ->set('version.hour_per_day', 8)
            ->call('newResponsible')
            ->set('responsibleUser.name', 'Responsable Nuevo')
            ->set('responsibleUser.email', 'responsable.nuevo@example.com')
            ->set('responsiblePassword', 'secret123')
            ->set('responsiblePerson.description', 'Contacto principal')
            ->set('responsiblePerson.phone', '123456')
            ->set('responsiblePerson.skype', 'responsable.skype')
            ->set('responsiblePerson.rol_id', $rol->id)
            ->call('saveResponsible')
            ->assertSet('showingResponsibleModal', false)
            ->assertHasNoErrors([
                'responsibleUser.name',
                'responsibleUser.email',
                'responsiblePassword',
                'responsiblePerson.rol_id',
            ]);

        $newUser = User::where('email', 'responsable.nuevo@example.com')->first();

        $this->assertNotNull($newUser);

        $newPerson = Person::where('client_id', $client->id)
            ->where('user_id', $newUser->id)
            ->first();

        $this->assertNotNull($newPerson);

        $component
            ->set('version.user_id', $newUser->id)
            ->call('save')
            ->assertHasNoErrors();

        $version = Version::latest('id')->first();

        $this->assertNotNull($version);
        $this->assertTrue(
            $version
                ->people()
                ->where('people.id', $newPerson->id)
                ->exists()
        );
    }
}
