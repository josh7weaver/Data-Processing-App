<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCountTbbEnrollmentsView extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
    public function up()
    {
        /*
         * COUNT_TBB_ENROLLMENTS
         * This view counts all the enrollments for each section (by grouping and counting)
         *
         * The section table is joined simply to provide the section_id to the view
         *
         * The divisions table is joined in order to determine which of the enrollments
         * are actually TBB enrollments (i.e. enrollments from sections belonging to divisions that use TBB)
         *
         * The subquery on the customers table in the WHERE clause is performed to weed out students who
         * have selected "opt out" as their preference. We don't want to count opted out students.
         */
        if(Config::get('database.default') == "mysql")
        {
            DB::statement("
                CREATE OR REPLACE VIEW count_tbb_enrollments AS
                SELECT
                    sections.school_id     AS school_id,
                    divisions.name         AS division_name,
                    sections.id            AS section_id,
                    enrollments.term       AS term,
                    enrollments.department AS department,
                    enrollments.course     AS course,
                    enrollments.section    AS section,
                    COUNT(0)                   AS counted_enrollment
                FROM
                    ((enrollments
                LEFT JOIN
                    sections
                 ON
                    (((sections.term             = enrollments.term)
                        AND (sections.department = enrollments.department)
                        AND (sections.course     = enrollments.course)
                        AND (sections.section    = enrollments.section))))
                JOIN
                    divisions
                 ON
                    ((sections.campus = divisions.name)))
                WHERE
                    ((divisions.use_butler = '1')
                    AND enrollments.student_id IN
                        (
                         SELECT
                                customers.customer_acct
                           FROM
                                customers
                          WHERE
                                ((customers.b_delete           = '0')
                                AND (customers.enabled         = '1')
                                AND (NOT((customers.comment LIKE '%out%')))
                                AND (trim(customers.comment)  <> '')))
                    AND (enrollments.b_delete                  = '0')
                    AND (enrollments.enabled                   = '1')
                    AND (sections.b_delete                     = '0')
                    AND (sections.enabled                      = '1'))
                GROUP BY
                    enrollments.term,
                    enrollments.course,
                    enrollments.section
                ORDER BY
                    enrollments.term,
                    enrollments.course,
                    enrollments.section DESC;
            ");
        }
        elseif(Config::get('database.default') == "pgsql")
        {
            DB::statement("
                CREATE OR REPLACE VIEW count_tbb_enrollments AS
                SELECT
                    sections.school_id     AS school_id,
                    divisions.name         AS division_name,
                    sections.id            AS section_id,
                    enrollments.term       AS term,
                    enrollments.department AS department,
                    enrollments.course     AS course,
                    enrollments.section    AS section,
                    COUNT(0)               AS counted_enrollment
                FROM
                    ((enrollments
                LEFT JOIN
                    sections ON (((sections.term     = enrollments.term)
                            AND (sections.department = enrollments.department)
                            AND (sections.course     = enrollments.course)
                            AND (sections.section    = enrollments.section))))
                JOIN
                    divisions ON ((sections.campus = divisions.name)))
                WHERE
                    (divisions.use_butler = true)
                    AND enrollments.student_id IN
                    (
                        SELECT
                            customers.customer_acct
                        FROM
                            customers
                        WHERE
                            (customers.b_delete               = false)
                                AND (customers.enabled         = true)
                                AND (customers.comment NOT ILIKE '%out%')
                                AND (btrim(customers.comment)  != '')
                    )
                    AND (enrollments.b_delete                  = false)
                    AND (enrollments.enabled                   = true)
                    AND (sections.b_delete                     = false)
                    AND (sections.enabled                      = true)
                GROUP BY
                    sections.school_id,
                    enrollments.term,
                    divisions.name,
                    enrollments.department,
                    enrollments.course,
                    sections.id,
                    enrollments.section
                ORDER BY
                    enrollments.term,
                    enrollments.course,
                    enrollments.section DESC;
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
			DROP VIEW count_tbb_enrollments;
		");
    }

}
