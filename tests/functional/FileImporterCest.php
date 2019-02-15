<?php

use DataStaging\Contracts\ImportableAndExportableFile;
use DataStaging\Contracts\ImportableAndExportableModel;

class FileImporterCest
{
    /**
     * @var \DataStaging\Models\School
     */
    protected $school;

    /**
     * @var \DataStaging\Models\Course
     */
    protected $model;

    /**
     * @var \Illuminate\Support\Collection
     */
    protected $itemsFromFile;

    public function _before(FunctionalTester $I)
    {
        $this->model = new \DataStaging\Models\Course();
        $this->school = \DataStaging\Models\School::where('code', 'MU')->firstOrFail();
        $this->itemsFromFile = new \Illuminate\Support\Collection();
    }

    public function _after(FunctionalTester $I)
    {
    }

    public function testImportAll(FunctionalTester $I)
    {
        $I->expect('given a set of file paths, all the records from that file '.
                    'should be saved to the database and enabled. Any rows not '.
                    'in the file should be DISabled in the DB');

        $files = [
            codecept_data_dir('malone/data_files/mu_course.csv'),
            codecept_data_dir('malone/data_files/mu_course_alt.csv')
        ];

        $fileDataIntegrity = \Codeception\Util\Stub::makeEmpty('DataStaging\FileValidator');
        $fileImporter = new \DataStaging\FileImporter($this->model, $this->school, $fileDataIntegrity);
        $fileImporter->importAll($files);

        foreach($files as $filePath) {
            $file = new \DataStaging\CsvFile($filePath);

            $this->testFileDataIsPersistedToDBAndEnabled($I, $file, $this->model);
        }

        $this->testRowsNotFromFilesAreDisabled($I, $file, $this->model);
    }

    private function testFileDataIsPersistedToDBAndEnabled(FunctionalTester $I,
                                                 ImportableAndExportableFile $file,
                                                 ImportableAndExportableModel $model)
    {
        $I->expectTo("see the records from ".$file->name()." for school_id ".$this->school->getKey()." in the DB");

        $file->map(function($row) use($I, $model)
        {
            $instance = $model::newInstanceFromFile($row, $this->school);

            $I->seeRecord($model->getTable(), $instance->getAttributes());

            $record = $I->grabRecord($model->getTable(), $instance->getAttributes());
            $this->itemsFromFile->push($record);
        });

    }

    private function testRowsNotFromFilesAreDisabled(FunctionalTester $I,
                                                    ImportableAndExportableFile $file,
                                                    ImportableAndExportableModel $model)
    {
        $I->expectTo("see the records NOT from ".$file->name().
            " for the current school_id=".$this->school->getKey().
            " in the DB are DISABLED");

        $enabledIds = $this->itemsFromFile->lists('id');

        $disabledIds = $this->model
                            ->where('school_id', $this->school->getKey())
                            ->whereNotIn('id', $enabledIds)
                            ->get();

        if($disabledIds->isEmpty()) throw new Exception("Can't test that the rows not form the file are disabled: There are no disabled models for ".$file->name());

        $disabledIds->each(function($item) use($I, $model)
        {
            $item->enabled = true; // should NOT be enabled
            $I->dontSeeRecord($model->getTable(), $item->getAttributes());
        });
    }
}
