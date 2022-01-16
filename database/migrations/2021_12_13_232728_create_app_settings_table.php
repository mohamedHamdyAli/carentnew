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
            $table->uuid('id')->primary();
            $table->mediumInteger('version')->unique();
            $table->smallInteger('vat_percentage')->default(14);
            $table->smallInteger('processing_percentage')->default(3);
            $table->decimal('processing_fixed', 8, 2)->default(3.50);
            $table->smallInteger('early_return_percentage')->default(80);
            $table->decimal('owner_cancel_penality', 10, 2)->default(0);
            $table->boolean('point_to_money')->default(false);
            $table->decimal('point_to_money_rate', 10, 2)->default(1);
            $table->boolean('money_to_point')->default(false);
            $table->decimal('money_to_point_rate', 10, 2)->default(1);
            $table->mediumInteger('min_redemption_amount')->default(0);
            $table->text('car_legal_download_1')->nullable();
            $table->text('car_legal_download_2')->nullable();
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
        Schema::dropIfExists('app_settings');
    }
}
