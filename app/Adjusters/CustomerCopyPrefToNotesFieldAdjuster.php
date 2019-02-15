<?php namespace DataStaging\Adjusters;

use DataStaging\Contracts\Adjuster;
use DataStaging\Models\School;
use DataStaging\Traits\TbbSchoolHelpers;
use DB;
use Illuminate\Database\QueryException;
use Log;

/*
 * This class copys the preference from the COMMENT field to the NOTES field
 * as "opted in" to flag in the client for people using the register. That way when
 * they're checking students out they don't sell them books if they already have books
 * coming to them through the TBB program.
 *
 * DEPENDANCY: MUST BE RUN AFTER THE CUSTOMER PREF ADJUSTER
 */
class CustomerCopyPrefToNotesFieldAdjuster implements Adjuster
{
    use TbbSchoolHelpers;

    /**
     * @var School
     */
    protected $school;

    public function __construct(School $school)
    {
        $this->school = $school;

    }

    public function adjust()
    {
        Log::info("------------\n\nADJUSTING: Copy preference to NOTES field\n\n");

        if($this->isNotTbbSchool($this->school)){
            Log::info("School {$this->school->getName()} is not a TBB school, skipping copy customer pref to notes\n");
            return false;
        }

        try{
            $affectedRows = $this->adjustNotes();
        }
        catch (QueryException $e){
            Log::alert('There was an error adjusting the notes field in CustomerCopyPrefToNotesFieldAdjuster: '. $e->getMessage());
            return;
        }

        Log::info("$affectedRows rows affected\n");
    }

    /**
     * alter notes field for any text book butler school where COMMENT isn't "opt out"
     *      prepend Opted In
     * @return int|warning if note length is too long
     */
    public function adjustNotes()
    {
        return DB::affectingStatement("
            UPDATE
                customers
            SET
                notes = CONCAT('Opted In ', notes)
            WHERE
                school_id = :school_id
                AND comment NOT ILIKE '%out%'
                AND enabled = TRUE;
        ", [
            ':school_id' => $this->school->getKey()
        ]);
    }
}