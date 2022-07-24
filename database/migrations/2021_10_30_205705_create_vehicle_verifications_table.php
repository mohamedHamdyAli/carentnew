<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehicleVerificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicle_verifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('vehicle_id');
            $table->uuid('vehicle_license_id')->nullable();
            $table->boolean('vehicle_license_verified')->default(false);
            $table->uuid('vehicle_insurance_id')->nullable();
            $table->boolean('vehicle_insurance_verified')->default(false);
            $table->enum('status', ['created', 'in-review', 'approved', 'rejected'])->default('created');
            $table->text('reason')->nullable();
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
        Schema::dropIfExists('vehicle_verifications');
    }
}
