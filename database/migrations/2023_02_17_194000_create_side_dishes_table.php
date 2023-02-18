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
        Schema::create('side_dishes', function (Blueprint $table) {
            $table->unsignedInteger("id")->autoIncrement();
            $table->unsignedInteger("side_id");
            $table->integer("quantity");
            $table->unsignedInteger("cart_item_id");
            $table->foreign('side_id')->references('id')->on('menu_item_ingredients');
            $table->foreign('cart_item_id')->references('id')->on('cart_items');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('side_dishes');
    }
};
