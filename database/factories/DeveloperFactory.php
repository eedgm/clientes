<?php

namespace Database\Factories;

use App\Models\Developer;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeveloperFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Developer::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'rol_id' => Rol::factory(),
            'cost_per_hour' => $this->faker->randomFloat(2, 0, 200),
        ];
    }
}
