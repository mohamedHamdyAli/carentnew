<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddActiveColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('categories', function(Blueprint $table) {
            $table->boolean('active')->default(true);
            $table->softDeletes();
        });

        Schema::table('fuel_types', function(Blueprint $table) {
            $table->boolean('active')->default(true);
            $table->softDeletes();
        });

        Schema::table('features', function(Blueprint $table) {
            $table->boolean('active')->default(true);
            $table->softDeletes();
        });

        Schema::table('brand_models', function(Blueprint $table) {
            $table->boolean('active')->default(true);
            $table->softDeletes();
        });

        Schema::table('states', function(Blueprint $table) {
            $table->boolean('active')->default(true);
            $table->softDeletes();
        });

        Schema::table('brands', function(Blueprint $table) {
            $table->boolean('active')->default(true);
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //drop columns
        Schema::table('categories', function(Blueprint $table) {
            $table->dropColumn('active');
            $table->dropSoftDeletes();
        });
        Schema::table('fuel_types', function(Blueprint $table) {
            $table->dropColumn('active');
            $table->dropSoftDeletes();
        });
        Schema::table('features', function(Blueprint $table) {
            $table->dropColumn('active');
            $table->dropSoftDeletes();
        });
        Schema::table('brand_models', function(Blueprint $table) {
            $table->dropColumn('active');
            $table->dropSoftDeletes();
        });
        Schema::table('states', function(Blueprint $table) {
            $table->dropColumn('active');
            $table->dropSoftDeletes();
        });

        Schema::table('brands', function(Blueprint $table) {
            $table->dropColumn('active');
            $table->dropSoftDeletes();
        });


    }
}
