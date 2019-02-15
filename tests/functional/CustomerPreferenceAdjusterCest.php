<?php
use DataStaging\Adjusters\CustomerPreferenceAdjuster;
use DataStaging\Models\Customer;
use DataStaging\Models\School;

/**
 * Class CustomerPreferenceAdjusterCest
 *
 * The saveNewComment() method is unit tested
 */
class CustomerPreferenceAdjusterCest
{
    use \Codeception\Util\Shared\Asserts;

    protected $school;
    protected $controlSchool;
    protected $adjuster;

    public function _before(FunctionalTester $I)
    {
        $this->school = School::where('code','TU')->firstOrFail();
        $this->controlSchool = School::whereNotIn('code', ['TU'])->firstOrFail();
        $this->adjuster = new CustomerPreferenceAdjuster($this->school);
    }

    public function _after(FunctionalTester $I)
    {
    }

    // tests
    public function testCopyTbbPreferenceToCommentField(FunctionalTester $I)
    {
        $controlId =  $I->haveRecord('customers', [
            'school_id'=>$this->controlSchool->getKey(),
            'customer_acct'=>'100000110',
            'student_acct' => '',
            'lastname'=>'',
            'email'=>'',
            'comment'=>"Opted Out / 100000110",
            'preference'=>'opt out',
            'firstname'=>'control customer',
            'b_delete'=> false,
            'enabled' => true
        ]);

        $customerWithPrefId = $I->haveRecord('customers', [
            'school_id'=>$this->school->getKey(),
            'customer_acct'=>'100000111',
            'student_acct' => '',
            'lastname'=>'',
            'email'=>'',
            'preference'=>'rent',
            'firstname'=>'cust with preference',
            'b_delete'=> false,
            'enabled' => true
        ]);

        $customerWithNullPrefId = $I->haveRecord('customers', [
            'school_id'=>$this->school->getKey(),
            'customer_acct'=>'100000112',
            'student_acct' => '',
            'lastname'=>'',
            'email'=>'',
            'firstname'=>'cust with NULL preference',
            'b_delete'=> false,
            'enabled' => true
        ]);

        $this->adjuster->copyTbbPreferenceToCommentField();

        // ASSERTIONS
        $I->expect("A customer from this school with a preference will have a comment that matches the preference.");
        $this->assertEquals("Prefers Rental / 100000111", Customer::findOrFail($customerWithPrefId)->getComment());

        $I->expect("A customer from this school with a null preference will have a blank comment");
        $this->assertEquals('', Customer::findOrFail($customerWithNullPrefId)->getComment());

        $I->expect("Any customers not from this school will remain unchanged.");
        $this->assertEquals("Opted Out / 100000110", Customer::findOrFail($controlId)->getComment());
    }

    public function testFillBlankCommentFields(FunctionalTester $I)
    {
        $tbbSchool = $this->school->tbbSettings()->firstOrFail();
        $tbbSchool->setAttribute('default_pref', 'rent');
        $tbbSchool->save();

        $controlId =  $I->haveRecord('customers', [
            'school_id'=>$this->controlSchool->getKey(),
            'customer_acct'=>'100000110',
            'comment'=>"",
            'preference'=>'opt out',
            'firstname'=>'control customer with blank comment',
            'student_acct' => '',
            'lastname'=>'',
            'email'=>'',
            'b_delete'=> false,
            'enabled' => true
        ]);

        $customerWithBlankCommentId = $I->haveRecord('customers', [
            'school_id'=>$this->school->getKey(),
            'customer_acct'=>'100000111',
            'firstname'=>'cust with a blank comment',
            'comment'=>'',
            'student_acct' => '',
            'lastname'=>'',
            'email'=>'',
            'firstname'=>'cust with NULL preference',
            'b_delete'=> false,
            'enabled' => true
        ]);

        $customerWithFilledCommentId = $I->haveRecord('customers', [
            'school_id'=>$this->school->getKey(),
            'customer_acct'=>'100000112',
            'firstname'=>'cust with stuff in comment already',
            'comment'=>'Opted Out / 100000112',
            'student_acct' => '',
            'lastname'=>'',
            'email'=>'',
            'firstname'=>'cust with NULL preference',
            'b_delete'=> false,
            'enabled' => true
        ]);

        $this->adjuster->fillBlankCommentFields();

        //ASSERTIONS
        $I->expect("A customer from this school with a blank comment field should receive the default preference for the school");
        $this->assertEquals('Prefers Rental / 100000111', Customer::findOrFail($customerWithBlankCommentId)->getComment());

        $I->expect("A customer from this school whom already has a comment will not be modified");
        $this->assertEquals('Opted Out / 100000112', Customer::findOrFail($customerWithFilledCommentId)->getComment());

        $I->expect("Any customers not from this school will remain unchanged.");
        $this->assertEquals('', Customer::findOrFail($controlId)->getComment());
    }
}
