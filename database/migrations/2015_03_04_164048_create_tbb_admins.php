<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTbbAdmins extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tbb_admins', function(Blueprint $table)
		{
            $table->increments('id');
            $table->string('google_id')->nullable()->unique();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->integer('school_id_permission')->default('0'); // 0 is all
            $table->string('login_token')->nullable();
            $table->rememberToken();
			$table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('tbb_admins');
	}

}
