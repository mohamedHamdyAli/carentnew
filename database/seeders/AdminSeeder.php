<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * * Create a super admin user
         */
        //uuid of super admin user
        $superAdminId = Str::uuid();
        User::factory(1)->hasBalance()->create([
            'id' => $superAdminId,
            'name' => 'Super Admin',
            'email' => 'super@carentegypt.com',
            'phone' => '+000000000001',
        ]);

        /**
         * * Assign super admin role to user
         */
        User::find($superAdminId)->assignRole('super admin');

        /**
         * * Create a admin user
         */
        //uuid of admin user
        $adminId = Str::uuid();
        User::factory(1)->hasBalance()->create([
            'id' => $adminId,
            'name' => 'Admin',
            'email' => 'admin@carentegypt.com',
            'phone' => '+000000000002',
        ]);

        /**
         * * Assign admin role to user
         */
        User::find($adminId)->assignRole('admin');
    }
}
