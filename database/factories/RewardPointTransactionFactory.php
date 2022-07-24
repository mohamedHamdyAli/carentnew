<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RewardPointTransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => $this->faker->randomElement(\App\Models\User::all()->pluck('id')->toArray()),
            'points' => $this->faker->randomElement([100, 200, 300, 400, 500, 600, 700, 800, 900, 1000]),
            'operation' => $this->faker->randomElement(['in', 'in', 'out']),
            'type' => $this->faker->randomElement(['order', 'refund']),
        ];
    }
}
