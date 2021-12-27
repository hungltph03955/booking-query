<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\RoomType;

class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => rtrim(ucfirst($this->faker->text(20))),
            'description' => $this->faker->sentence(),
            'created_at' => $this->faker->dateTimeBetween('-20 days', '-10 days'),
            'updated_at' => $this->faker->dateTimeBetween('-5 days', '-1 days'),
            'room_type_id' => RoomType::factory()->create(),
        ];
    }
}
