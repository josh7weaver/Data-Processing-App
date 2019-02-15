<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCckTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('rocket_students', function(Blueprint $table)
		{
			$table->text('student_id')->nullable();
			$table->text('first_name')->nullable();
			$table->text('last_name')->nullable();
			$table->text('primary_email')->nullable();
			$table->text('other_emails')->nullable();
			$table->text('birthdate')->nullable();
			$table->text('phone')->nullable();
			$table->text('year_in_school')->nullable();
			$table->text('program')->nullable();
			$table->text('subprogram')->nullable();
			$table->text('sms_opt_in')->nullable();
			$table->text('ship_address')->nullable();
			$table->text('ship_city')->nullable();
			$table->text('ship_state')->nullable();
			$table->text('ship_postal_code')->nullable();
			$table->text('ship_country')->nullable();
		});

		Schema::create('rocket_courses', function(Blueprint $table)
		{
			$table->text('campus')->nullable();
			$table->text('term')->nullable();
			$table->text('term_start_date')->nullable();
			$table->text('term_end_date')->nullable();
			$table->text('section_start_date')->nullable();
			$table->text('section_end_date')->nullable();
			$table->text('department')->nullable();
			$table->text('department_name')->nullable();
			$table->text('course')->nullable();
			$table->text('course_name')->nullable();
			$table->text('section')->nullable();
			$table->text('credit_hours')->nullable();
			$table->text('professor')->nullable();
			$table->text('professor_id')->nullable();
			$table->text('enrollment_capacity')->nullable();
			$table->text('enrollment_estimated')->nullable();
			$table->text('enrollment_actual')->nullable();
			$table->text('faculty_id')->nullable();
		});

		Schema::create('rocket_enrollments', function(Blueprint $table)
		{
			$table->text('student_id')->nullable();
			$table->text('campus_id')->nullable();
			$table->text('term_id')->nullable();
			$table->text('dept_id')->nullable();
			$table->text('course_id')->nullable();
			$table->text('section_id')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('rocket_students');
		Schema::drop('rocket_courses');
		Schema::drop('rocket_enrollments');
	}

}
