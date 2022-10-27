<?php

namespace Database\Factories;

use App\Models\Version;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class VersionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Version::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'attachment' => $this->faker->text(255),
            'total' => $this->faker->randomFloat(2, 0, 9999),
            'time' => $this->faker->date,
            'cost_per_hour' => $this->faker->randomNumber(1),
            'hour_per_day' => $this->faker->randomNumber(1),
            'months_to_pay' => $this->faker->randomNumber(1),
            'unexpected' => $this->faker->randomNumber(1),
            'company_gain' => $this->faker->randomNumber(1),
            'bank_tax' => $this->faker->randomNumber(1),
            'first_payment' => $this->faker->randomNumber(1),
            'proposal_id' => \App\Models\Proposal::factory(),
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
