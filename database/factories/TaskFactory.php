<?php

namespace Database\Factories;

use App\Models\Priority;
use App\Models\Proposal;
use App\Models\Statu;
use App\Models\Task;
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
            'text' => $this->faker->name,
            'hours' => $this->faker->randomNumber(0),
            'real_hours' => $this->faker->randomNumber(1),
            'statu_id' => Statu::factory(),
            'priority_id' => Priority::factory(),
            'proposal_id' => Proposal::factory(),
            'start_date' => now()->format('Y-m-d H:i:s'),
            'duration' => 1,
            'progress' => 0,
            'parent' => 0,
        ];
    }
}
