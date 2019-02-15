<?php namespace DataStaging\Adjusters;

use DataStaging\Contracts\Adjuster;
use DataStaging\Models\School;
use DB;
use Log;

class RemoveOptedOutEnrollmentsAdjuster implements Adjuster
{
    /**
     * schools.code column in DB
     * @var mixed
     */
    private $schoolCode;

    /**
     * optional flag to limit adjuster to given division name (divisions.name in DB)
     * @var string
     */
    private $limitToDivision;

    /**
     * @var School
     */
    private $school;

    /**
     * Data structure in the form 'School code' => 'Division Name'
     * currently only supports EITHER entire school or single division
     * null value = all divisions
     */
    const SCHOOLS_TO_ADJUST = [
        'TEST' => 'TEST DIVISION',
    ];

    /**
     * RemoveOptedOutEnrollmentsAdjuster constructor.
     * @param School $school
     */
    public function __construct(School $school)
    {
        $this->school = $school;
        $this->schoolCode = $school->getCode();
    }

    public function adjust()
    {
        // Only run for specific schools
        if(!array_key_exists($this->schoolCode, static::SCHOOLS_TO_ADJUST)) return false;

        $divisionName = static::SCHOOLS_TO_ADJUST[$this->schoolCode];

        if($divisionName){
            Log::info("------------\n\nRemoving enrollments where customer is opted out for School Code: {$this->schoolCode}, Division: $divisionName.\n\n");
            $results = $this->setLimitToDivision($divisionName)
                            ->removeEnrollmentsForDivision();
        }
        else{
            Log::info("------------\n\nRemoving enrollments where customer is opted out for School Code: {$this->schoolCode}\n\n");
            $results = $this->removeEnrollmentsForEntireSchool();
        }

        Log::info("$results items updated\n");
    }

    /**
     * Joining sections in this one because the section has a reliable division
     *   name and the enrollments does NOT.
     * @return int
     */
    public function removeEnrollmentsForDivision()
    {
        return DB::update("
            UPDATE enrollments SET enabled = false
            WHERE id IN
            (
                SELECT e.id FROM enrollments e
                JOIN
                    customers c ON (c.customer_acct = e.student_id
                        AND c.school_id             = e.school_id)
                JOIN
                    sections s ON ( e.term = s.term
                        AND e.department   = s.department
                        AND e.course       = s.course
                        AND e.section      = s.section )
                WHERE
                    c.school_id = ( SELECT id FROM schools WHERE code = :school_code )
                    AND s.campus = :division_name
                    AND c.comment ILIKE '%out%'
                    AND c.b_delete = false
                    AND c.enabled  = true
                    AND e.b_delete = false
                    AND e.enabled  = true
            );
        ", [
            ':school_code' => $this->schoolCode,
            ':division_name' => $this->limitToDivision
        ]);
    }

    public function removeEnrollmentsForEntireSchool()
    {
        return DB::update("
            UPDATE enrollments SET enabled = false
            WHERE id IN
            (
                SELECT e.id FROM enrollments e
                JOIN
                    customers c ON (c.customer_acct = e.student_id
                        AND c.school_id             = e.school_id)
                WHERE
                    c.school_id = ( SELECT id FROM schools WHERE code = :school_code )
                    AND c.comment ILIKE '%out%'
                    AND c.b_delete = false
                    AND c.enabled  = true
                    AND e.b_delete = false
                    AND e.enabled  = true
            );
        ", [
            ':school_code' => $this->schoolCode
        ]);
    }

    /**
     * @param mixed $limitToDivision
     * @return RemoveOptedOutEnrollmentsAdjuster
     */
    public function setLimitToDivision($limitToDivision)
    {
        $this->limitToDivision = $limitToDivision;
        return $this;
    }
}