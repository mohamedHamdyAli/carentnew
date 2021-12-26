<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name_en', 50);
            $table->string('name_ar', 50);
            $table->boolean('terminate')->default(false);
            $table->boolean('notify_client')->default(false);
            $table->boolean('notify_owner')->default(false);
            $table->string('client_title_en', 75)->nullable();
            $table->string('client_title_ar', 75)->nullable();
            $table->string('client_body_en', 150)->nullable();
            $table->string('client_body_ar', 150)->nullable();
            $table->string('owner_title_en', 75)->nullable();
            $table->string('owner_title_ar', 75)->nullable();
            $table->string('owner_body_en', 150)->nullable();
            $table->string('owner_body_ar', 150)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_statuses');
    }
}
