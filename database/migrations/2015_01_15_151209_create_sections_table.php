<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSectionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sections', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('school_id')->unsigned();
            $table->foreign('school_id')->references('id')->on('schools');
			$table->text('campus');
			$table->text('term');
			$table->text('department');
			$table->text('course');
			$table->text('section');
			$table->text('instructor');
			$table->integer('est_enrollment');
			$table->integer('act_enrollment');
			$table->text('comment');
			$table->boolean('b_delete')->index();
			$table->boolean('enabled')->index();
			$table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->index(['term', 'department', 'course', 'section']); // for performant relating to enrollments - section has many enrollments
			$table->unique(['campus', 'term', 'department', 'course', 'section']);
		});


	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('sections');
	}

}
