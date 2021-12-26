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
                'notify_client' => $d->notify_client,
                'notify_owner' => $d->notify_owner,
                'client_title_en' => $d->client_title_en,
                'client_title_ar' => $d->client_title_ar,
                'client_body_en' => $d->client_body_en,
                'client_body_ar' => $d->client_body_ar,
                'owner_title_en' =>  $d->owner_title_en,
                'owner_title_ar' => $d->owner_title_ar,
                'owner_body_en' => $d->owner_body_en,
                'owner_body_ar' => $d->owner_body_ar,
            ]);
            $dataCount++;
        }
    }
}
