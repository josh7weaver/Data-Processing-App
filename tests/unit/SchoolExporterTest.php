<?php

use \DataStaging\SchoolExporter;

class SchoolExporterTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var \DataStaging\SchoolExporter
     */
    protected $exporter;

    protected $school;

    protected $course;
    protected $section;
    protected $enrollment;
    protected $customer;
    protected $initialized;

    protected function _before()
    {
        $this->course = new \DataStaging\Models\Course;
        $this->section = new \DataStaging\Models\Section;
        $this->enrollment = new \DataStaging\Models\Enrollment;
        $this->customer = new \DataStaging\Models\Customer;

        $this->school = \DataStaging\Models\School::where('code', 'TU')->firstOrFail();

        $this->exporter = new SchoolExporter;

        DB::beginTransaction();
    }

    protected function _after()
    {
        DB::rollBack();
    }

    public function testExportSchool()
    {
        $this->exporter->exportSchool($this->school);

        $this->assertFileExists($this->school->getFullExportPath());
        $this->assertFileExists($this->school->getFullExportFilePath('course'));
        $this->assertFileExists($this->school->getFullExportFilePath('section'));
        $this->assertFileExists($this->school->getFullExportFilePath('enrollment'));
        $this->assertFileExists($this->school->getFullExportFilePath('customer'));
    }

    public function testExportSchoolDoesntCreateBlankFileIfNothingEnabled()
    {
        $this->school->courses()->delete();

        $this->exporter->exportSchool($this->school);

        $this->assertStringNotEqualsFile($this->school->getFullExportFilePath('course'), '');
    }

    public function testExportSchoolFileCreatesFile()
    {
        $data = [
            ['model' => $this->course, 'modelName' => 'course'],
            ['model' => $this->section, 'modelName' => 'section'],
            ['model' => $this->enrollment, 'modelName' => 'enrollment'],
            ['model' => $this->customer, 'modelName' => 'customer'],
        ];

        foreach($data as $item){
            $this->runExportSchoolFileCreatesFile($item['model'], $item['modelName']);
        }
    }

    /**
     * @param $model
     * @param $modelName
     */
    private function runExportSchoolFileCreatesFile($model, $modelName)
    {
        $this->exporter->exportSchoolFile($this->school, $model, $modelName);

        $this->assertFileExists($this->school->getFullExportFilePath($modelName));
    }

    public function provideTestExportSchoolFileCsvHasCorrectData()
    {
        $data = [
            ['model' => $this->course, 'modelName' => 'course', 'modelQuery' => $this->school->courses()],
            ['model' => $this->section, 'modelName' => 'section', 'modelQuery' => $this->school->sections()],
            ['model' => $this->enrollment, 'modelName' => 'enrollment', 'modelQuery' => $this->school->enrollments()],
            ['model' => $this->customer, 'modelName' => 'customer', 'modelQuery' => $this->school->customers()],
        ];

        foreach($data as $item){
            $this->runnExportSchoolFileCsvHasCorrectData($item['model'], $item['modelName'], $item['modelQuery']);
        }
    }

    /**
     * @param $model
     * @param $modelName
     * @param $modelQuery
     */
    private function runnExportSchoolFileCsvHasCorrectData($model, $modelName, $modelQuery)
    {
        $allEntities = $modelQuery->enabled()->get($model->getColumnListingForFile());
        $this->assertFalse($allEntities->isEmpty());

        $allEntityValues = $allEntities->map(function($entity)
        {
            return array_values($entity->getAttributes());
        })
                                       ->toArray();
//        dump($allEntityValues);

        $file = $this->school->getFileInstanceFor($modelName, 'r');

        $this->tester->expect("All the enabled rows in the database should be present in the exported csv file");
        $file->map(function($row) use($allEntityValues)
        {
//            dump($row);
            $this->assertTrue(in_array($row, $allEntityValues));
        });
    }
}