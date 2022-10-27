<?php

namespace Database\Factories;

use App\Models\Person;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class PersonFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Person::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'description' => $this->faker->sentence(15),
            'phone' => $this->faker->phoneNumber,
            'skype' => $this->faker->text(255),
            'client_id' => \App\Models\Client::factory(),
            'rol_id' => \App\Models\Rol::factory(),
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
