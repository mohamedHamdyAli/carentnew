<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'display_order' => rand(1, 5),
            'image' => $this->faker->imageUrl(640, 480, 'transport'),
        ];
    }
}
