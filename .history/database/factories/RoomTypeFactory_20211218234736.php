<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RoomTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'size' => $this->faker->numberBetween(1,5),
            'price' => $this->faker->numberBetween(100,500),
            'amount' => $this->faker->numberBetween(1,5),
            'created_at' => $this->faker->dateTimeBetween('-20 days', '-10 days'),
            'updated_at' => $this->faker->dateTimeBetween('-5 days', '-1 days'),
        ];
    }
}
