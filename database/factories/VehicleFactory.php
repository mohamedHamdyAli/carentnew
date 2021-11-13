<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'country_id',
            'state_id',
            'category_id',
            'brand_id',
            'model_id',
            'plate_number' => $this->faker->unique()->regexify('[A-Z]{3}[0-9]{3}'),
            'manufacture_year' => $this->faker->year,
            'color' => $this->faker->colorName,
            'fuel' => $this->faker->randomElement(['Gasoline', 'Diesel', 'Electric']),
            'features' => $this->faker->text,
            'seat_count' => $this->faker->numberBetween(2, 10),
            'rating' => $this->faker->numberBetween(1, 5),
            'views' => $this->faker->numberBetween(1, 100),
            'rented' => $this->faker->numberBetween(0, 50),
            'vehicle_license_verified_at' => now(),
            'vehicle_insurance_verified_at' => now(),
            'verified_at' => now(),
        ];
    }

    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'vehicle_license_verified_at' => null,
                'vehicle_insurance_verified_at' => null,
                'verified_at' => null,
            ];
        });
    }

    public function active()
    {
        return $this->state(function (array $attributes) {
            return [
                'active' => true,
            ];
        });
    }
}
