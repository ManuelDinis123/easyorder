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
        Schema::create('order_items_sides', function (Blueprint $table) {
            $table->unsignedInteger("id")->autoIncrement();
            $table->unsignedInteger("order_item_id");
            $table->unsignedInteger("side_id");
            $table->foreign('order_item_id')->references('id')->on('order_items');
            $table->foreign('side_id')->references('id')->on('menu_item_ingredients');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_items_sides');
    }
};
