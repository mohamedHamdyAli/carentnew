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
            $table->smallInteger('vat')->default(14);
            $table->smallInteger('profit_margin')->default(10);
            $table->smallInteger('renter_cancellation_fees')->default(20);
            $table->smallInteger('owner_cancellation_fees')->default(20);
            $table->smallInteger('late_retern_fees')->default(20);
            $table->smallInteger('early_retern_fees')->default(20);
            $table->smallInteger('accident_fees')->default(20);
            $table->decimal('accident_max_fees', 10, 2)->default(2000);
            $table->decimal('money_to_point_rate', 10, 2)->default(1);
            $table->decimal('point_to_money_rate', 10, 2)->default(1);
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
