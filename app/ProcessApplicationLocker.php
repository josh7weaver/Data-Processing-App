<?php namespace DataStaging;

use DataStaging\Models\ProcessLock;
use DataStaging\Models\School;
use Log;

class ProcessApplicationLocker
{
    /**
     * @var ProcessLock
     */
    protected $processLock;

    /**
     * ProcessApplicationLocker constructor.
     */
    public function __construct()
    {
        $this->processLock = new ProcessLock;
    }

    /**
     * @param School $school
     * @return int - the total number of deleted rows
     */
    public function unlockSchool(School $school)
    {
        Log::notice("\nApplication lock unset for school ".$school->getName().".\n\n");
        return $this->processLock->destroy($school->getKey());
    }

    /**
     * @param School $school
     * @return bool
     */
    public function lockSchool(School $school)
    {
        Log::notice("Set application lock for school ".$school->getName()."\n");
        $processLock = $this->processLock->newInstance();
        $processLock->school_id = $school->getKey();
        return $processLock->save();
    }

    /**
     * @return bool
     */
    public function isLocked()
    {
        return $this->processLock->exists();
    }
}