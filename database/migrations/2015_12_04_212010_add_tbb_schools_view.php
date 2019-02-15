<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTbbSchoolsView extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
    public function up()
    {
        DB::statement("
			CREATE  OR REPLACE VIEW tbb_schools AS
			    SELECT
                    schools.id AS id,
                    schools.name AS name,
                    tbb_school_data.slug AS slug,
                    tbb_school_data.default_pref AS default_pref,
                    schools.enabled AS enabled,
                    tbb_school_data.use_butler AS use_butler
                FROM
                (schools
                    JOIN tbb_school_data ON ((schools.id = tbb_school_data.school_id)))
                WHERE
                ((schools.enabled = '1')
                    AND (tbb_school_data.use_butler IS NOT NULL));
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
			DROP VIEW tbb_schools;
		");
    }

}
