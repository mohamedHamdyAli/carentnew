<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('vat_percentage')->default(14);
            $table->boolean('point_to_money')->default(false);
            $table->decimal('point_to_money_rate', 10, 2)->default(1);
            $table->boolean('money_to_point')->default(false);
            $table->decimal('money_to_point_rate', 10, 2)->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('app_settings');
    }
}
