<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddCountAllEnrollmentsView extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(Config::get('database.default') == "mysql")
        {
            DB::statement("
                CREATE  OR REPLACE VIEW count_all_enrollments AS
                    SELECT
                        sections.id AS section_id,
                        enrollments.term AS term,
                        enrollments.department AS department,
                        enrollments.course AS course,
                        enrollments.section AS section,
                        COUNT(0) AS counted_enrollment
                    FROM
                        (enrollments
                        JOIN sections ON (((sections.term = enrollments.term)
                            AND (sections.department = enrollments.department)
                            AND (sections.course = enrollments.course)
                            AND (sections.section = enrollments.section))))
                    WHERE
                        ((enrollments.b_delete = '0')
                            AND (enrollments.enabled = '1')
                            AND (sections.b_delete = '0')
                            AND (sections.enabled = '1'))
                    GROUP BY enrollments.term , enrollments.course , enrollments.section
                    ORDER BY enrollments.term , enrollments.course , enrollments.section DESC
            ");
        }
        elseif(Config::get('database.default') == "pgsql")
        {
            DB::statement("
                CREATE OR REPLACE VIEW count_all_enrollments AS
                SELECT
                    sections.id            AS section_id,
                    enrollments.term       AS term,
                    enrollments.department AS department,
                    enrollments.course     AS course,
                    enrollments.section    AS section,
                    COUNT(0)               AS counted_enrollment
                FROM
                    (enrollments
                JOIN
                    sections ON (((sections.term     = enrollments.term)
                            AND (sections.department = enrollments.department)
                            AND (sections.course     = enrollments.course)
                            AND (sections.section    = enrollments.section))))
                WHERE
                    ((enrollments.b_delete       = false)
                        AND (enrollments.enabled = true)
                        AND (sections.b_delete   = false)
                        AND (sections.enabled    = true))
                GROUP BY
                    enrollments.term ,
                    enrollments.department,
                    enrollments.course ,
                    enrollments.section,
                    sections.id
                ORDER BY
                    enrollments.term ,
                    enrollments.course ,
                    enrollments.section DESC
            ");
        }
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("
			DROP VIEW count_all_enrollments;
		");
    }

}
