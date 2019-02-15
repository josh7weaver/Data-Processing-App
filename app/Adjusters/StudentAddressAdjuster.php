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

        switch($this->schoolId){
//            case 9: //greenville
//                $numberUpdated = $this->enabledCustomersFromSchoolQuery()
//                     ->where('alias', 'GVCTRAD')
//                     ->update([
//                        'ship_addr' => '500 Ganton Circle',
//                        'ship_city' => 'Greenville',
//                        'ship_state' => 'IL',
//                        'ship_postal_code' => '62246'
//                    ]);
//                break;

//            case 4: //Bluffton
//                $numberUpdated = $this->enabledCustomersFromSchoolQuery()
//                    ->update([
//                        'ship_addr' => '1 University Drive',
//                        'ship_city' => 'Bluffton',
//                        'ship_state' => 'OH',
//                        'ship_postal_code' => '45817',
//                        'ship_country' => 'US',
//                    ]);
//                break;

//            case 8: //Grace College
//                $numberUpdated = $this->enabledCustomersFromSchoolQuery()
//                    ->where('alias', 'FREERNTL')
//                   ->update([
//                        'ship_addr' => '200 Seminary Drive',
//                        'ship_city' => 'Winona Lake',
//                        'ship_state' => 'IN',
//                        'ship_postal_code' => '46590',
//                        'ship_country' => 'US',
//                    ]);
//                break;

//            case 13: // Malone
//                $numberUpdated = $this->enabledCustomersFromSchoolQuery()
//                   ->whereRaw("comment NOT ILIKE '%out%'")
//                    ->update([
//                        'ship_addr' => '2600 Cleveland Ave NW',
//                       'ship_city' => 'Canton',
//                        'ship_state' => 'OH',
//                        'ship_postal_code' => '44709',
//                        'ship_country' => 'US',
//                    ]);
//                break;
        }

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