<?php

namespace Database\Seeders;

use App\Models\Feature;
use File;
use Illuminate\Database\Seeder;

class FeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Feature::truncate();
        $json = File::get("database/json/features.json");
        $data = json_decode($json);
        foreach ($data as $d) {
            Feature::create([
                'name_en' => $d->name_en,
                'name_ar' => $d->name_ar
            ]);
        }
    }
}
