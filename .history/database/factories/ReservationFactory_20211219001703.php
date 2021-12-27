<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ReservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'check_in' => $this->faker->dateTimeBetween('-2 days', '+5 days'),
            'check_out' => $this->faker->dateTimeBetween('+6 days', '-10 days'),
        ];
    }
}
