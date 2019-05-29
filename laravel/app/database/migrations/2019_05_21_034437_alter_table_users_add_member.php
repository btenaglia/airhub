<?php

use Illuminate\Database\Migrations\Migration;

class AlterTableUsersAddMember extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('users', function ($table) {

            $table->integer('member_id')->unsigned();
            $table->foreign('member_id')->references('id')->on('members');
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
		Schema::table('users', function($table) {
			$table->dropForeign('member_id');}
		);
    }

}
