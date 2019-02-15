<?php namespace DataStaging;

use DataStaging\Contracts\ImportableAndExportableModel;
use DataStaging\Models\School;
use DataStaging\Traits\ModelGetter;
use Log;

class SchoolImporter{

    use ModelGetter;

    /**
     * @var FileImporter
     */
    protected $fileImporter;

    /**
     * @var School
     */
    private $school;

    /**
     * @var array
     */
    private $files = [];

    public function __construct(School $school)
	{
        // All files in the directory
        $this->school = $school;
        $this->files 	   = $this->getAllFilesForSchool();
        $this->fileImporter = FileImporter::class;
    }

    public function importAllFiles()
    {
        // loop through searching for course/section/etc...
        foreach (Mapper::NAME_TO_IO_MODEL_MAP() as $modelName => $namespacedClassName) {

            $filePattern = $this->school->getFileNamePattern($modelName);

            Log::info("\n".strtoupper($modelName).": Attempting to import file(s) containing '$filePattern' in: ".$this->school->getFullImportPath()."\n");

            try{
                $this->importFilesMatchingPattern(
                    $this->getFilesMatching( $filePattern ),
                    app($this->fileImporter, [new $namespacedClassName, $this->school])
                );
            }
            catch(\InvalidArgumentException $e){
                Log::error($this->school->getName() . ": No files matched for $namespacedClassName model\n", [
                    'file pattern'=>$filePattern,
                    'directory'=>$this->school->getFullImportPath()
                ]);
            }

        }
    }

    /**
     * Attempts to save all files referenced by their path in the array of filepaths, called $setOfFiles
     * @param array        $filesMatchingPattern
     * @param FileImporter $fileImporter
     * @throws \ErrorException
     */
    private function importFilesMatchingPattern(array $filesMatchingPattern, FileImporter $fileImporter)
    {
        if(empty($filesMatchingPattern)){
            throw new \InvalidArgumentException;
        }

        $fileImporter->importAll($filesMatchingPattern);
    }

    private function getFilesMatching($filename)
    {
        return preg_grep("/$filename/i", $this->files);
    }

    /**
     * Get all the files of whatever type is defined in the schools table.
     * i.e. /import_dir/*.csv
     * @return array
     */
    private function getAllFilesForSchool()
    {
        return glob("/{$this->school->getFullImportPath()}/*.{$this->school->getFileExtension()}"); // returns array
    }
}