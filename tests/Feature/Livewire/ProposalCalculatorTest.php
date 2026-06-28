<?php

namespace Tests\Feature\Livewire;

use App\Http\Livewire\ProposalCalculator;
use App\Models\Client;
use App\Models\Developer;
use App\Models\Person;
use App\Models\Priority;
use App\Models\Proposal;
use App\Models\Rol;
use App\Models\Statu;
use App\Models\Task;
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

    /** @test */
    public function it_uses_developer_base_costs_when_calculating_price_for_developer_assigned_tasks()
    {
        $client = Client::factory()->create();
        $seller = User::factory()->create();
        Person::factory()->create([
            'client_id' => $client->id,
            'user_id' => $seller->id,
        ]);

        $proposal = Proposal::factory()->create([
            'client_id' => $client->id,
        ]);

        $task = Task::create([
            'proposal_id' => $proposal->id,
            'text' => 'Composable task',
            'statu_id' => Statu::factory()->create()->id,
            'priority_id' => Priority::factory()->create()->id,
            'start_date' => now()->format('Y-m-d H:i:s'),
            'duration' => 1,
            'progress' => 0,
            'parent' => 0,
            'hours' => 0,
        ]);

        $junior = Developer::factory()->create(['cost_per_hour' => 20]);
        $senior = Developer::factory()->create(['cost_per_hour' => 50]);

        $task->developers()->attach($junior->id, ['hours' => 4]);
        $task->developers()->attach($senior->id, ['hours' => 6]);

        Livewire::test(ProposalCalculator::class, ['proposal' => $proposal])
            ->call('addNewVersion')
            // 4*20 + 6*50 = 80 + 300 = 380 (no override)
            ->set('version.cost_per_hour', 999) // intentionally high to prove override isn't used
            ->set('version.unexpected', 0)
            ->set('version.company_gain', 0)
            ->set('version.bank_tax', 0)
            ->set('version.months_to_pay', 1)
            ->set('version.seller_commission_percentage', 0)
            ->assertSet('price', 380.0);
    }

    /** @test */
    public function it_prefers_per_version_developer_cost_overrides_over_base_costs()
    {
        $client = Client::factory()->create();
        $seller = User::factory()->create();
        Person::factory()->create([
            'client_id' => $client->id,
            'user_id' => $seller->id,
        ]);

        $proposal = Proposal::factory()->create([
            'client_id' => $client->id,
        ]);

        $task = Task::create([
            'proposal_id' => $proposal->id,
            'text' => 'Override task',
            'statu_id' => Statu::factory()->create()->id,
            'priority_id' => Priority::factory()->create()->id,
            'start_date' => now()->format('Y-m-d H:i:s'),
            'duration' => 1,
            'progress' => 0,
            'parent' => 0,
            'hours' => 0,
        ]);

        $junior = Developer::factory()->create(['cost_per_hour' => 20]);
        $senior = Developer::factory()->create(['cost_per_hour' => 50]);

        $task->developers()->attach($junior->id, ['hours' => 4]);
        $task->developers()->attach($senior->id, ['hours' => 6]);

        // First, create a version with the overrides, then edit it.
        $component = Livewire::test(ProposalCalculator::class, ['proposal' => $proposal])
            ->call('addNewVersion')
            ->set('responsiblePersonId', null)
            ->set('version.unexpected', 0)
            ->set('version.company_gain', 0)
            ->set('version.bank_tax', 0)
            ->set('version.months_to_pay', 1)
            ->set('version.seller_commission_percentage', 0)
            ->set('version.cost_per_hour', 999)
            ->set('version.hour_per_day', 1);

        // Need at least one responsible before save.
        $person = Person::first();
        $component->set('responsiblePersonId', $person->id)
            ->call('addResponsible')
            ->call('save');

        $version = Version::latest('id')->first();
        $this->assertNotNull($version);

        // Apply per-version overrides.
        $version->developers()->sync([
            $junior->id => ['cost_per_hour' => 100], // base is 20
            $senior->id => ['cost_per_hour' => 200], // base is 50
        ]);

        // 4*100 + 6*200 = 400 + 1200 = 1600
        Livewire::test(ProposalCalculator::class, ['proposal' => $proposal])
            ->call('editVersion', $version->id)
            ->assertSet('price', 1600.0);
    }

    /** @test */
    public function it_exposes_a_per_developer_summary_with_accumulated_hours_and_effective_cost()
    {
        $client = Client::factory()->create();
        $seller = User::factory()->create();
        Person::factory()->create([
            'client_id' => $client->id,
            'user_id' => $seller->id,
        ]);

        $proposal = Proposal::factory()->create([
            'client_id' => $client->id,
        ]);

        $first = Task::create([
            'proposal_id' => $proposal->id,
            'text' => 'First slice',
            'statu_id' => Statu::factory()->create()->id,
            'priority_id' => Priority::factory()->create()->id,
            'start_date' => now()->format('Y-m-d H:i:s'),
            'duration' => 1,
            'progress' => 0,
            'parent' => 0,
            'hours' => 0,
        ]);

        $second = Task::create([
            'proposal_id' => $proposal->id,
            'text' => 'Second slice',
            'statu_id' => Statu::factory()->create()->id,
            'priority_id' => Priority::factory()->create()->id,
            'start_date' => now()->format('Y-m-d H:i:s'),
            'duration' => 1,
            'progress' => 0,
            'parent' => 0,
            'hours' => 0,
        ]);

        $junior = Developer::factory()->create([
            'cost_per_hour' => 20,
        ]);
        $senior = Developer::factory()->create([
            'cost_per_hour' => 50,
        ]);

        $first->developers()->attach($junior->id, ['hours' => 2]);
        $first->developers()->attach($senior->id, ['hours' => 3]);
        $second->developers()->attach($junior->id, ['hours' => 4]);

        Livewire::test(ProposalCalculator::class, ['proposal' => $proposal])
            ->call('addNewVersion')
            ->assertSet('version.cost_per_hour', null)
            ->set('version.cost_per_hour', 999) // ignored, overrides/base used
            ->assertSet('developerOverrides', [])
            ->tap(function ($component) use ($junior, $senior) {
                $summaries = $component->instance()->developerSummaries;
                $byId = $summaries->keyBy('id');

                $this->assertEquals(6.0, $byId[$junior->id]['proposal_hours']);
                $this->assertEquals(3.0, $byId[$senior->id]['proposal_hours']);
                $this->assertEquals(20.0, $byId[$junior->id]['base_cost_per_hour']);
                $this->assertEquals(50.0, $byId[$senior->id]['base_cost_per_hour']);
                $this->assertNull($byId[$junior->id]['version_cost_per_hour']);
                $this->assertNull($byId[$senior->id]['version_cost_per_hour']);
                $this->assertEquals(20.0, $byId[$junior->id]['effective_cost_per_hour']);
                $this->assertEquals(50.0, $byId[$senior->id]['effective_cost_per_hour']);
                $this->assertEquals(120.0, $byId[$junior->id]['subtotal']); // 6 * 20
                $this->assertEquals(150.0, $byId[$senior->id]['subtotal']); // 3 * 50
            });
    }

    /** @test */
    public function it_uses_version_override_in_the_summary_when_provided_and_recalculates_price()
    {
        $client = Client::factory()->create();
        $seller = User::factory()->create();
        Person::factory()->create([
            'client_id' => $client->id,
            'user_id' => $seller->id,
        ]);

        $proposal = Proposal::factory()->create([
            'client_id' => $client->id,
        ]);

        $task = Task::create([
            'proposal_id' => $proposal->id,
            'text' => 'Override summary',
            'statu_id' => Statu::factory()->create()->id,
            'priority_id' => Priority::factory()->create()->id,
            'start_date' => now()->format('Y-m-d H:i:s'),
            'duration' => 1,
            'progress' => 0,
            'parent' => 0,
            'hours' => 0,
        ]);

        $developer = Developer::factory()->create(['cost_per_hour' => 20]);
        $task->developers()->attach($developer->id, ['hours' => 4]);

        $component = Livewire::test(ProposalCalculator::class, ['proposal' => $proposal])
            ->call('addNewVersion')
            ->set('version.cost_per_hour', 0)
            ->set('version.unexpected', 0)
            ->set('version.company_gain', 0)
            ->set('version.bank_tax', 0)
            ->set('version.months_to_pay', 1)
            ->set('version.seller_commission_percentage', 0)
            ->assertSet('price', 80.0); // 4 * base 20

        // Setting a version override should re-run the calculator.
        $component->set("developerOverrides.{$developer->id}", 35)
            ->assertSet('price', 140.0); // 4 * override 35
    }

    /** @test */
    public function it_persists_developer_overrides_when_saving_a_new_version()
    {
        $client = Client::factory()->create();
        $seller = User::factory()->create();
        $person = Person::factory()->create([
            'client_id' => $client->id,
            'user_id' => $seller->id,
        ]);

        $proposal = Proposal::factory()->create([
            'client_id' => $client->id,
        ]);

        $task = Task::create([
            'proposal_id' => $proposal->id,
            'text' => 'Save overrides',
            'statu_id' => Statu::factory()->create()->id,
            'priority_id' => Priority::factory()->create()->id,
            'start_date' => now()->format('Y-m-d H:i:s'),
            'duration' => 1,
            'progress' => 0,
            'parent' => 0,
            'hours' => 0,
        ]);

        $junior = Developer::factory()->create(['cost_per_hour' => 20]);
        $senior = Developer::factory()->create(['cost_per_hour' => 50]);
        $task->developers()->attach($junior->id, ['hours' => 4]);
        $task->developers()->attach($senior->id, ['hours' => 6]);

        Livewire::test(ProposalCalculator::class, ['proposal' => $proposal])
            ->call('addNewVersion')
            ->set('version.unexpected', 0)
            ->set('version.company_gain', 0)
            ->set('version.bank_tax', 0)
            ->set('version.months_to_pay', 1)
            ->set('version.seller_commission_percentage', 0)
            ->set('version.cost_per_hour', 0)
            ->set('version.hour_per_day', 1)
            ->set("developerOverrides.{$junior->id}", 25)
            ->set("developerOverrides.{$senior->id}", 80)
            ->set('responsiblePersonId', $person->id)
            ->call('addResponsible')
            ->call('save')
            ->assertHasNoErrors();

        $version = Version::latest('id')->first();
        $this->assertNotNull($version);

        $this->assertDatabaseHas('developer_version', [
            'version_id' => $version->id,
            'developer_id' => $junior->id,
            'cost_per_hour' => 25,
        ]);

        $this->assertDatabaseHas('developer_version', [
            'version_id' => $version->id,
            'developer_id' => $senior->id,
            'cost_per_hour' => 80,
        ]);
    }

    /** @test */
    public function it_loads_persisted_overrides_when_editing_an_existing_version()
    {
        $client = Client::factory()->create();
        $seller = User::factory()->create();
        $person = Person::factory()->create([
            'client_id' => $client->id,
            'user_id' => $seller->id,
        ]);

        $proposal = Proposal::factory()->create([
            'client_id' => $client->id,
        ]);

        $task = Task::create([
            'proposal_id' => $proposal->id,
            'text' => 'Load overrides',
            'statu_id' => Statu::factory()->create()->id,
            'priority_id' => Priority::factory()->create()->id,
            'start_date' => now()->format('Y-m-d H:i:s'),
            'duration' => 1,
            'progress' => 0,
            'parent' => 0,
            'hours' => 0,
        ]);

        $junior = Developer::factory()->create(['cost_per_hour' => 20]);
        $senior = Developer::factory()->create(['cost_per_hour' => 50]);
        $task->developers()->attach($junior->id, ['hours' => 4]);
        $task->developers()->attach($senior->id, ['hours' => 6]);

        Livewire::test(ProposalCalculator::class, ['proposal' => $proposal])
            ->call('addNewVersion')
            ->set('version.unexpected', 0)
            ->set('version.company_gain', 0)
            ->set('version.bank_tax', 0)
            ->set('version.months_to_pay', 1)
            ->set('version.seller_commission_percentage', 0)
            ->set('version.cost_per_hour', 0)
            ->set('version.hour_per_day', 1)
            ->set("developerOverrides.{$junior->id}", 25)
            ->set("developerOverrides.{$senior->id}", 80)
            ->set('responsiblePersonId', $person->id)
            ->call('addResponsible')
            ->call('save');

        $version = Version::latest('id')->first();

        // 4*25 + 6*80 = 100 + 480 = 580
        Livewire::test(ProposalCalculator::class, ['proposal' => $proposal])
            ->call('editVersion', $version->id)
            ->assertSet('price', 580.0)
            ->assertSet("developerOverrides.{$junior->id}", 25)
            ->assertSet("developerOverrides.{$senior->id}", 80);
    }

    /** @test */
    public function it_falls_back_to_version_cost_per_hour_when_developer_has_no_base_cost()
    {
        $client = Client::factory()->create();
        $seller = User::factory()->create();
        Person::factory()->create([
            'client_id' => $client->id,
            'user_id' => $seller->id,
        ]);

        $proposal = Proposal::factory()->create([
            'client_id' => $client->id,
        ]);

        $task = Task::create([
            'proposal_id' => $proposal->id,
            'text' => 'Fallback cost',
            'statu_id' => Statu::factory()->create()->id,
            'priority_id' => Priority::factory()->create()->id,
            'start_date' => now()->format('Y-m-d H:i:s'),
            'duration' => 1,
            'progress' => 0,
            'parent' => 0,
            'hours' => 0,
        ]);

        $developer = Developer::factory()->create(['cost_per_hour' => null]);
        $task->developers()->attach($developer->id, ['hours' => 3]);

        Livewire::test(ProposalCalculator::class, ['proposal' => $proposal])
            ->call('addNewVersion')
            ->set('version.cost_per_hour', 10)
            ->set('version.unexpected', 0)
            ->set('version.company_gain', 0)
            ->set('version.bank_tax', 0)
            ->set('version.months_to_pay', 1)
            ->set('version.seller_commission_percentage', 0)
            ->assertSet('price', 30.0) // 3 * 10 fallback
            ->tap(function ($component) use ($developer) {
                $summary = $component
                    ->instance()
                    ->developerSummaries
                    ->firstWhere('id', $developer->id);

                $this->assertNotNull($summary);
                $this->assertEquals(0.0, $summary['base_cost_per_hour']);
                $this->assertNull($summary['version_cost_per_hour']);
                $this->assertEquals(10.0, $summary['effective_cost_per_hour']);
                $this->assertEquals(30.0, $summary['subtotal']);
            });
    }
}
