<?php namespace DataStaging\Adjusters;

use DataStaging\Contracts\Adjuster;
use DataStaging\Models\School;
use DataStaging\Models\Customer;
use Log;

/**
 * This Class updates the address for every customer (student) attending
 * 	the given school id, meeting the conditions. Currently all args are required.
 */
class StudentAddressAdjuster implements Adjuster
{
	private $schoolId;
    protected $school;

    /**
     * StudentAddressAdjuster constructor.
     * @param School $school
     */
    public function __construct(School $school)
    {
        $this->school = $school;
        $this->schoolId = $school->getKey();
    }

    public function adjust()
    {
        $numberUpdated = null;

        // proprietary code removed here

        if(is_null($numberUpdated)) return;

        Log::info("------------\n\nADJUSTING ADDRESSES\n\n");
        Log::info("Updated $numberUpdated addresses for ".$this->school->getName()."\n");
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function enabledCustomersFromSchoolQuery()
    {
        return Customer::enabled()->where('school_id', $this->schoolId);
    }
}