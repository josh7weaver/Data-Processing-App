<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEnrollmentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('enrollments', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('school_id')->unsigned();
            $table->foreign('school_id')->references('id')->on('schools');
			$table->text('student_id');
			$table->text('campus');
			$table->text('term');
			$table->text('department');
			$table->text('course');
			$table->text('section');
			$table->text('comment')->nullable();
			$table->boolean('b_delete')->index();
			$table->boolean('enabled')->index();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->index(['term', 'department', 'course', 'section']); // for performant relating to section: enrollment belongs to a section
		});

		if(Config::get('database.default') == "mysql"){
			// Have to do this unique key constraint manually since
			// the generated key name is too long thanks to all the column names!
			DB::statement("
				ALTER TABLE enrollments
				ADD UNIQUE INDEX unique (campus ASC, student_id ASC, term ASC, department ASC, course ASC, section ASC);
			");
		}
		elseif(Config::get('database.default') == "pgsql")
		{
			DB::statement("
				ALTER TABLE enrollments
				ADD CONSTRAINT studentid_locator_unique UNIQUE (campus, student_id, term, department, course, section);
			");
		}
		else{
			throw new Exception("the database driver is not supported currently");
		}
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('enrollments');
	}

}
