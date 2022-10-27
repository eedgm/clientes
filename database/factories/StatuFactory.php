<?php

namespace Database\Factories;

use App\Models\Statu;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class StatuFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Statu::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'limit' => $this->faker->randomNumber(0),
            'color_id' => \App\Models\Color::factory(),
            'icon_id' => \App\Models\Icon::factory(),
        ];
    }
}
