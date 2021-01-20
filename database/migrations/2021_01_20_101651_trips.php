<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Trips extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trips', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->integer('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('status')->default('active');
            $table->string('start');
            $table->string('destination');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->string('pick_up_place');
            $table->string('location');
            $table->string('number_of_passengers');
            $table->string('passenger_fare');
            $table->string('car_type');
            $table->string('car_photo');
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
        Schema::dropIfExists('trips');
    }
}
