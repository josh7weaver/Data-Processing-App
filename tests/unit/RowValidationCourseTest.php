<?php

use DataStaging\Models\Course;
use DataStaging\Models\Customer;
use DataStaging\Models\Enrollment;
use Illuminate\Database\Eloquent;
use DataStaging\Models\Section;

class RowValidationCourseTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected $isInitialized;

    protected $courseFile = ["AU - UNDERGRADUATE", "2015CAUC", "MUPF", "MUPF 2830", "Trumpet/Cornet", "3", 0];
    protected $course;
    protected $school;

    protected function _before()
    {
        if(!$this->isInitialized)
        {
            $this->school = \DataStaging\Models\School::firstOrFail();
            $this->course = Course::newInstanceFromFile($this->courseFile, $this->school);

            $this->isInitialized = true;
        }

        $this->setExpectedException('DataStaging\Exceptions\RowValidationException');
    }

    public function testCourseInvalidDivisionNameThrowsException()
    {
        $course = $this->course;
        $course->campus = "INVALID DIVISION NAME";
        $course->validate();
    }

    /**
     * VALIDATE COLUMN COUNTS
     */
    public function testInvalidCourseColumnsThrowsException()
    {
        $course = $this->course;
        $tooFewColumns = $course->getOriginRow();
        array_shift($tooFewColumns);

        $course->setOriginRow($tooFewColumns);
        \DataStaging\RowValidator::checkColumnCountMatchesExpected($tooFewColumns, $course->getExpectedColumnCount());
    }
}