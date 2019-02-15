<?php namespace DataStaging;

use Carbon\Carbon;
use DataStaging\Contracts\ImportableAndExportableFile;
use DataStaging\Contracts\ImportableAndExportableModel;
use DataStaging\Contracts\DataIntegrityCheckerInterface;
use DataStaging\Exceptions\FileValidationException;
use DataStaging\Traits\ModelGetter;

class FileValidator
{
    // Validation Codes
    const ENCODING_CODE = 'ENCODING';
    const COLUMN_COUNT_CODE = 'F_COL_COUNT';
    const OLD_FILE_CODE = 'FILE_OLD';

    const VALIDATION_ID = 'file';

    /**
     * @var ImportableAndExportableFile
     */
    protected $file;

    /**
     * @var ImportableAndExportableModel
     */
    protected $model;


    use ModelGetter;


    /**
     * @param ImportableAndExportableFile  $file
     * @param ImportableAndExportableModel $model
     * @throws \ErrorException
     */
    public function validate(ImportableAndExportableFile $file, ImportableAndExportableModel $model)
    {
        $this->file = $file;
        $this->model = $model;

        // do checks
        $this->checkEncodingValid();
        $this->checkFileNotMoreThan24HoursOld($this->file->timeLastModified());

        // comment this out because we have files that don't have same number of columns as db
        // check is unnecessary because column count is validated at the row level.
        // $this->checkColumnCountEqualWithDb();
    }

    protected function checkEncodingValid()
    {
        if( !in_array($this->file->encoding(), $this->file->validEncodings()) )
        {
            throw new FileValidationException(
                "The file is encoded incorrectly. It should be encoded as UTF-8 or US-ASCII.",
                [
                    'validationCode' => self::ENCODING_CODE,
                    'context' => ['fileEncoding' => $this->file->encoding() . ' - Use `$file -I FILEPATH` to check in terminal'],
                ]


            );
        }
    }

//    protected function checkColumnCountEqualWithDb()
//    {
//        if( $this->model->getExpectedColumnCount() != $this->file->columnCount() )
//        {
//            throw new FileValidationException(
//                "The file column count (".$this->file->columnCount().") didn't match the DB column count (". $this->model->getExpectedColumnCount() .")\n",
//                [
//                    'validationCode' => self::COLUMN_COUNT_CODE,
//                ]
//            );
//        }
//    }

    protected function checkFileNotMoreThan24HoursOld(Carbon $timeLastModified)
    {
        $prettyTimestamp = $timeLastModified->toDateTimeString();
        $hoursSinceLastUpdate = $timeLastModified->diffInHours(Carbon::now());

        if($hoursSinceLastUpdate > 24)
        {
            throw new FileValidationException(
                "The file has not been updated for $hoursSinceLastUpdate hours. File timestamp is $prettyTimestamp\n",
                [
                    'validationCode' => self::OLD_FILE_CODE,
                    'context' => ['currentTime' => Carbon::now()],
                ]
            );
        }
    }
}