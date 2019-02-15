<?php namespace DataStaging\Adjusters;

use DataStaging\Models\School;
use DataStaging\Util;
use DB;
use Log;

/*
 * This class detects invalid enrollments.
 * INVALID is defined as a current enrollment in the file where the corresponding student does not exist in the file.
 */
class NotifyInvalidEnrollmentsAdjuster
{
    public function __construct(School $school)
    {
        Log::info("------------\n\nDETECT INVALID ENROLLMENTS: any enrollments where student doesn't exist?\n\n");

        $invalidEnrollments = $this->getInvalidEnrollments($school->getKey());

        if(!empty($invalidEnrollments))
        {
            Log::alert('There were invalid enrollments. The customer for these enrollments does not exist: ', Util::stringifyCollection($invalidEnrollments));
            return;
        }

        Log::info("No invalid Enrollments found.\n");
    }

    /**
     * Get enrollments, left join all active customers.
     * Return any enrollments for which a customer can't be found (i.e. where customer_id IS NULL) for the given school.
     * @param $schoolId
     * @return array
     */
    public function getInvalidEnrollments($schoolId)
    {
        return DB::select("
            SELECT
                e.id,
                e.school_id,
                e.student_id as customer_acct,
                e.campus,
                e.term,
                e.department,
                e.course,
                e.section
            FROM
                enrollments AS e
            LEFT JOIN
                (
                    SELECT
                        cust.id as customer_id,
                        cust.customer_acct,
                        cust.school_id
                    FROM
                        customers as cust
                    WHERE
                        enabled = true
                        AND b_delete = false
                ) AS c
            ON (
                    e.student_id = c.customer_acct
                    AND e.school_id = c.school_id
               )
            WHERE
                c.customer_id IS NULL
                AND e.school_id = $schoolId
                AND e.enabled  = true
                AND e.b_delete = false;
        ");
    }
}