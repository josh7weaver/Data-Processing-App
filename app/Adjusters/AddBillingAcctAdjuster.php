<?php namespace DataStaging\Adjusters;

use DataStaging\Contracts\Adjuster;
use DataStaging\Models\Customer;
use DataStaging\Models\School;
use Log;

/**
 * This is necesary to fill in the comment field where it is blank with the customer preference (blank)
 *  and the customer billing id. In the cases we generate it, it will just be the customer_acct number, 
 *  which is student_id_num in Juniper db.
 */
class AddBillingAcctAdjuster implements Adjuster
{
    /**
     * @var Customer
     */
    protected $customer;

    /**
     * @var School
     */
    private $school;

    public function __construct(School $school)
	{
        $this->school = $school;
        $this->customer = new Customer;
    }

    public function adjust()
    {
        Log::info("------------\n\nADDING BILLING ID\n\n");

        // any comments that are blank get filled with "/ billing_id"
        // any comments that do not contain a slash will be filled with "/ BILLING_ID"
        //
        // This guards against schools who send us shit data that doesn't conform to the standards agreed upon.
        $this->appendBillingAccountToNonSlashComments();
    }

    public function appendBillingAccountToNonSlashComments()
    {
        $customers = $this->getCustomersBySchoolId($this->school->getKey())
                          ->where('comment', 'not like', '%/%')
                          ->get();

        foreach($customers as $customer)
        {
            $this->saveNewComment($customer, $customer->comment);

            Log::info("adjusting customer id = {$customer->id} and customer_acct = {$customer->customer_acct} ".
                "TO >> ".$this->buildCommentString($customer->comment, $customer->customer_acct)."\n");
        }

        if($customers->isEmpty()){
            Log::notice('No customers found having comment without a slash for school '.$this->school->getKey()."\n");
        }
    }

    public function saveNewComment($customer, $preference)
    {
        $newComment = $this->buildCommentString($preference, $customer->customer_acct);

        $customer->comment = $newComment;
        return $customer->save();
    }

    /**
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function getCustomersBySchoolId($id)
    {
        return $this->customer
                    ->newQuery()
                    ->enabled()
                    ->where('school_id', $id);
    }

    private function buildCommentString($preference, $customerAcct)
    {
        return "$preference / $customerAcct";
    }
}