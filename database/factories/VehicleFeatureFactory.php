<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

class VehicleFeatureFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'vehicle_id' => DB::table('vehicles')->inRandomOrder()->first()->id,
            'feature_id' => DB::table('features')->inRandomOrder()->first()->id,
        ];
    }
}
