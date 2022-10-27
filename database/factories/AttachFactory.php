<?php

namespace Database\Factories;

use App\Models\Attach;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttachFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Attach::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'attachment' => $this->faker->text(255),
            'description' => $this->faker->sentence(15),
            'task_id' => \App\Models\Task::factory(),
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
