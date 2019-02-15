<?php namespace DataStaging;

use Carbon\Carbon;
use DataStaging\Models\School;
use Illuminate\Filesystem\Filesystem;
use Log;

class BackupFiles
{
    const SCHOOL_DATA_FILES = 'school_data_files';
    const SIDEWALK_FILES = 'sidewalk_files';

    /**
     * @var string
     */
    private $sourceDir;

    /**
     * @var string
     */
    private $destinationDir;

    /**
     * @var string - current hour with leading 0 (i.e. 01 or or 23)
     */
    private $currentHour;

    /**
     * @var Carbon
     */
    private $now;

    /**
     * Must be either self::SCHOOL_DATA_FILES or self::SIDEWALK_FILES
     * @var string
     */
    private $backupType;

    /**
     * @var School
     */
    private $school;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * BackupFiles constructor.
     * @param School     $school
     * @param Filesystem $filesystem
     */
    public function __construct(School $school, Filesystem $filesystem)
    {
        $this->school      = $school;
        $this->now         = Carbon::now();
        $this->currentHour = $this->now->format('G');
        $this->filesystem = $filesystem; // test this with a double -- should call copy() and files()
    }

    public function backup()
    {
        Log::info("Backing up the dir {$this->sourceDir} to {$this->destinationDir} \n");

        $this->createFolderStructure($this->destinationDir);

        foreach($this->filesystem->files($this->sourceDir) as $sourceFile){
            $this->copyFileToDestinationAddingTimestamp($sourceFile);
        }
    }

    public function createFolderStructure($backupDirPath)
    {
        // create the folder and all parent folders that don't exist.
        // had to use shell_exec bc the mkdir() php function was doing wonky
        // stuff with the file permissions and wasn't creating parents
        //        File::makeDirectory($backupDirPath, 775, true);
        shell_exec("mkdir -p $backupDirPath");
    }

    /**
     * Copy the given file to the appropriate destination, prepending a dateTime string to filename
     * @param $filePath  - absolute path for the source file
     */
    public function copyFileToDestinationAddingTimestamp($filePath)
    {
        $fileName = basename($filePath);

        if(strtolower($this->filesystem->extension($filePath)) !== 'csv') return; // only copy csv files

        $this->filesystem->copy($filePath, $this->getTimestampFilePath($fileName));
    }

    /**
     * @param $fileName - i.e. junk.csv
     * @return string
     * @throws \Exception
     */
    private function getTimestampFilePath($fileName)
    {
        if(is_null($this->destinationDir)) throw new \Exception('Destination directory is not set. Make sure to set the backup type.');

        return $this->destinationDir . $this->now->format('Y-m-d_H.i_') . $fileName;
    }

    /**
     * @return string
     */
    public function getDestinationDir()
    {
        return $this->destinationDir;
    }

    /**
     * @return string
     */
    public function getSourceDir()
    {
        return $this->sourceDir;
    }

    /**
     * Set backup type attribute as long as its a valid backup type
     * Set the desination directory and source directory also since they
     *  just depend on the backup type being set.
     * @param $backupType
     * @return $this
     */
    public function setBackupType($backupType)
    {
        if($this->isInvalidBackupType($backupType)){
            throw new \InvalidArgumentException("The backup type specified is invalid: {$this->backupType}");
        }

        $this->backupType = $backupType;

        $this->setDestinationDir()
             ->setSourceDir();

        return $this;
    }

    private function isInvalidBackupType($backupType)
    {
        return !in_array($backupType, [self::SCHOOL_DATA_FILES, self::SIDEWALK_FILES]);
    }

    /**
     * @return BackupFiles
     * @throws \Exception
     */
    private function setSourceDir()
    {
        switch ($this->backupType){
            case static::SCHOOL_DATA_FILES:
                $this->sourceDir = $this->school->getFullImportPath();
                break;

            case static::SIDEWALK_FILES:
                $this->sourceDir = $this->school->getFullExportPath();
                break;
        }

        return $this;
    }

    /**
     * @return BackupFiles
     */
    private function setDestinationDir()
    {
        $this->destinationDir = getenv('BACKUP_DIR').
            $this->school->getBackupPath() . '/' .  // school name
            $this->now->format('Y') . '/' . // 2016
            $this->now->format('M') . '/' . // Jan
            $this->backupType . '/';        // school_data_files

        return $this;
    }

    /**
     * @return bool
     */
    public function shouldDoBackupNow()
    {
        return in_array($this->currentHour, $this->getBackupTimeSchedule());
    }

    /**
     * The hours that the backup should run are stored in the ENV file as | deliniated string
     * @return array
     */
    private function getBackupTimeSchedule()
    {
        return explode('|', getenv('BACKUP_SCHEDULE'));
    }
}