<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BalanceTransactionFactory extends Factory
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
            'amount' => $this->faker->randomFloat(2, 0, 100),
            'operation' => $this->faker->randomElement(['in', 'in', 'out']),
            'type' => $this->faker->randomElement(['order', 'refund', 'withdrawal']),
        ];
    }
}
