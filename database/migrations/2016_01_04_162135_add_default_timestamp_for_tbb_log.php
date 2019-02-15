<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDefaultTimestampForTbbLog extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tbb_log', function($table)
		{
			$table->dateTime('timestamp')->default(DB::raw('CURRENT_TIMESTAMP'))->change();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('tbb_log', function($table)
		{
			$table->dateTime('timestamp')->nullable()->change();
		});
	}

}
