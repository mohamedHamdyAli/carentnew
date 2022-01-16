<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use Illuminate\Database\Seeder;

class AppSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AppSetting::truncate();

        AppSetting::create([
            'vat_percentage' => 0.14,
            'point_to_money' => true,
            'point_to_money_rate' => 0.05,
            'money_to_point' => true,
            'money_to_point_rate' => 1,
            'car_legal_download_1' => 'storage/public/app-settings/rent_download_1.pdf',
            'car_legal_download_2' => 'storage/public/app-settings/rent_download_2.pdf',
        ]);
    }
}
