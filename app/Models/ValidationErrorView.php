<?php namespace DataStaging\Models;

/**
 * Class ValidationErrorView - THIS IS A VIEW, so it is READ ONLY
 * @package DataStaging
 */
class ValidationErrorView extends ProcessLog
{
    public $table = 'validation_errors'; // THIS IS A VIEW
    protected $casts = [
        'context' => 'array'
    ];

    /**
     * Count the VALIDATION errors for ALL schools & given /token/type
     * @param        $processToken
     * @param string $validationType
     * @return mixed
     */
    public function countAllErrors($processToken, $validationType = '')
    {
        $query = $this->where('process_token', $processToken);

        if($validationType){
            $query->where('validation_type', $validationType);
        }
        return $query->count();
    }

    public function getSchoolsWithErrors($processToken, $validationType = '')
    {
        $query = $this->where('process_token', $processToken);

        if($validationType){
            $query->where('validation_type', $validationType);
        }

        $schoolCodes = $query->distinct()->lists('school_code');

        return School::whereIn('code', $schoolCodes)->get();
    }

    /**
     * This field is only present when a [count(*) as validation_count] query has been run.
     * @data ValidationErrorRepository::getCodesAndCounts()
     * @return mixed
     */
    public function getValidationCount()
    {
        return $this->validation_count;
    }

    /**
     * From validation_data table
     * @return mixed
     */
    public function getSummary()
    {
        return $this->validation_summary;
    }

    /**
     * From validation_data table
     * @return file | row
     */
    public function getType()
    {
        return $this->validation_type;
    }
}
