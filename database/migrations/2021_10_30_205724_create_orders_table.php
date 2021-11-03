<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('renter_id');
            $table->uuid('vehicle_id');
            $table->uuid('owner_id');
            $table->uuid('order_Status_id');
            $table->enum('type', ['normal', 'extension']);
            $table->uuid('extended_from_id')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->smallInteger('day_count');
            $table->decimal('day_cost', 8, 2);
            $table->decimal('total', 8, 2);
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
        Schema::dropIfExists('orders');
    }
}
