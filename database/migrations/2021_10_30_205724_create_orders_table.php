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
            $table->string('number', 20)->unique();
            $table->uuid('user_id');
            $table->uuid('vehicle_id');
            $table->uuid('owner_id');
            $table->uuid('order_status_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('type', ['rent', 'extend'])->default('rent');
            $table->boolean('with_driver')->default(false);
            $table->decimal('vehicle_total', 8, 2);
            $table->decimal('driver_total', 8, 2);
            $table->decimal('sub_total', 8, 2);
            $table->decimal('vat', 8, 2);
            $table->decimal('discount', 8, 2);
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
