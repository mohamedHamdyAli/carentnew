<?php

namespace Database\Seeders;

use App\Models\Category;
use File;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Category::truncate();
        $json = File::get("database/json/categories.json");
        $data = json_decode($json);
        $dataCount = 1;
        foreach ($data as $d) {
            Category::create([
                'display_order' => $dataCount,
                'name_en' => $d->name_en,
                'name_ar' => $d->name_ar
            ]);
            $dataCount++;
        }
    }
}
