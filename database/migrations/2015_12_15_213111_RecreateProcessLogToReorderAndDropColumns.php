<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RecreateProcessLogToReorderAndDropColumns extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('schools', function($table)
		{
            $table->unique('code');
		});

		// Must first drop the schema bc postgres doesn't support re-ordering columns!
		Schema::drop('process_log');

		Schema::create('process_log', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('process_token')->nullable()->index();
			$table->text('school_code')->nullable()->index();
			$table->foreign('school_code')->references('code')->on('schools');
			$table->text('file_type')->nullable()->index(); // course/section/enroll/cust
			$table->text('validation_code')->nullable();
            $table->foreign('validation_code')->references('code')->on('validation_data');
			$table->text('level')->index();
			$table->smallInteger('level_code')->index();
			$table->text('message')->nullable();
			$table->text('context')->nullable();
			$table->text('channel')->nullable();
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
        // drop new version
        Schema::drop('process_log');

        // drop unique record needed for foreign keyg
        Schema::table('schools', function($table)
		{
            $table->dropUnique('schools_code_unique');
		});


		//create old one
		Schema::create('process_log', function(Blueprint $table)
		{
			$table->increments('id');
			$table->text('process_token')->nullable()->index();
			$table->integer('school_id')->nullable()->index();
			$table->text('school_code')->nullable();
			$table->text('channel')->nullable();
			$table->text('level')->nullable()->index();
			$table->text('message')->nullable();
			$table->text('context')->nullable();
			$table->timestamp('timestamp')->default(DB::raw('CURRENT_TIMESTAMP'));
		});
	}

}
