<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderRenterReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_renter_reviews', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('order_id');
            $table->uuid('vehicle_id');
            $table->tinyInteger('driver_rate')->nullable();
            $table->tinyInteger('cleaness_rate');
            $table->tinyInteger('condition_rate');
            $table->tinyInteger('communication_rate');
            $table->tinyInteger('overall_rate')->nullable();
            $table->text('comment')->nullable();
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
        Schema::dropIfExists('order_renter_reviews');
    }
}
