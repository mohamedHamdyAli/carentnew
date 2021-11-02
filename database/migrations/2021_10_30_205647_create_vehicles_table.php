<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('country_id');
            $table->uuid('state_id');
            $table->uuid('category_id')->nullable();
            $table->uuid('brand_id')->nullable();
            $table->uuid('model_id')->nullable();
            $table->string('manufacture_year', 4)->nullable();
            $table->string('color', 20)->nullable();
            $table->string('plate_number', 20)->nullable();
            $table->tinyInteger('rating')->nullable();
            $table->tinyInteger('views')->default(0);
            $table->tinyInteger('rented')->default(0);
            $table->boolean('active')->default(false);
            $table->text('inactive_message')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('vehicles');
    }
}
