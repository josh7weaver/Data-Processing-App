<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCoursesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('courses', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('school_id')->unsigned();
            $table->foreign('school_id')->references('id')->on('schools');
			$table->text('campus');
			$table->text('term');
			$table->text('department');
			$table->text('course');
			$table->text('description');
			$table->text('comment');
			$table->boolean('b_delete')->index();
			$table->boolean('enabled')->index();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));

			$table->unique(['campus', 'term', 'department', 'course']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('courses');
	}

}
