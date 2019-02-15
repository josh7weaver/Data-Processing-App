<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTbbSchoolData extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tbb_school_data', function(Blueprint $table)
		{
            $table->integer('school_id')->unsigned()->primary();
            $table->foreign('school_id')->references('id')->on('schools');
            $table->string('slug');
            $table->boolean('use_butler')->index()->nullable();
            $table->string('default_pref')->nullable();
            $table->string('image')->nullable();
            $table->string('merch_link')->nullable();
            $table->string('textbook_link')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('tbb_school_data');
	}

}
