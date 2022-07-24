<?php

namespace Database\Seeders;

use App\Models\Report;
use Illuminate\Database\Seeder;

class ReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $count = 0;
        while ($count < 120) {
            Report::updateOrCreate(
                [
                    'date' => now()->subDays($count)
                ],
                [
                    'sales' => rand(5000, 100000),
                    'bookings' => rand(20, 100),
                    'users' => rand(20, 100),
                    'vehicles' => rand(10, 100)
                ]
            );
            $count++;
        }
    }
}
