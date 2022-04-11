<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\BrandModel;
use File;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Brand::truncate();
        BrandModel::truncate();
        $json = File::get("database/json/brands.json");
        $data = json_decode($json);
        $brandCount = 1;
        foreach ($data as $brand) {
            $newBrand = Brand::create([
                'display_order' => $brandCount,
                'name_en' => $brand->name_en,
                'name_ar' => $brand->name_ar,
                'logo' => $brand->logo
            ]);
            $models = $brand->models;
            $modelsCount = 1;
            foreach ($models as $model) {
                BrandModel::create([
                    'display_order' => $modelsCount,
                    'name_en' => $model,
                    'name_ar' => $model,
                    'brand_id' => $newBrand->id
                ]);
                $modelsCount++;
            }
            $brandCount++;
        }
    }
}
