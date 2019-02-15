<?php

class StudentAddressAdjusterCest
{
    protected $schoolId;
    protected $where;
    protected $finalAddress;
    protected $beforeCustAttributes;
    protected $blankAddress;
    protected $finalCustAttributes;
    protected $controlCustomer;

    public function _before(FunctionalTester $I)
    {
    }

    public function _after(FunctionalTester $I)
    {
    }

    private function runAdjusterAndAssert(FunctionalTester $I)
    {
        // INSERT RECORDS
        $school = \DataStaging\Models\School::where('id',$this->schoolId)->firstOrFail();

        $I->haveRecord('customers', $this->controlCustomer);
        $I->haveRecord('customers', $this->beforeCustAttributes);

        $I->seeRecord('customers', $this->controlCustomer);
        $I->seeRecord('customers', $this->beforeCustAttributes);

        // run adjuster
        $adjuster = new \DataStaging\Adjusters\StudentAddressAdjuster($school);
        $adjuster->adjust();

        // ASSERT
        $I->seeRecord('customers', $this->controlCustomer);

        $I->seeRecord('customers', $this->finalCustAttributes);
        $I->dontSeeRecord('customers', $this->beforeCustAttributes);
    }

    // tests
    public function testGraceCollegeAdjuster(FunctionalTester $I)
    {
        $this->setupGraceCollege();
        $this->runAdjusterAndAssert($I);
    }

    public function testMaloneAdjuster(FunctionalTester $I)
    {
        $this->setupMalone();
        $this->runAdjusterAndAssert($I);
    }


    private function setupGraceCollege()
    {
        $this->schoolId           = 8;
        $this->where              = ['alias' => 'FREERNTL'];
        $this->finalAddress       = [
            'ship_addr' => '200 Seminary Drive',
            'ship_city' => 'Winona Lake',
            'ship_state' => 'IN',
            'ship_postal_code' => '46590',
            'ship_country' => 'US',
        ];

        $this->blankAddress = [
            'ship_addr_desc'=>'',
            'ship_addr'=>'',
            'ship_city'=>'',
            'ship_state'=>'',
            'ship_postal_code'=>'',
            'ship_country'=>'',
        ];

        $this->beforeCustAttributes = array_merge([
            'school_id' => $this->schoolId,
            'customer_acct'=>'999456789',
            'student_acct'=>'999456789',
            'firstname'=>'Lester',
            'lastname'=>'Tester',
            'email'=>'oneOffTester@junk.com',
            'b_delete'=>false,
            'enabled'=>true,
        ], $this->blankAddress, $this->where);

        $this->finalCustAttributes = array_merge([
            'school_id' => $this->schoolId,
            'customer_acct'=>'999456789',
            'student_acct'=>'999456789',
            'firstname'=>'Lester',
            'lastname'=>'Tester',
            'email'=>'oneOffTester@junk.com',
            'b_delete'=>false,
            'enabled'=>true,
        ], $this->finalAddress, $this->where);

        $this->controlCustomer = array_merge([
            'school_id' => $this->schoolId,
            'alias' => '',
            'customer_acct'=>'9999999',
            'student_acct'=>'9999999',
            'firstname'=>'',
            'lastname'=>'',
            'email'=>'control@junk.com',
            'b_delete'=>false,
            'enabled'=>true,
        ], $this->blankAddress);
    }

    private function setupMalone()
    {
        $this->schoolId           = 13;
        $this->finalAddress       = [
            'ship_addr' => '2600 Cleveland Ave NW',
            'ship_city' => 'Canton',
            'ship_state' => 'OH',
            'ship_postal_code' => '44709',
            'ship_country' => 'US',
        ];

        $this->blankAddress = [
            'ship_addr_desc'=>'',
            'ship_addr'=>'',
            'ship_city'=>'',
            'ship_state'=>'',
            'ship_postal_code'=>'',
            'ship_country'=>'',
        ];

        $this->beforeCustAttributes = array_merge([
            'comment' => 'opt in / 999456789',
            'school_id' => $this->schoolId,
            'customer_acct'=>'999456789',
            'student_acct'=>'999456789',
            'firstname'=>'Lester',
            'lastname'=>'Tester',
            'email'=>'oneOffTester@junk.com',
            'b_delete'=>false,
            'enabled'=>true,
        ], $this->blankAddress);

        $this->finalCustAttributes = array_merge([
            'comment' => 'opt in / 999456789',
            'school_id' => $this->schoolId,
            'customer_acct'=>'999456789',
            'student_acct'=>'999456789',
            'firstname'=>'Lester',
            'lastname'=>'Tester',
            'email'=>'oneOffTester@junk.com',
            'b_delete'=>false,
            'enabled'=>true,
        ], $this->finalAddress);

        $this->controlCustomer = array_merge([
            'comment' => 'Opted Out / 9999999',
            'school_id' => $this->schoolId,
            'alias' => '',
            'customer_acct'=>'9999999',
            'student_acct'=>'9999999',
            'firstname'=>'',
            'lastname'=>'',
            'email'=>'control@junk.com',
            'b_delete'=>false,
            'enabled'=>true,
        ], $this->blankAddress);
    }
}
