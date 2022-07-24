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
            'vat' => 0.14,
            'point_to_money_rate' => 0.05,
            'money_to_point_rate' => 1,
            'rental_contract_file' => 'storage/public/app-settings/rent_download_1.pdf',
            'vehicle_receive_file' => 'storage/public/app-settings/rent_download_2.pdf',
            'vehicle_return_file' => 'storage/public/app-settings/rent_download_2.pdf',
        ]);
    }
}
