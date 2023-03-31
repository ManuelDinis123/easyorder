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
        Schema::table('order_items', function (Blueprint $table) {
            $table->integer("price")->default(0);
            $table->integer("cost")->default(0);
        });
        Schema::table('order_items_sides', function (Blueprint $table) {
            $table->integer("price")->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn("price");
            $table->dropColumn("cost");
        });
        Schema::table('order_items_sides', function (Blueprint $table) {
            $table->dropColumn("price");
        });
    }
};
