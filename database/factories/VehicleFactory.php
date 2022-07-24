<?php

namespace Database\Factories;

use App\Models\Vehicle;
use App\Models\VehicleFeature;
use App\Models\VehicleImage;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VehicleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $country = $this->randCountryAndState();
        $brand = $this->randBrandAndBrandModel();

        return [
            'country_id' => $country->id,
            'state_id' => $country->state->id,
            'category_id' => DB::table('categories')->inRandomOrder()->first()->id,
            'brand_id' => $brand->id,
            'model_id' => $brand->model->id,
            'plate_number' => Str::upper(Str::random(3)) . ' ' . rand(100, 9999),
            'manufacture_year' => $this->faker->dateTimeBetween('-10 years', '-1 years')->format('Y'),
            'color' => $this->faker->colorName,
            'thumbnail' => $this->faker->imageUrl(640, 480, 'transport'),
            'fuel_type_id' => DB::table('fuel_types')->inRandomOrder()->first()->id,
            'seat_count' => $this->faker->numberBetween(2, 10),
            'rating' => $this->faker->numberBetween(1, 5),
            'views' => $this->faker->numberBetween(1, 100),
            'rented' => $this->faker->numberBetween(0, 50),
            'vehicle_license_verified_at' => now(),
            'vehicle_insurance_verified_at' => now(),
            'verified_at' => now(),
        ];
    }

    /**
     * Get a random Country with a random
     * State of that Country.
     *
     * @return object
     */
    private function randCountryAndState()
    {
        $country = DB::table('countries')->inRandomOrder()->first();
        $state = DB::table('states')->where('country_id', $country->id)->inRandomOrder()->first();
        $country->state = (object) $state;
        return (object) $country;
    }

    /**
     * Get a random Brand with a random
     * BrandModel of that Brand.
     *
     * @return object
     */
    private function randBrandAndBrandModel()
    {
        $brand = DB::table('brands')->inRandomOrder()->first();
        $model = DB::table('brand_models')->where('brand_id', $brand->id)->inRandomOrder()->first();
        $brand->model = (object) $model;
        return (object) $brand;
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

    public function configure()
    {
        return $this->afterMaking(function (Vehicle $vehicle) {
        })->afterCreating(function (Vehicle $vehicle) {
            VehicleImage::factory()->times(rand(0, 3))->create([
                'vehicle_id' => $vehicle->id,
            ]);

            // get Features count and add random number of features to the vehicle
            $featuresCount = DB::table('features')->count();
            $features = DB::table('features')->inRandomOrder()->limit($this->faker->numberBetween(0, 5))->get();
            $features->each(function ($feature) use ($vehicle) {
                VehicleFeature::factory()->create([
                    'vehicle_id' => $vehicle->id,
                    'feature_id' => $feature->id,
                ]);
            });
        });
    }
}
