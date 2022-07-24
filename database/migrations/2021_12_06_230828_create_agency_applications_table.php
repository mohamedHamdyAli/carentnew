<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgencyApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agency_applications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->boolean('terms_agreed')->default(false);
            $table->uuid('identity_document_id')->nullable();
            $table->boolean('identity_document_verified')->default(false);
            $table->uuid('business_document_id')->nullable();
            $table->boolean('business_document_verified')->default(false);
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
        Schema::dropIfExists('agency_applications');
    }
}
