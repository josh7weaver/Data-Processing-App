<?php namespace DataStaging;

use DataStaging\Contracts\ImportableAndExportableModel;
use DataStaging\Models\School;
use Log;

class SchoolExporter{

	public function exportSchools()
	{
		Log::info("------------\n\nEXPORTING ALL CSV's\n");

		// just another way of doing foreach()
		School::enabled()->get()->each(function( $school )
		{
			$this->exportSchool( $school );
		});

		Log::info("Exported All Schools!\n");
	}

	public function exportSchool(School $school)
	{
        Log::info("\nExporting {$school->name} ({$school->id})\n");

		$this->createExportDir( $school->getFullExportPath() );

        foreach (Mapper::NAME_TO_IO_MODEL_MAP() as $fileType => $namespacedFileModelName)
        {
            try{
                $this->exportSchoolFile($school, new $namespacedFileModelName, $fileType);
            }
            catch(\Exception $e){
                Log::error($e->getMessage());
                continue;
            }
        }

        Log::info("Export success for ".$school->getFullExportPath()."\n");
	}

    /**
     * @param School                       $school
     * @param ImportableAndExportableModel $model
     * @param                              $fileType - 'course','section','enrollment','customer'
     * @throws \Exception
     */
    public function exportSchoolFile(School $school, ImportableAndExportableModel $model, $fileType)
    {
        $model->exportToFile($school, $fileType);
        Log::info("Exported all $fileType(s)\n");
    }

	public function createExportDir($path, $permission = '0777')
	{
		if( !file_exists($path) ){
			return mkdir($path, $permission, true);
		}
	}
}