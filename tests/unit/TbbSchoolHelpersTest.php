<?php

use DataStaging\Models\School;
use DataStaging\Models\TbbSchool;
use DataStaging\Traits\TbbSchoolHelpers;

class TbbSchoolHelpersTest extends \Codeception\TestCase\Test
{
    use TbbSchoolHelpers;

    /**
     * @var \UnitTester
     */
    protected $tester;

    protected $tbbSchoolIds;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    protected function getTbbSchoolIds(){
        if(!$this->tbbSchoolIds){
            $this->tbbSchoolIds = TbbSchool::lists('school_id');
        }

        return $this->tbbSchoolIds;
    }

    // tests
    public function testIsTbbSchool()
    {
        $this->tester->wantTo('Know whether the current school is a Textbook butler school or not');
        $this->tester->expect('School IS a textbook butler school');

        $school = TbbSchool::first()->schoolSettings()->first();

        // its a trait, see use statement above
        $result = $this->isTbbSchool($school);

        $this->assertTrue($result);
    }

    public function testIsNotTbbSchool()
    {
        $this->tester->wantTo('Know whether the current school is a Textbook butler school or not');
        $this->tester->expect('School is NOT a textbook butler school');

        $tbbSchoolIds = $this->getTbbSchoolIds();
        $school = School::whereNotIn('id', $tbbSchoolIds)->first();

        // its a trait, see use statement above
        $result = $this->isTbbSchool($school);

        $this->assertFalse($result);
    }

    public function testIsNotTbbSchoolByInverting()
    {
        $this->tester->wantTo('Know whether the current school is a Textbook butler school or not');
        $this->tester->expect('School is NOT a textbook butler school using the not method');

        $tbbSchoolIds = $this->getTbbSchoolIds();
        $school = School::whereNotIn('id', $tbbSchoolIds)->first();

        // its a trait, see use statement above
        $result = $this->isNotTbbSchool($school);

        $this->assertTrue($result);
    }
}