<?php namespace DataStaging;

use Illuminate\Support\Collection;

class SchoolErrorDto
{
    /**
     * @var string
     */
    protected $schoolName;

    /**
     * @var Collection
     */
    protected $validationErrors;

    /**
     * @var Collection
     */
    protected $generalErrors;

    /**
     * SchoolErrorDto constructor.
     */
    public function __construct()
    {
        $this->validationErrors = collect();
    }

    /**
     * @return Collection
     */
    public function getGeneralErrors()
    {
        return $this->generalErrors;
    }

    /**
     * @return string
     */
    public function getSchoolName()
    {
        return $this->schoolName;
    }

    /**
     * @return Collection
     */
    public function getValidationErrors()
    {
        return $this->validationErrors;
    }

    /**
     * @param mixed $generalErrors
     * @return SchoolErrorDto
     */
    public function setGeneralErrors($generalErrors)
    {
        $this->generalErrors = $generalErrors;
        return $this;
    }

    /**
     * @param mixed $schoolName
     * @return SchoolErrorDto
     */
    public function setSchoolName($schoolName)
    {
        $this->schoolName = $schoolName;
        return $this;
    }

    /**
     * @param            $fileType
     * @param Collection $validationError
     * @return SchoolErrorDto
     */
    public function addValidationError($fileType, Collection $validationError)
    {
        if($validationError->isEmpty()){
            return $this;
        }

        $this->validationErrors->put($fileType, $validationError);
        return $this;
    }

    public function newInstance()
    {
        return new static;
    }

    public function hasErrors()
    {
        return !$this->generalErrors->isEmpty() || !$this->validationErrors->isEmpty();
    }
}