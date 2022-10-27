<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Task::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'hours' => $this->faker->randomNumber(0),
            'real_hours' => $this->faker->randomNumber(1),
            'statu_id' => \App\Models\Statu::factory(),
            'priority_id' => \App\Models\Priority::factory(),
            'version_id' => \App\Models\Version::factory(),
            'receipt_id' => \App\Models\Receipt::factory(),
        ];
    }
}
