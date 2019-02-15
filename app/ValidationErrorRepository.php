<?php namespace DataStaging;

use DataStaging\Models\Course;
use DataStaging\Models\Customer;
use DataStaging\Models\Enrollment;
use DataStaging\Models\School;
use DataStaging\Models\Section;
use DataStaging\Models\ValidationErrorView;
use DB;

class ValidationErrorRepository
{
    /**
     * @var ValidationErrorView
     */
    protected $validationErrorView;

    /**
     * @var int
     */
    protected $processToken;

    /**
     * @var School
     */
    protected $school;

    /**
     * ValidationErrorRepository constructor.
     * @param        $processToken
     * @param School $school
     */
    public function __construct($processToken, School $school)
    {
        $this->validationErrorView = new ValidationErrorView;
        $this->processToken        = $processToken;
        $this->school              = $school;
    }

    /**
     * Count the VALIDATION errors for given process code/school/type
     * @param null $validationType
     * @return mixed
     */
    public function countErrors($validationType = null)
    {
        $query = $this->school
            ->validationErrors()
            ->where('process_token', $this->processToken);

        if($validationType){
            $query->where('validation_type', $validationType);
        }

        return $query->count();
    }

    public function getCodesAndCounts($validationType = null, $fileType = null)
    {
        $query = $this->validationErrorView
            ->where('process_token', $this->processToken)
            ->where('school_code', $this->school->getCode());

        if($validationType){
            $query->where('validation_type', $validationType);
        }

        if($fileType){
            $query->where('file_type', $fileType);
        }

        return $query->select([
                        'validation_code',
                        'validation_summary',
                        'file_type',
                        DB::raw('count(*) as validation_count') // has getter on model
                    ])
                    ->groupBy(['validation_code', 'file_type', 'validation_summary'])->get();
    }

    public function getAllErrors($validationType = null)
    {
        return collect()
            ->merge($this->getCustomerErrors($validationType))
            ->merge($this->getCourseErrors($validationType))
            ->merge($this->getSectionErrors($validationType))
            ->merge($this->getEnrollmentErrors($validationType));
    }

    public function getCustomerErrors($validationType = null, $validationCode = null)
    {
        return $this->getSchoolErrors(Customer::getBaseName(), $validationType, $validationCode);
    }

    public function getCourseErrors($validationType = null, $validationCode = null)
    {
        return $this->getSchoolErrors(Course::getBaseName(), $validationType, $validationCode);
    }

    public function getSectionErrors($validationType = null, $validationCode = null)
    {
        return $this->getSchoolErrors(Section::getBaseName(), $validationType, $validationCode);
    }

    public function getEnrollmentErrors($validationType = null, $validationCode = null)
    {
        return $this->getSchoolErrors(Enrollment::getBaseName(), $validationType, $validationCode);
    }

    public function getSchoolErrors($fileType = null, $validationType = null, $validationCode = null)
    {
        $query = $this->school
            ->validationErrors()
            ->where('process_token', $this->processToken);

        if($fileType){
            $query->where('file_type', $fileType);
        }

        if($validationType){
            $query->where('validation_type', $validationType);
        }

        if($validationCode){
            $query->where('validation_code', $validationCode);
        }

        return $query->get();
    }
}