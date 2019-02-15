<?php namespace DataStaging;

use DataStaging\Contracts\ImportableAndExportableModel;
use DataStaging\Exceptions\RowValidationException;
use DataStaging\Models\Course;
use DataStaging\Models\Customer;
use DataStaging\Models\Division;
use DataStaging\Models\IoBaseModel;
use DataStaging\Models\Section;
use DataStaging\Traits\ModelGetter;
use Illuminate\Support\Collection;
use Log;

class RowValidator
{
    // Validation Code
    const BAD_DIVISION_NAME_CODE = 'DIV_NAME';
    const BAD_COLUMN_COUNT_CODE = 'R_COL_COUNT';
    const BAD_DATES_CODE = 'BAD_DATES';
    const NO_DATES_CODE = 'NO_DATES';
    const BAD_ADDRESS_CODE = 'ADDRESS';
    const NO_COURSE = 'NO_COURSE';
    const NO_INSTRUCTOR = 'NO_INSTRUCTOR';
    const NO_CUSTOMER = 'NO_CUSTOMER';
    const NO_SECTION = 'NO_SECTION';
    const DUP_CUSTOMER = 'DUP_CUSTOMER';
    const DUP_SECTION = 'DUP_SECTION';
    const DUP_COURSE = 'DUP_COURSE';
    const DUP_ENROLLMENT = 'DUP_ENROLLMENT';
    const BAD_POSTAL_CODE = 'BAD_POSTAL_CODE';

    const VALIDATION_ID = 'row';

    use ModelGetter;

    /**
     * @var ImportableAndExportableModel
     */
    protected $model;

    /**
     * @var School
     */
    protected $school;

    /**
     * @var Division
     */
    private $division;

    /**
     * @var Course
     */
    private $course;

    /**
     * @var Section
     */
    private $section;

    /**
     * @var Customer
     */
    private $customer;

    /**
     * Resolve this singleton out of the container. Registered in providers/AppServiceProvider
     * @var \Illuminate\Support\Collection
     */
    protected $store;

    /**
     * RowValidator constructor.
     * @param ImportableAndExportableModel $model
     * @param Division                     $division - resolve from container
     * @param Course                       $course
     * @param Section                      $section
     * @param Customer                     $customer
     */
    public function __construct(
        ImportableAndExportableModel $model,
        Division $division,
        Course $course,
        Section $section,
        Customer $customer
    ){
        $this->model = $model;
        $this->division = $division;
//        $this->course = $course;
//        $this->section = $section;
//        $this->customer = $customer;
//        $this->school = $this->model->school()->first();
//        $this->store = app('store.models'); // see note above.
    }

    public function checkPostalCodeIsCorrectLength()
    {
        $postalCode = $this->model->getShipPostalCode();
        if(strlen($postalCode) > 16){
            throw new RowValidationException(
                "The Postal Code field must be less than or equal to 16 chars. $postalCode given.\n",
                [
                    'currentRow' => $this->model->getOriginRow(),
                    'validationCode' => self::BAD_POSTAL_CODE,
                ]
            );
        }
    }

    /**
     * Used to check that the division name is a valid division name
     *  Why? The campus/division name is used in the sectionEnrollmentAdjuster to relate enrollment to a section
     * @throws RowValidationException
     */
    public function checkCampusFieldIsValidDivisionName()
    {
        $divisionName = $this->model->getDivision(); // division name, or "campus" is first column in section file
        $validDivisionNames = $this->division->validDivisionNames();

        if($this->isDivisionNameInvalid($divisionName, $validDivisionNames))
        {
            throw new RowValidationException(
                "The Campus field must be a valid Division Name. $divisionName given.\n",
                [
                    'currentRow' => $this->model->getOriginRow(),
                    'validationCode' => self::BAD_DIVISION_NAME_CODE,
                ]
            );
        }
    }

    protected function isDivisionNameInvalid($divisionName, array $validDivisionNames)
    {
        return !in_array(trim($divisionName), $validDivisionNames);
    }

    /**
     * Used for all models. Row must have correct number of columns.
     * @param array $row
     * @param       $expectedColumnCount
     * @throws RowValidationException
     */
    public static function checkColumnCountMatchesExpected(array $row, $expectedColumnCount)
    {
        $actualColumnCount = count($row);

        if ( $actualColumnCount != $expectedColumnCount){
            throw new RowValidationException(
                "Column Count Mismatch (within csv): Expecting $expectedColumnCount columns, got $actualColumnCount\n",
                [
                    'currentRow' => $row,
                    'validationCode' => self::BAD_COLUMN_COUNT_CODE,
                ]
            );
        }
    }

