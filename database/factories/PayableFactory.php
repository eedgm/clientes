<?php

namespace Database\Factories;

use App\Models\Payable;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class PayableFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Payable::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'date' => $this->faker->date,
            'cost' => $this->faker->randomNumber(1),
            'margin' => $this->faker->randomNumber(1),
            'total' => $this->faker->randomFloat(2, 0, 9999),
            'supplier_id_reference' => $this->faker->text(255),
            'periodicity' => 'month',
            'product_id' => \App\Models\Product::factory(),
            'supplier_id' => \App\Models\Supplier::factory(),
            'receipt_id' => \App\Models\Receipt::factory(),
        ];
    }
}
