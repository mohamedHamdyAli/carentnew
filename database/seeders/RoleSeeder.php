<?php

namespace Database\Seeders;

use App\Models\Role;
use File;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Role::truncate();
        $json = File::get("database/json/roles.json");
        $data = json_decode($json);
        foreach ($data as $d) {
            Role::create([
                'name_en' => $d->name_en,
                'name_ar' => $d->name_ar,
                'key'     => $d->key
            ]);
        }
    }
}
