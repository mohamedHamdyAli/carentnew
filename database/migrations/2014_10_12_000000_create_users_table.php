<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 66);
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('phone', 15)->unique()->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('password');
            $table->uuid('default_address_id')->nullable();
            $table->decimal('balance', 8, 2)->default(0);
            $table->bigInteger('reward_points')->default(0);
            $table->timestamp('driver_license_verified_at')->nullable();
            $table->timestamp('identity_document_verified_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('fcm')->nullable();
            $table->string('language')->default('en');
            // $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
