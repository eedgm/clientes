<?php

namespace Database\Factories;

use App\Models\Priority;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class PriorityFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Priority::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'color_id' => \App\Models\Color::factory(),
        ];
    }
}
