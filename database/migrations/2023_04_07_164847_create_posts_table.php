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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->text("title", 64);
            $table->text("body");
            $table->tinyInteger("published");
            $table->unsignedInteger("restaurantId");
            $table->unsignedInteger("created_by")->nullable();
            $table->unsignedInteger("edited_by")->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete("cascade");
            $table->foreign('edited_by')->references('id')->on('users')->onDelete("cascade");
            $table->foreign('restaurantId')->references('id')->on('restaurants')->onDelete("cascade");
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
        Schema::dropIfExists('posts');
    }
};
