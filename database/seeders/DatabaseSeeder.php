<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        Seeder::call('Database\Seeders\RoleSeeder');
        Seeder::call('Database\Seeders\PrivilegeSeeder');
        Seeder::call('Database\Seeders\LocationSeeder');
        Seeder::call('Database\Seeders\BrandSeeder');
        Seeder::call('Database\Seeders\CategorySeeder');
        Seeder::call('Database\Seeders\FeatureSeeder');
        Seeder::call('Database\Seeders\FuelTypeSeeder');
        Seeder::call('Database\Seeders\AdminSeeder');
        Seeder::call('Database\Seeders\AppTestSeeder');
        Seeder::call('Database\Seeders\SettingSeeder');
        Seeder::call('Database\Seeders\OrderStatusSeeder');
    }
}
