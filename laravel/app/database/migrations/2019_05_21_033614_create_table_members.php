<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableMembers extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::create('members', function($table) {
            $table->increments('id');
            $table->string('description');
            $table->float('discount');
       

            // $table->integer('user_id')->unsigned();
            // $table->foreign('user_id')->references('id')->on('users');
            
            // $table->integer('flight_id')->unsigned();
            // $table->foreign('flight_id')->references('id')->on('flights');
            
            // $table->integer('payment_id')->unsigned();
            // $table->foreign('payment_id')->references('id')->on('payments');
            
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
		//
		Schema::drop('members');
	}

}
