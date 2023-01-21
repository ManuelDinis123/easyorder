<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::create('restaurant_type', function (Blueprint $table) {
            $table->unsignedInteger("id")->autoIncrement();
            $table->string('label');            
        });

        // Insert the types
        DB::table('restaurant_type')->insert(
            array(                
                'label' => "restaurant"
            )
        );
        DB::table('restaurant_type')->insert(
            array(                
                'label' => "takeaway"
            )
        );
        DB::table('restaurant_type')->insert(
            array(                
                'label' => "delivery"
            )
        );
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('restaurant_type');
    }
};
