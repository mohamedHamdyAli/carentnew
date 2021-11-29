<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehiclePricingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicle_pricings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('vehicle_id');
            $table->decimal('daily_price', 8, 2);
            $table->decimal('week_to_month', 8, 2)->nullable();
            $table->decimal('month_or_more', 8, 2)->nullable();
            $table->boolean('has_driver')->default(false);
            $table->decimal('driver_daily_price', 8, 2)->nullable();
            $table->boolean('is_driver_required')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehicle_pricings');
    }
}
