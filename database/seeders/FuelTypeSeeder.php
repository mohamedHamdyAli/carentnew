<?php

namespace Database\Seeders;

use App\Models\FuelType;
use File;
use Illuminate\Database\Seeder;

class FuelTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        FuelType::truncate();
        $json = File::get("database/json/fuel_types.json");
        $data = json_decode($json);
        $dataCount = 1;
        foreach ($data as $d) {
            FuelType::create([
                'display_order' => $dataCount,
                'name_en' => $d->name_en,
                'name_ar' => $d->name_ar
            ]);
            $dataCount++;
        }
    }
}
