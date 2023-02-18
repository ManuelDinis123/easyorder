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
        Schema::table('menu_item_ingredients', function (Blueprint $table) {
            $table->enum('quantity_type', ["numeric", "dose"])->default("numeric");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('menu_item_ingredients', function (Blueprint $table) {
            $table->dropColumn('quantity_type');
        });
    }
};
