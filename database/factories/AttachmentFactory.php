<?php

namespace Database\Factories;

use App\Models\Attachment;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttachmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Attachment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'attachment' => $this->faker->text(255),
            'description' => $this->faker->text,
            'user_id' => \App\Models\User::factory(),
            'ticket_id' => \App\Models\Ticket::factory(),
        ];
    }
}
