<?php

use Illuminate\Database\Eloquent;
use DataStaging\Models\Section;

class RowValidationSectionTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    protected $isInitialized;
    protected $section;
    protected $sectionFile = ["AU - UNDERGRADUATE", "2015AAUC", "POSC", "POSC 3310", "01", "15480213545", 25, 24, "2015/01/14 - 2015/05/07", 0];
    protected $school;

    protected function _before()
    {
        if(!$this->isInitialized)
        {
            $this->school = \DataStaging\Models\School::firstOrFail();
            $this->section = Section::newInstanceFromFile($this->sectionFile, $this->school);

            $this->isInitialized = true;
        }

        $this->setExpectedException('DataStaging\Exceptions\RowValidationException');
    }

    /**
     * DIVISION NAME VALIDATION
     */
    public function testSectionInvalidDivisionNameThrowsException()
    {
        $section = $this->section;
        $section->campus = "INVALID DIVISION NAME";
        $section->getValidator()->checkCampusFieldIsValidDivisionName();
    }

    /**
     * SECTION START/END DATE VALIDATION
     */
    public function testValidSectionDatesDoNotThrowException()
    {
        $this->setExpectedException(null);
        $section = $this->section;
        $section->comment = "2015/01/14 - 2015/01/30";
        $section->getValidator()->checkEndDateIsAfterStartDate();
    }

    // testing for end date must be after start date
    // validation checks
    public function testSectionEndDateBeforeStartDateThrowsException()
    {
        $section = $this->section;
        $section->comment = "2015/01/14 - 2015/01/01";
        $section->getValidator()->checkEndDateIsAfterStartDate();
    }

    public function testWeirdSectionEndDateBeforeStartDateThrowsException()
    {
        $section = $this->section;
        $section->comment = "asdf 2015/01/14 - 2015/01/01 Winona Lake";
        $section->getValidator()->checkEndDateIsAfterStartDate();
    }

    // testing for blank or unextractable start end dates don't break
    //  section start/end date validation checks
    public function testBlankCommentThrowsException()
    {
        $section = $this->section;
        $section->comment = "";
        $section->getValidator()->checkEndDateIsAfterStartDate();
    }

    public function testUnextractableDateThrowsException()
    {
        $section = $this->section;
        $section->comment = "asdf2015/01/14 - 2015/01/01 Winona Lake";
        $section->getValidator()->checkEndDateIsAfterStartDate();
    }

    public function testUnextractableDateThrowsException2()
    {
        $section = $this->section;
        // expecting pattern YYYY/mm/dd - YYYY/mm/dd OR YYYY/mm/dd YYYY/mm/dd
        $section->comment = "2015/01/14 through 2015/01/30";
        $section->getValidator()->checkEndDateIsAfterStartDate();
    }

    public function testCancelledSectionDoesNotThrowException()
    {
        $this->setExpectedException(null);
        $section = $this->section;
        $section->comment = "Cancelled";
        $section->b_delete = 1;
        $section->getValidator()->checkEndDateIsAfterStartDate();
    }

//    /**
//     * CHECK COURSE EXISTS
//     */
//    public function testInvalidCourseThrowsException()
//    {
//        $section = $this->section;
//
//        $section->setDivision('INVALID_DIV');
//        $section->setAttribute('term', 'term');
//        $section->setAttribute('department', 'invalid_department');
//        $section->setAttribute('course', 'invalid_course');
//
//        $section->getValidator()->checkCourseExists();
//    }
//
//    public function testValidCoursePasses()
//    {
//        $this->setExpectedException(null);
//
//        $course = $this->school->courses()->enabled()->firstOrFail();
//        $section = $this->section;
//
//        $section->setDivision($course->getDivision());
//        $section->setAttribute('term', $course->getTerm());
//        $section->setAttribute('department', $course->getDepartment());
//        $section->setAttribute('course', $course->getCourse());
//
//        $section->getValidator()->checkCourseExists();
//    }
//
//    /**
//     * CHECK INSTRUCTOR EXISTS
//     */
//    public function testInvalidInstructorThrowsException()
//    {
//        $section = $this->section;
//        $section->setAttribute('instructor', 'non existent instructor');
//        $section->getValidator()->checkInstructorExists();
//    }
//
//    public function testValidInstructorPasses()
//    {
//        $this->setExpectedException(null);
//        $customer = $this->school->customers()->enabled()->firstOrFail();
//
//        $section = $this->section;
//        $section->setAttribute('instructor', $customer->getInstructor());
//        $section->getValidator()->checkInstructorExists();
//    }
}