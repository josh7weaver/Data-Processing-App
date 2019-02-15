<?php

use DataStaging\Models\Course;
use DataStaging\Models\Customer;
use DataStaging\Models\Enrollment;
use Illuminate\Database\Eloquent;
use DataStaging\Models\Section;

class RowValidationEnrollmentTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected $isInitialized;

    protected $enrollmentFile = ["2099670", "AUC", "2015AAUC", "PSYC", "PSYC 2440", "01", "2015/01/14", 0];
    protected $enrollment;
    protected $school;

    protected function _before()
    {
        if(!$this->isInitialized)
        {
            $this->school = \DataStaging\Models\School::firstOrFail();
            $this->enrollment = Enrollment::newInstanceFromFile($this->enrollmentFile, $this->school);

            $this->isInitialized = true;
        }

        $this->setExpectedException('DataStaging\Exceptions\RowValidationException');
    }

    /**
     * check column count
     */
    public function testInvalidEnrollmentColumnsThrowsException()
    {
        $enrollment = $this->enrollment;
        $tooManyColumns = $this->enrollment->getOriginRow();
        $tooManyColumns = array_merge($tooManyColumns, ['too many columns!!']);

        $enrollment->setOriginRow($tooManyColumns);
        \DataStaging\RowValidator::checkColumnCountMatchesExpected($tooManyColumns, $enrollment->getExpectedColumnCount());
    }

//    /**
//     * CHECK Customer EXISTS
//     */
//    public function testInvalidCustomerThrowsException()
//    {
//        $enrollment = $this->enrollment;
//        $enrollment->setAttribute('student_id', 'NON_EXISTENT_ID');
//        $enrollment->getValidator()->checkCustomerExists();
//    }
//
//    public function testValidCustomerPasses()
//    {
//        $this->setExpectedException(null);
//
//        $customer = $this->school->customers()->enabled()->firstOrFail();
//
//        $enrollment = $this->enrollment;
//        $enrollment->setAttribute('school_id', $customer->school->getKey());
//        $enrollment->setAttribute('student_id', $customer->getCustomerAcct());
//        $enrollment->getValidator()->checkCustomerExists();
//    }
//
//    /**
//     * CHECK Section EXISTS
//     */
//    public function testInvalidSectionThrowsException()
//    {
//        $enrollment = $this->enrollment;
//        $enrollment->setDivision('INVALID_DIVISION');
//        $enrollment->setAttribute('term', 'asdf');
//        $enrollment->setAttribute('department', 'asd');
//        $enrollment->setAttribute('course', 'asdf');
//        $enrollment->setAttribute('section', 'asdf');
//        $enrollment->getValidator()->checkSectionExists();
//    }
//
//    public function testValidSectionPasses()
//    {
//        $this->setExpectedException(null);
//
//        $section = $this->school->sections()->enabled()->firstOrFail();
//
//        $enrollment = $this->enrollment;
//        $enrollment->setDivision($section->getDivision());
//        $enrollment->setAttribute('term', $section->getTerm());
//        $enrollment->setAttribute('department', $section->getDepartment());
//        $enrollment->setAttribute('course', $section->getCourse());
//        $enrollment->setAttribute('section', $section->getSection());
//        $enrollment->getValidator()->checkSectionExists();
//    }
}