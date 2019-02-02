<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('payments', function($table) {
            $table->increments('id');
            
            $table->string('external_payment_id');
            $table->string('currency');
            $table->float('amount');
            $table->string('description');
            $table->text('payment_json');
            $table->text('capture_json')->nullable();
            $table->text('capture_state')->nullable();
            $table->string('intent');
            $table->string('external_state');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('payments');
    }

}
