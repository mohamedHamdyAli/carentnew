<?php

namespace Database\Seeders;

use App\Models\Privilege;
use File;
use Illuminate\Database\Seeder;

class UserPrivilegeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $json = File::get("database/json/privileges.json");
        $data = json_decode($json);
        foreach ($data as $d) {
            Privilege::updateOrCreate([
                'name_en' => $d->name_en,
                'name_ar' => $d->name_ar,
            ], [
                'key'     => $d->key,
                'role_group' => $d->role_group,
            ]);
        }
    }
}
