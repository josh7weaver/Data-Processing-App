<?php namespace DataStaging\Adjusters;

use DataStaging\Contracts\Adjuster;
use DataStaging\Models\Customer;
use DataStaging\Mapper;
use DataStaging\Models\School;
use DataStaging\Traits\TbbSchoolHelpers;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Log;

/**
 * This class is necessary to copy the customers' preference, entered from
 * 	TB butler to the customers.preference column TO the customers.comment column,
 * 	where sidewalk will pick up their preferences from the comment field.
 * MUST BE RUN BEFORE OTHER ADJUSTERS
 */
class CustomerPreferenceAdjuster implements Adjuster
{
    use TbbSchoolHelpers;

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
        Log::info("------------\n\nADJUSTING CUSTOMER PREFERENCES\n\n");

        if($this->isNotTbbSchool($this->school)){
            Log::info("School {$this->school->getName()} is not a TBB school, skipping adjust customer pref\n\n");
            return false;
        }

        // copy all existing preferences over to the comment field
        $this->copyTbbPreferenceToCommentField();

        // any comments still blank, fill with school default
        $this->fillBlankCommentFields();
    }

    public function copyTbbPreferenceToCommentField()
    {
        $customers = $this->getCustomersBySchoolId($this->school->getKey())
                        ->whereNotNull('preference')
                        ->get();

        // since there's a LOT of customers, chunk the results into 10,000
        $customers->chunk(10000)->each(function($customers)
        {
            foreach($customers as $customer)
            {
                $this->saveNewComment($customer, $customer->getPreference());
            }
        });

        // LOG RESULTS
        if ($customers->count() == 0) {
            Log::warning("Customer Preferences was NOT copied to comment field: No Customers with Preferences Exist!\n");
        } else {
            Log::info($customers->count() . " enabled customers with preferences were updated in this chunk.\n");
        }
    }

    public function fillBlankCommentFields()
    {
        try {
            $defaultPref = $this->school->tbbSettings()->firstorFail()->getDefaultPref();
        }
        catch(ModelNotFoundException $e){
            Log::error("Couldn't get default pref for the school: ". $e->getMessage(), $this->school);
            return false;
        }

        $customers = $this->getCustomersBySchoolId($this->school->getKey())
                          ->where('comment', '=', '')
                          ->get();

        foreach($customers as $customer)
        {
            $this->saveNewComment($customer, $defaultPref);

            Log::info("adjusting customer id = {$customer->id} and customer_acct = {$customer->customer_acct} ".
                "TO >> ".$this->buildCommentString($defaultPref, $customer->customer_acct)."\n");
        }

        if($customers->isEmpty()){
            Log::notice('No TBB customers found with blank comment field for school '.$this->school->getKey()."\n");
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
        $preference = $this->getCommentFormOfPreference($preference);
        return "$preference / $customerAcct";
    }

    private function getCommentFormOfPreference( $preference )
	{
		return Mapper::PREFERENCE_TO_COMMENT_MAP()[strtolower($preference)];
	}
}