<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->numerify('+##1########'),
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
            'driver_license_verified_at' => now(),
            'identity_document_verified_at' => now(),
            'verified_at' => now(),
            'password' => '$2y$10$vfKlSGrgL.q58pQBFmfgf.DmFXV0V7kjSpv8fDlP5pzpoDkD4gdrW', // secret
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
                'phone_verified_at' => null,
                'driver_license_verified_at' => null,
                'identity_document_verified_at' => null,
                'verified_at' => null,
            ];
        });
    }

    public function hasBalance()
    {
        return $this->state(function (array $attributes) {
            return [
                'balance' => rand(1, 199999),
                'reward_points' => rand(1, 14999),
            ];
        });
    }

    public function inActive()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => false,
            ];
        });
    }

    public function configure()
    {
        return $this->afterMaking(function (User $user) {
        })->afterCreating(function (User $user) {
            $user->assignRole('user');
        });
    }
}
