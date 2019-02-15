<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProcessLogTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('process_log', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('process_token')->nullable()->index();
            $table->integer('school_id')->nullable()->index();
			$table->text('school_code')->nullable();
			$table->text('channel')->nullable();
			$table->text('level')->nullable()->index();
			$table->text('message')->nullable();
			$table->text('context')->nullable();
			$table->timestamp('timestamp')->default(DB::raw('CURRENT_TIMESTAMP'));
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('process_log');
	}

}
