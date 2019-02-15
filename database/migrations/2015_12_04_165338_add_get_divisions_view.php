<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddGetDivisionsView extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement("
			CREATE  OR REPLACE VIEW getdivisions AS
			    SELECT
			        divisions.id AS division_id,
			        schools.name AS school_name,
			        divisions.name,
			        divisions.enrollment_percentage,
			        divisions.enabled
			    FROM
			        (schools
			        JOIN divisions ON ((divisions.school_id = schools.id)));
		");
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement("
			DROP VIEW getdivisions;
		");
	}

}
