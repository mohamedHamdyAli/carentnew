<?php

namespace Database\Seeders;

use App\Models\User;
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
        /**
         * * Create a renter user
         */
        $renterId = Str::uuid();
        User::factory(1)->hasBalance()->create([
            'id' => $renterId,
            'name' => 'Renter Carent',
            'email' => 'renter@carnet.com',
            'phone' => '+000000000003',
        ]);

        /**
         * * Assign renter role to renter user
         */
        User::find($renterId)->assignRole('renter');

        /**
         * * Generate vehicles for renter user
         */
        // $vehicles = User::find($renterId)->vehicles()->saveMany(
        //     User::find($renterId)->vehicles()->factory(10)->create()
        // );
    }
}
