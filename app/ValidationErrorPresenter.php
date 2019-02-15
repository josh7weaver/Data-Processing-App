<?php namespace DataStaging;

use DataStaging\Models\ValidationData;
use Illuminate\Support\Collection;

class ValidationErrorPresenter
{
    /**
     * @var \Illuminate\Database\Eloquent\Model|static
     */
    protected $validationData;

    /**
     * @var string, corresponds to validation_data.code column
     */
    private $errorCode;

    /**
     * @var Collection
     */
    private $errorItems;

    /**
     * @var string
     */
    private $fileType;

    /**
     * ValidationErrorPresenter constructor.
     * @param string $errorCode
     * @param array  $errorItems - where each item is a ProcessLog Object
     * @param        $fileType
     */
    public function __construct($errorCode, array $errorItems, $fileType)
    {
        $this->errorCode = $errorCode;
        $this->errorItems = collect($errorItems);
        $this->validationData = (new ValidationData)->findByCode($this->errorCode);
        $this->fileType = $fileType;
    }

    /**
     * Get the Summary message for a given validation error code and process_log entries
     * @return string
     */
    public function getSummary()
    {
        if($this->validationData->getType() == FileValidator::VALIDATION_ID){
            return $this->errorItems->count() . " file(s) invalid: " . $this->validationData->getSummary();
        }

        if($this->validationData->getType() == RowValidator::VALIDATION_ID){
            return $this->errorItems->count() . " row(s) invalid: " . $this->validationData->getSummary();
        }
    }

    /**
     * Get the detail message for a given validation error code and process_log entries
     *
     * Note that there are not break; commands in the switch bc in every case we are returning
     * something, which truncates the switch process.
     *
     * @return Collection
     */
    public function getDetails()
    {
        switch($this->errorCode){
            case RowValidator::BAD_COLUMN_COUNT_CODE:
                // this works for customer & enrollment File only, others don't have cust ID
                if(in_array($this->fileType, ['Customer', 'Enrollment']))
                {
                    return $this->errorItems->map(function($item) {
                        return "Customer ID " . $item->getRow()[0] . ' - ' . $item->getMessage();
                    });
                }

                return $this->errorItems->map(function($item)
                {
                    return json_encode($item->getRow()); // just return the whole row
                });

//            case RowValidator::DUP_CUSTOMER:
//                $custIds = $this->errorItems->map(function($item){
//                    return $item->getRow()[0];
//                });
//                return collect("Customer ID's: " . $custIds->toJson());

//            case RowValidator::NO_INSTRUCTOR:
//                $instructors = $this->errorItems->map(function($item) {
//                    return $item->getRow()[5];
//                });
//                return collect("Instructors: " . $instructors->toJson());

            case RowValidator::BAD_DIVISION_NAME_CODE:
                $divisionNames = $this->errorItems->map(function($item){
                    return $item->getRow()[0]; // this works for SECTION only
                });
                return collect('Divisions: ' . $divisionNames->toJson());

            case FileValidator::COLUMN_COUNT_CODE:
            case FileValidator::ENCODING_CODE:
            case FileValidator::OLD_FILE_CODE:
                return $this->errorItems->map(function($item){
                    return $item->getMessage() . " " . $item->getFilePath();
                });

            default:
                return $this->errorItems->map(function($item)
                {
                    return json_encode($item->getRow()); // just return the whole row
                });
        }
    }
}