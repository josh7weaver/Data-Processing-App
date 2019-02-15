<?php


use DataStaging\Adjusters\CustomerCopyPrefToNotesFieldAdjuster;
use DataStaging\Models\Customer;
use DataStaging\Models\TbbSchool;

class CustomerCopyPrefToNotesFieldAdjusterTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    protected $tbbSchoolIds;

    protected function _before()
    {
        DB::beginTransaction();
    }

    protected function _after()
    {
        DB::rollBack();
    }

    protected function getTbbSchoolIds(){
        if(!$this->tbbSchoolIds){
            $this->tbbSchoolIds = TbbSchool::lists('school_id');
        }

        return $this->tbbSchoolIds;
    }

    // tests
    public function testAdjustNotes()
    {
        $this->tester->expect('The phrase "Opted In" to be prepended to whatever is currently in the notes field');

        $tbbSchoolId = $this->getTbbSchoolIds()[0];
        $school = \DataStaging\Models\School::findOrFail($tbbSchoolId);
        $customerId = $this->tester->haveRecord('customers', [
            'school_id' => $tbbSchoolId,
            'customer_acct'=>'12345678999999',
            'student_acct'=>'12345678999999',
            'email'=>'junktester@deleteme.com',
            'comment' => 'prefers rent',
            'notes' => 'junk',
            'firstname'=>'Lester',
            'lastname'=>'Tester',
            'alias' => 'fake',
            'ship_addr_desc'=>'',
            'ship_addr'=>'',
            'ship_city'=>'',
            'ship_state'=>'',
            'ship_postal_code'=>'',
            'ship_country'=>'',
            'enabled' => true,
            'b_delete'=>false,
        ]);

        $customer =  Customer::findOrFail($customerId);
        $expectedFinalNote = 'Opted In '.$customer->getNotes();

        $adjuster = new CustomerCopyPrefToNotesFieldAdjuster($school);
        $adjuster->adjustNotes();

        $customer = Customer::find($customerId);

        $this->assertEquals($expectedFinalNote, $customer->getNotes());
    }
}