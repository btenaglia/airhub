<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlanesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('planes', function($table) {
            $table->increments('id');
            $table->string('identifier')->unique();
            $table->string('name');
            $table->string('type');
            $table->integer('seats_limit');
            $table->float('weight_limit');
            //$table->string('image_path');
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
        Schema::dropIfExists('planes');
    }

}
