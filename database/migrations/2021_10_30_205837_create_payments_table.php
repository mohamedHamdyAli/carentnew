<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type')->nullable();
            $table->string('referenceNumber')->nullable();
            $table->string('merchantRefNumber')->nullable();
            $table->decimal('orderAmount', 8, 2)->nullable();
            $table->decimal('paymentAmount', 8, 2)->nullable();
            $table->decimal('fawryFees', 8, 2)->nullable();
            $table->string('paymentMethod')->nullable();
            $table->string('orderStatus')->nullable();
            $table->timestamp('paymentTime')->nullable();
            $table->string('customerMobile')->nullable();
            $table->string('customerMail')->nullable();
            $table->string('customerProfileId')->nullable();
            $table->text('signature')->nullable();
            $table->mediumInteger('statusCode')->nullable();
            $table->string('statusDescription')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
