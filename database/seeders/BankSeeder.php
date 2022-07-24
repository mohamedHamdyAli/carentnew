<?php

namespace Database\Seeders;

use App\Models\Bank;
use App\Models\Country;
use File;
use Illuminate\Database\Seeder;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Bank::truncate();
        $json = File::get("database/json/banks.json");
        $data = json_decode($json);
        foreach ($data as $key => $obj) {
            $country_id = Country::where('country_code', $key)->first()->id;
            foreach ($obj as $bank) {
                Bank::create([
                    'country_id' => $country_id,
                    'name' => $bank,
                ]);
            }
        }
    }
}
