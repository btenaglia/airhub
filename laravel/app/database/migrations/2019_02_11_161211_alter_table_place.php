<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTablePlace extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('places', function(Blueprint $table)
		{
			//
			$table->double('latitude')->nullable();
			$table->double('longitude')->nullable();
		});
		Schema::table('flights', function(Blueprint $table)
		{
			$table->integer('distance')->nullable();
			$table->string('route');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('places', function(Blueprint $table)
		{
			//
			$table->dropColumn('latitude');
			$table->dropColumn('longitude');
		});
		Schema::table('flights', function(Blueprint $table)
		{
			//
			$table->integer('distance');
			$table->string('route');
		});
	}

}
