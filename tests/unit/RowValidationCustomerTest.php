<?php

use DataStaging\Models\Course;
use DataStaging\Models\Customer;
use DataStaging\Models\Enrollment;
use Illuminate\Database\Eloquent;
use DataStaging\Models\Section;

class RowValidationCustomerTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected $isInitialized;

    protected $customerFile = ["2266237", "2266237", "", "", "Simone", "Bush", "DCNF", "sbush@anderson.edu", "", "", "Opted Out / 2266237", "", "", "", "Simone Bush", "5661 Loudon Drive", "Indianapolis", "IN", "46235", "USA", "", "", "", "0", "", "", "", "", "", "", "", "", "", "", "", "", "", "", 0];

    /**
     * @var Customer
     */
    protected $customer;
    protected $school;

    protected function _before()
    {
        if(!$this->isInitialized)
        {
            $this->school = \DataStaging\Models\School::firstOrFail();
            $this->customer = Customer::newInstanceFromFile($this->customerFile, $this->school);

            $this->isInitialized = true;
        }

        $this->setExpectedException('DataStaging\Exceptions\RowValidationException');
    }

    public function testTooLongPostalCode()
    {
        $customer = $this->customer;
        $customer->ship_postal_code = 'this is way too long to be a postal code are you crazyy????';
        $customer->validate();
    }

    /**
     * ADDRESS VALIDATION
     */
//    public function testInvalidAddressStreetThrowsException()
//    {
//        $this->testAddressPart('ship_addr');
//    }
//
//    public function testInvalidAddressCityThrowsException()
//    {
//        $this->testAddressPart('ship_city');
//    }
//
//    public function testInvalidAddressStateThrowsException()
//    {
//        $this->testAddressPart('ship_state');
//    }
//
//    public function testInvalidAddressPostalCodeThrowsException()
//    {
//        $this->testAddressPart('ship_postal_code');
//    }
//
//    private function testAddressPart($part)
//    {
//        $customer = $this->customer;
//        $customer->$part = '';
//        $customer->validate();
//    }
}