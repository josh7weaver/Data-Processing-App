<?php


use DataStaging\Adjusters\RemoveOptedOutEnrollmentsAdjuster;
use DataStaging\Models\Enrollment;
use DataStaging\Models\School;

class RemoveOptedOutEnrollmentsAdjusterTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
//        DB::beginTransaction();
    }

    protected function _after()
    {
//        DB::rollBack();
    }

    public function testAdjustForAsbury()
    {
        $asbury = School::where('code','ABS')->firstOrFail();
        $this->tester->haveRecord('customers', [
            'school_id'=>$asbury->getKey(),
            'customer_acct' => '10000000',
            'student_acct' => '',
            'firstname' => '',
            'lastname' => '',
            'email' => '',
            'comment' => 'Opted Out',
            'b_delete' => false,
            'enabled'=> true
        ]);

        $asburyEnrollmentId = $this->tester->haveRecord('enrollments', [
            'school_id'=>$asbury->getKey(),
            'student_id' => '10000000',
            'campus' => '',
            'term'=>'',
            'department'=>'',
            'course'=>'',
            'section'=>'',
            'section' => 'This is an enrollment for Asbury',
            'b_delete' => false,
            'enabled'=> true
        ]);

//        dump($asburyEnrollmentId);
        $this->tester->seeRecord('enrollments', ['id'=>$asburyEnrollmentId, 'enabled'=>true]);
//        dump("found enrollment with eneabled TRUE");
        $adjuster = new RemoveOptedOutEnrollmentsAdjuster($asbury);
        $adjuster->adjust();

        $this->tester->expect("the enrollment for customer who is opted out should be DISABLED");
        $this->tester->seeRecord('enrollments', ['id'=>$asburyEnrollmentId, 'enabled'=>false]);
    }

}