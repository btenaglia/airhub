<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUsers extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('users', function($table) {
            $table->increments('id');
            $table->string('complete_name');
            $table->string('address');
            $table->string('cell_phone');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('user_type');
            $table->float('body_weight');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('users');
    }

}
