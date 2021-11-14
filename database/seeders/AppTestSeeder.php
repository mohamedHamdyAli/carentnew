<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleFeature;
use App\Models\VehicleImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AppTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Seeding app test data...');
        /**
         * * Create a owner user
         */
        // delete user where email = 'owner@carent.com' if exists
        // User::where('email', 'owner@carent.com')->delete();

        $owner = User::where('email', 'owner@carent.com')->first();
        $ownerId = $owner->id;
        if (!$ownerId) {
            $ownerId = Str::uuid();
            User::factory(1)->hasBalance()->create([
                'id' => $ownerId,
                'name' => 'Owner Carent',
                'email' => 'owner@carent.com',
                'phone' => '+000000000003',
            ]);

            /**
             * * Assign owner role to owner user
             */
            User::find($ownerId)->assignRole('owner');
        }

        /**
         * * Generate vehicles for owner user
         */
        // Vehicle::truncate();
        // VehicleImage::truncate();
        // VehicleFeature::truncate();
        Vehicle::factory(10)->active()->create([
            'user_id' => $ownerId,
        ]);
        $this->command->info('App test data seeded');
    }
}
