<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGetsectionsView extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        /*
         * This view exists for the sycamore import process.
         *
         * It is being used because there is no way to relate the school
         * and division to the section/term etc that you're looking at via the API
         */
        DB::statement("
			CREATE OR REPLACE VIEW getsections AS
            SELECT
                sections.school_id AS school_id,
                schools.code AS school_code,
                sections.campus AS division,
                sections.term AS term,
                sections.department AS department,
                sections.course AS course,
                sections.section AS section,
                sections.est_enrollment AS capacity,
                sections.act_enrollment AS act_enrollment,
                sections.comment AS comment,
                sections.b_delete AS b_delete,
                courses.description AS description
            FROM
                ((sections
                JOIN courses ON (((sections.campus = courses.campus)
                    AND (sections.term = courses.term)
                    AND (sections.department = courses.department)
                    AND (sections.course = courses.course))))
                JOIN schools ON ((sections.school_id = schools.id)))
            WHERE
                (sections.enabled = true)
            ORDER BY sections.campus , sections.term , sections.department , sections.course
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
			DROP VIEW getsections;
		");
	}

}
