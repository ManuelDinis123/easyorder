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
        Schema::table('types', function (Blueprint $table) {
            $table->tinyInteger("view_orders")->default(0);
            $table->tinyInteger("write_orders")->default(0);
            $table->tinyInteger("view_menu")->default(0);
            $table->tinyInteger("write_menu")->default(0);
            $table->tinyInteger("view_stats")->default(0);
            $table->tinyInteger("invite_users")->default(0);
            $table->tinyInteger("ban_users")->default(0);
            $table->tinyInteger("admin")->default(0);
            $table->tinyInteger("owner")->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('types', function (Blueprint $table) {
            $table->dropColumn("view_orders");
            $table->dropColumn("write_orders");
            $table->dropColumn("view_menu");
            $table->dropColumn("write_menu");
            $table->dropColumn("view_stats");
            $table->dropColumn("invite_users");
            $table->dropColumn("ban_users");
            $table->dropColumn("admin");
            $table->dropColumn("owner");
        });
    }
};
