<?php

namespace Database\Seeders;

use App\Models\OrderStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class OrderStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        OrderStatus::truncate();
        $json = File::get("database/json/order_statuses.json");
        $data = json_decode($json);
        $dataCount = 1;
        foreach ($data as $d) {
            OrderStatus::create([
                'name_en' => $d->name_en,
                'name_ar'   => $d->name_ar,
                'terminate'  => $d->terminate,
                'notify'  => $d->notify,
                'message_en'  => $d->message_en,
                'message_ar'  => $d->message_ar,
            ]);
            $dataCount++;
        }
    }
}