    /**
     * Used for course & section. If end date PRECEEDS start date, error out
     * @return bool
     * @throws RowValidationException
     */
    public function checkEndDateIsAfterStartDate()
    {
        if($this->model->shouldBeDisabled()) return true; // let it pass through (i.e. skip this check) if b_delete is true

        try{
            $endDate = $this->model->getEndDate()->toDateString();
            $startDate = $this->model->getStartDate()->toDateString();
        }
        catch(\RuntimeException $e){
            // if the comment field doesn't contain dates we can find. For now just log it.
            throw new RowValidationException(
                "Invalid start/end dates: ". $e->getMessage() ."\n",
                [
                    'currentRow' => $this->model->getOriginRow(),
                    'validationCode' => self::NO_DATES_CODE,
                ]
            );
        }

        if($endDate < $startDate){
            throw new RowValidationException(
                "Invalid start/end dates: The End date is before the start date. Start Date = $startDate, End date = $endDate\n",
                [
                    'currentRow' => $this->model->getOriginRow(),
                    'validationCode' => self::BAD_DATES_CODE,
                ]
            );
        }
    }

//    public function checkCourseExists()
//    {
//        $courseExists = $this
//            ->getStoreFor($this->course)
//            ->contains(function($key, Course $course) {
//                return $course->courseKey()
//                         == $this->model->courseKey();
//            });
//
//        if (!$courseExists){
//            throw new RowValidationException(
//                "The corresponding course for this row does not exist in the courses file.\n",
//                [
//                    'currentRow' => $this->model->getOriginRow(),
//                    'validationCode' => self::NO_COURSE,
//                ]
//            );
//        }
//    }
//
//    public function checkInstructorExists()
//    {
//        $instructorExists = $this
//            ->getStoreFor($this->customer)
//            ->contains(function($key, Customer $customer) {
//                return $customer->getInstructor() == $this->model->getInstructor();
//            });
//
//        if (!$instructorExists){
//            // throw new RowValidationException(  // do this instead of log if you want to SKIP the row
//            Log::error("The corresponding instructor for this row does not exist in the customers file. Importing anyway.\n",
//                [
//                    'currentRow' => $this->model->getOriginRow(),
//                    'validationCode' => self::NO_INSTRUCTOR,
//                ]
//            );
//        }
//    }
//
//    public function checkCustomerExists()
//    {
//        $customerExists = $this
//            ->getStoreFor($this->customer)
//            ->contains(function($key, Customer $customer) {
//                return $customer->customerKey() == $this->model->customerKey();
//            });
//
//        if (!$customerExists){
//            // throw new RowValidationException(  // do this instead of log if you want to SKIP the row
//            Log::error("The corresponding customer for this row does not exist in the customers file. Importing anyway.\n",
//                [
//                    'currentRow' => $this->model->getOriginRow(),
//                    'validationCode' => self::NO_CUSTOMER,
//                ]
//            );
//        }
//    }
//
//    public function checkSectionExists()
//    {
//        $sectionExists = $this
//            ->getStoreFor($this->section)
//            ->contains(function($key, Section $section) {
//                return $section->sectionKey() == $this->model->sectionKey();
//            });
//
//        if (!$sectionExists){
//            throw new RowValidationException(
//                "The corresponding section for this row does not exist in the section file.\n",
//                [
//                    'currentRow' => $this->model->getOriginRow(),
//                    'validationCode' => self::NO_SECTION,
//                ]
//            );
//        }

//    }

//    /**
//     * @param ImportableAndExportableModel $model
//     * @return Collection
//     */
//    private function getStoreFor(ImportableAndExportableModel $model)
//    {
//        $typeId = $model->getBaseName();
//
//        if(!$this->store->has($typeId)){
//            $this->store->put($typeId, $this->getAllRows($model));
//        }
//
//        return $this->store->get($typeId);
//    }
//
//    private function getAllRows(ImportableAndExportableModel $model)
//    {
//        return $model->enabled()
//                ->where('school_id', $this->school->getKey())
//                ->get();
//    }


//    /**
//     * If any of the address fields are empty don't include the row.
//     * This is because it causes problems when trying to ship orders out to customers with invalid addresses.
//     * @throws RowValidationException
//     */
//    public function checkAddressIsValid()
//    {
//        $address = $this->model->getShipAddress();
//        $city = $this->model->getShipCity();
//        $state = $this->model->getShipState();
//        $zip = $this->model->getShipPostalCode();
//        if($this->isAnyAddressFieldBlank([$address, $city, $state, $zip])){
//            throw new RowValidationException(
//                "Invalid Address: This Row was NOT saved. Address given was... Address: $address; City: $city; State: $state; Zip code: $zip;\n",
//                [
//                    'currentRow' => $this->model->getOriginRow(),
//                    'validationCode' => self::BAD_ADDRESS_CODE,
//                ]
//            );
//        }
//
//    }
//
//    protected function isAnyAddressFieldBlank(array $addressFields)
//    {
//        foreach($addressFields as $addressField)
//        {
//            if(is_null($addressField) || trim($addressField) == '') return true;
//        }
//
//    }
}