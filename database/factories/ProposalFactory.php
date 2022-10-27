<?php

namespace Database\Factories;

use App\Models\Proposal;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProposalFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Proposal::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'product_name' => $this->faker->text(255),
            'description' => $this->faker->sentence(15),
            'client_id' => \App\Models\Client::factory(),
        ];
    }
}
