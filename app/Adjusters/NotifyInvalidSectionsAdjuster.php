<?php namespace DataStaging\Adjusters;

use DataStaging\Models\School;
use DataStaging\Util;
use DB;
use Log;

/*
 * This class detects invalid sections.
 * INVALID is defined as a current section in the file where the corresponding course does not exist in the file.
 */
class NotifyInvalidSectionsAdjuster
{
    public function __construct(School $school)
    {
        Log::info("------------\n\nDETECT INVALID SECTIONS: any sections where course doesn't exist?\n\n");

        $invalidSections = $this->getInvalidSections($school->getKey());

        if(!empty($invalidSections))
        {
            Log::alert('There were invalid sections. The courses for these sections does not exist: ', Util::stringifyCollection($invalidSections));
            return;
        }

        Log::info("No invalid sections found.\n");
    }

    /**
     * Get all sections and left join courses.
     * Return any sections for which there is not a corresponding course (i.e. where course_id IS NULL) for the given school
     * @param $schoolId
     * @return array
     */
    public function getInvalidSections($schoolId)
    {
        return DB::select("
                SELECT
                    s.id,
                    s.school_id,
                    s.campus,
                    s.term,
                    s.department,
                    s.course,
                    s.section
                FROM
                    sections AS s
                LEFT JOIN
                    (
                         SELECT
                                crs.id AS course_id,
                                crs.campus,
                                crs.term,
                                crs.department,
                                crs.course
                         FROM
                                courses AS crs
                         WHERE
                                enabled      = true
                                AND b_delete = false
                    ) AS c
                    ON (
                            s.campus = c.campus
                            AND s.term       = c.term
                            AND s.department = c.department
                            AND s.course     = c.course
                        )
                WHERE
                    c.course_id IS NULL
                    AND s.school_id = $schoolId
                    AND s.enabled     = true
                    AND s.b_delete    = false;
            ");
    }
}