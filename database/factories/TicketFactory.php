<?php

namespace Database\Factories;

use App\Models\Ticket;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Ticket::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'description' => $this->faker->text,
            'hours' => $this->faker->randomNumber(1),
            'total' => $this->faker->randomFloat(2, 0, 9999),
            'finished_ticket' => $this->faker->date,
            'comments' => $this->faker->text,
            'statu_id' => \App\Models\Statu::factory(),
            'priority_id' => \App\Models\Priority::factory(),
            'product_id' => \App\Models\Product::factory(),
            'person_id' => \App\Models\Person::factory(),
            'receipt_id' => \App\Models\Receipt::factory(),
        ];
    }
}
