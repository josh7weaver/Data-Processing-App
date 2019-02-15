<?php namespace DataStaging\Traits;

use DataStaging\Models\School;
use DataStaging\Models\TbbSchool;

trait TbbSchoolHelpers
{
    /**
     * Checks to see if school is in the tbb_school_data table.
     * Does NOT take the 'use_butler' bool flag into account.
     *
     * @param School $school
     * @return mixed
     */
    public function isTbbSchool(School $school)
    {
        $tbbSchools = TbbSchool::all();
        return $tbbSchools->contains($school);
    }

    /**
     * Opposite of "isTbbSchool"
     * Does NOT take the 'use_butler' bool flag into account.
     * @param School $school
     * @return bool
     */
    public function isNotTbbSchool(School $school)
    {
        return ! $this->isTbbSchool($school);
    }
}