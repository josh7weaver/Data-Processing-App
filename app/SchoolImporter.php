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
        $matchingFiles = preg_grep("/$filename/i", $this->files);

        /*
         * If you're reading this, there's been a terrible mistake. I blame Chris.
         * This is an artifact from the IWU merge process when residential and NonRes were merged.
         * Basically, we need to make sure that the Non res customer info ALWAYS gets imported before
         * the Residential customer info so that the residential customer info is saved.
         *
         * Depends on the following naming convention:
         *   Non Res: iwu_customer.csv
         *   Residential: iwu_nr_customer.csv
         */
        if($this->school->getCode() == "IWU"){
            Log::info("Reversed the order files are processed for IWU - Res cust file LAST", $matchingFiles);
            return array_reverse($matchingFiles);
        }
        return $matchingFiles;
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