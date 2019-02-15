<?php namespace DataStaging;

use Carbon\Carbon;
use DataStaging\Adjusters\AddBillingAcctAdjuster;
use DataStaging\Adjusters\CreateTestDataAdjuster;
use DataStaging\Adjusters\CustomerCopyPrefToNotesFieldAdjuster;
use DataStaging\Adjusters\CustomerEmailOverwriterAdjuster;
use DataStaging\Adjusters\IwuNrOldPrependAdjuster;
use DataStaging\Adjusters\RemoveOptedOutEnrollmentsAdjuster;
use DataStaging\Adjusters\SectionEnrollmentAdjuster;
use DataStaging\Adjusters\StudentAddressAdjuster;
use DataStaging\Adjusters\CustomerPreferenceAdjuster;
use DataStaging\Adjusters\CcckAdjuster;
use DataStaging\Adjusters\WarnerPrefManualAdjuster;
use DataStaging\Models\School;
use Illuminate\Filesystem\Filesystem;
use Log;

class DataStager{
    protected $log;
    protected $enabled;

    /**
     * @var ProcessApplicationLocker
     */
    protected $processLocker;

    /**
     * @var School
     */
    protected $school;

    /**
     * @var string - 24 hour time w/o leading zero
     */
    protected $currentHour;

    /**
     * @var Carbon
     */
    protected $currentTime;

    /**
     * @var BackupFiles
     */
    protected $backupFiles;

    public function __construct($schoolCode, array $enabled = ['import', 'adjust', 'export'])
	{
        $this->school        = School::where('code', $schoolCode)->enabled()->firstOrFail();
        $this->enabled       = $enabled;
        $this->processLocker = new ProcessApplicationLocker();
        $this->backupFiles   = app(BackupFiles::class, [$this->school]);
    }

    public function run()
    {
        $this->setupLogger($this->school);
        $this->processLocker->lockSchool($this->school);

        try{
            // We only want to pre-process for certain schools.
            if($this->school->getIsPreProcessingEnabled()){
                $this->processSchool();
            } else {
                $fs = new Filesystem();
                // Glob all of the files in the school's data_files directory
                $schoolFiles = $fs->files($this->school->getFullImportPath());

                $destinationDir = $this->school->getFullExportPath();
                $fs->cleanDirectory($destinationDir);
                // Export all of the other school files.
                foreach ($schoolFiles as $file){
                    $filename = basename($file);

                    if(str_contains(strtolower($filename), Mapper::VALID_FILE_NAMES())){
                        $fs->copy($file, $destinationDir.'/'.$filename);
                    }
                }
                \Log::info("\n\nPre-preprocessing is not enabled for ".$this->school->getName().". \n\nInstead, copied files\nfrom: {$this->school->getFullImportPath()}\nto: $destinationDir\n");
            }

            if($this->backupFiles->shouldDoBackupNow())
            {
                $this->backupFiles->setBackupType(BackupFiles::SCHOOL_DATA_FILES)->backup();
                $this->backupFiles->setBackupType(BackupFiles::SIDEWALK_FILES)->backup();
            }
        }
        catch(\Exception $e){
            Log::error($e->getMessage());
        }

        $this->processLocker->unlockSchool($this->school);
        $this->resetLogger();
    }

    private function processSchool()
    {
        // Import CSV files
        if($this->isEnabled('import'))
        {
            (new CcckAdjuster($this->school))->adjust();

            Log::info("------------\n\nIMPORTING ALL CSV's for {$this->school->getName()} ({$this->school->getKey()})\n");
            $schoolImporter = new SchoolImporter($this->school);
            $schoolImporter->importAllFiles();
        }

        // Run Adjustments on the data just imported
        if( $this->isEnabled('adjust') )
        {
            // order matters
                // BEFORE
//                (new AddBillingAcctAdjuster($this->school))->adjust();
                // AFTER
//                (new SectionEnrollmentAdjuster($this->school))->adjust();
//                (new CustomerCopyPrefToNotesFieldAdjuster($this->school))->adjust();
//                (new RemoveOptedOutEnrollmentsAdjuster($this->school))->adjust();

            // order independant
//                (new StudentAddressAdjuster($this->school))->adjust();
        }


        // Export CSV files
        if( $this->isEnabled('export') )
        {
            $schoolExporter = new SchoolExporter();
            $schoolExporter->exportSchool($this->school);
        }
    }

    /**
     * @param $component
     * @return bool
     */
    private function isEnabled($component)
    {
        return in_array($component, $this->enabled);
    }

    private function setupLogger(School $school)
    {
        $this->log = new Logger();
        $this->log
            ->addDbHandler($school)
            ->addExtendedDbProcessor();
    }

    private function resetLogger()
    {
        $this->log
            ->resetHandlers();
    }
}