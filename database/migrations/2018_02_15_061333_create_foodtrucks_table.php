<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFoodtrucksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('food_truck', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('user_id')->unsigned();
			$table->string('name');
			$table->string('image_path');
			$table->integer('sort_order');
			$table->enum('status', ['enable', 'disable']);
            $table->timestamps();
        });
		
		Schema::table('food_truck', function($table) {
			$table->foreign('user_id')->references('id')->on('user');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('foodtrucks');
    }
}
