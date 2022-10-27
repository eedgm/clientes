<?php

namespace Database\Factories;

use App\Models\Receipt;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReceiptFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Receipt::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'number' => $this->faker->randomNumber,
            'description' => $this->faker->sentence(15),
            'real_date' => $this->faker->date,
            'charged' => $this->faker->boolean,
            'reference_charged' => $this->faker->text(255),
            'date_charged' => $this->faker->dateTime,
            'client_id' => \App\Models\Client::factory(),
        ];
    }
}
