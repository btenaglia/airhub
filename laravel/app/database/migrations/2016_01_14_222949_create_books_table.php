<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBooksTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('books', function($table) {
            $table->increments('id');
            $table->string('complete_name');
            $table->float('body_weight');
            $table->float('luggage_weight');
            $table->string('address');
            $table->string('cell_phone');
            $table->string('email');

            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            
            $table->integer('flight_id')->unsigned();
            $table->foreign('flight_id')->references('id')->on('flights');
            
            $table->integer('payment_id')->unsigned();
            $table->foreign('payment_id')->references('id')->on('payments');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('books', function($table) {
            $table->dropForeign('books_flight_id_foreign');
            $table->dropForeign('books_user_id_foreign');
            $table->dropForeign('books_payment_id_foreign');
        });
        Schema::dropIfExists('books');
        
    }

}
