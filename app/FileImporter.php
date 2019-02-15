<?php namespace DataStaging;

use DataStaging\Contracts\ImportableAndExportableFile;
use DataStaging\Contracts\ImportableAndExportableModel;
use DataStaging\Exceptions\FileValidationException;
use DataStaging\Exceptions\RowValidationException;
use DataStaging\Models\School;
use DB;
use Log;

class FileImporter
{
    /**
     * @var FileValidator
     */
    protected $fileDataIntegrity;

    /**
     * @var ImportableAndExportableFile
     */
    protected $file;

    /**
     * @var ImportableAndExportableModel
     */
    protected $model;

    /**
     * @var School
     */
    protected $school;

    protected $tempTable;

    /**
     * Each file that is imported corresponds to a given model, and belongs to a specific school
     * @param ImportableAndExportableModel $model - a blank object implementation of the interface
     * @param School                       $school - an object representing a specific school
     * @param FileValidator                $fileIntegrity
     */
    public function __construct(ImportableAndExportableModel $model,
                                School $school,
                                FileValidator $fileIntegrity)
    {
        $this->model = $model;
        $this->school = $school;
        $this->file = Mapper::FILE_TYPE_TO_MODEL_MAP()[$this->school->getFileType()]; // csv or other
        $this->fileDataIntegrity = $fileIntegrity;
        $this->tempTable = 'id_list';
    }

    public function importAll(array $filePaths)
    {
        $this->createTempTable();

        foreach ($filePaths as $filepath)
        {
            $csv = new $this->file($filepath, 'r', ['model' => $this->model, 'school' => $this->school]);
            $this->import($csv);
        }

        $numberDisabled = $this->disableRowsNotInFileForSchool();
        Log::info("$numberDisabled were disabled\n");
    }

    /**
     * Validate and persist each row in the file. This does NOT update the enabled status.
     * You must call $this->updateEnabledStatus() manually to do that.
     * @param ImportableAndExportableFile $file
     * @return array|bool
     *
     */
    public function import(ImportableAndExportableFile $file)
    {
        try{
            $this->fileDataIntegrity->validate($file, $this->model);
            
            $validRows = collect($file->map(function($currentRow) use($file)
            {
                try{
                    // setup, validate, persist the model instance
                    $model = $this->model;
                    $rowDto = $model::newInstanceFromFile($currentRow, $this->school);
                    $rowDto->validate();
                    $affectedId = $rowDto->persist();

                    $this->saveToTempTable($affectedId);

                    return $rowDto;
                }
                catch(RowValidationException $e){
                    Log::error($e->getMessage(), [
                        'path' => $file->path(),
                        'currentRow' => $e->getCurrentRow(),
                        'fileType' => $this->model->getBaseName(),
                        'validationCode' => $e->getValidationCode(),
                    ]);
                }
            }));

        }
        catch(FileValidationException $e){
            Log::error($e->getMessage(),
                array_merge([
                    'path'=>$file->path(),
                    'fileType'=> $this->model->getBaseName(),
                    'validationCode' => $e->getValidationCode(),
                ],
                    $e->getContext()
                ));
            return false;
        }
        catch(\Exception $e){
            Log::error("General Error: ". $e->getMessage(),
                [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]
            );
            return false;
        }

        Log::info("Import SUCCEEDED for ".$validRows->count()." rows in ".$file->path()."\n\n");

        return $validRows;
    }

    protected function disableRowsNotInFileForSchool()
    {
        // if nothing was inserted or updated, don't run the disable query. This addresses the case where all the files
        // of one type (say customer files) fail validation. In that case we want the results of the previous successful
        // run to stay enabled in the database, rather than having this query disable ALL customers for that school,
        // since it would essentially run a query saying `update TABLE set enabled = false where ... id not in (select id from id_list)` where the subquery is an empty set.
        // ya, that would not be good.
        if(!DB::table($this->tempTable)->exists()){
            return 0;
        }

        return $this->model->newQuery()
                    ->where('school_id', $this->school->getKey())
                    ->whereNotIn('id', function($query)
                    {
                       $query->select(DB::raw("id FROM {$this->tempTable}"));
                    })
                    ->where('enabled', true)
                    ->update(['enabled'=>false]);
    }

    protected function createTempTable()
    {
        DB::statement("DROP TABLE IF EXISTS {$this->tempTable}");
        DB::statement("
            CREATE TEMPORARY TABLE IF NOT EXISTS {$this->tempTable} (
                id INTEGER NOT NULL
            )
        ");
    }


    protected function saveToTempTable($itemId)
    {
        return DB::table($this->tempTable)->insert(['id'=>$itemId]);
    }

//    /**
//     * Uses Temporary table to hold all the successfully imported IDs
//     *  1. DISABLE all items NOT in the file (for current school)
//     *  2. ENABLE only items WERE successfully imported from file (for current school)
//     * @return mixed
//     */
//    public function updateEnabledStatus()
//    {
//        DB::beginTransaction();
//
//        try{
//            $numberDisabled = $this->disableRowsNotInFileForSchool();
//            $numberEnabled = $this->enableRowsInFileForSchool();
//
//            DB::commit();
//            Log::info("$numberEnabled rows were enabled, $numberDisabled were disabled\n");
//        }
//        catch(\Exception $e){
//            DB::rollBack();
//            Log::error("rows were NOT enabled properly. Error = ".$e->getMessage()."\n");
//        }
//    }

//    protected function enableRowsInFileForSchool()
//    {
//        return $this->model->newQuery()
//                           ->where([
//                               'school_id' => $this->school->getKey(),
//                               'enabled' => false
//                           ])
//                           ->whereIn('id', function($query)
//                           {
//                               $query->select(DB::raw("id FROM {$this->tempTable}"));
//                           })
//                           ->update(['enabled'=>true]);
//    }

}