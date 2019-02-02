<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFlightTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('flights', function($table) {
            $table->increments('id');
            $table->time('departure_time')->nullable();
            $table->date('departure_date');
            $table->string('status');
            $table->time('departure_min_time')->nullable();
            $table->time('departure_max_time')->nullable();

            $table->integer('origin')->unsigned();
            $table->foreign('origin')->references('id')->on('places');

            $table->integer('destination')->unsigned();
            $table->foreign('destination')->references('id')->on('places');

            $table->integer('created_by')->unsigned();
            $table->foreign('created_by')->references('id')->on('users');

            $table->integer('plane_id')->unsigned()->nullable();
            $table->foreign('plane_id')->references('id')->on('planes');

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
        Schema::table('flights', function($table) {
            $table->dropForeign('flights_plane_id_foreign');
            $table->dropForeign('flights_created_by_foreign');
            $table->dropForeign('flights_destination_foreign');
            $table->dropForeign('flights_origin_foreign');
        });

        Schema::dropIfExists('flights');
    }

}
