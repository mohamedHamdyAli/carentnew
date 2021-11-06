<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\State;
use Illuminate\Database\Seeder;
use File;
use PhpParser\ErrorHandler\Collecting;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Country::truncate();
        State::truncate();
        $json = File::get("database/json/locations.json");
        $data = json_decode($json);
        foreach ($data as $country) {
            $newCountry = Country::create([
                'name_en' => $country->name_en,
                'name_ar' => $country->name_ar,
                'flag' => url($country->flag),
                'country_code' => $country->country_code,
                'phone_prefix' => $country->phone_prefix,
                'currency_code' => $country->currency_code
            ]);
            $states = $country->states;
            foreach ($states as $state) {
                State::create([
                    'name_en' => $state->name_en,
                    'name_ar' => $state->name_ar,
                    'country_id' => $newCountry->id
                ]);
            }
        }
    }
}
