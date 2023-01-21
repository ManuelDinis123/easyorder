<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('connect_restaurant_type', function (Blueprint $table) {
            $table->unsignedInteger("id")->autoIncrement();
            $table->unsignedInteger("restaurant_id");
            $table->unsignedInteger("type_id");
            $table->foreign('restaurant_id')->references('id')->on('restaurants');
            $table->foreign('type_id')->references('id')->on('restaurant_type');
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('connect_restaurant_type');
    }
};
