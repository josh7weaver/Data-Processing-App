<?php

use DataStaging\Adjusters\CustomerPreferenceAdjuster;
use DataStaging\Models\Customer;
use DataStaging\Models\School;
use DataStaging\Models\TbbSchool;

class CustomerPreferenceAdjusterTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
        DB::beginTransaction();
    }

    protected function _after()
    {
        DB::rollBack();
    }

    /**
     * @dataProvider provideTestSaveNewComment
     * 
     * @param $preference
     * @param $commentFormOfPreference
     */
    public function testSaveNewComment($preference, $commentFormOfPreference)
    {
        // get a TBB school & customer
        $tbbSchool = TbbSchool::where('slug', 'taylor')->firstOrFail();
        $customer = $tbbSchool->customers()->firstOrFail();
        $customerAcctNumber = $customer->getCustomerAcct();
        $expectedComment = "$commentFormOfPreference / $customerAcctNumber";

        $adjuster = new CustomerPreferenceAdjuster($tbbSchool->schoolSettings);
        $adjuster->saveNewComment($customer, $preference);

        $customer = Customer::find($customer->getKey());

        $this->assertEquals($expectedComment, $customer->getComment());
    }

    public function provideTestSaveNewComment()
    {
        return [
            ['rent', 'Prefers Rental'],
            ['buy new', 'Prefers New'],
            ['buy used', 'Prefers Used'],
            ['opt out', 'Opted Out']
        ];
    }

}