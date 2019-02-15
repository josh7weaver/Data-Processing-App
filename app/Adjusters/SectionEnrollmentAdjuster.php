<?php namespace DataStaging\Adjusters;

use DataStaging\Contracts\Adjuster;
use DataStaging\Models\Division;
use DataStaging\Models\School;
use DataStaging\Models\Section;
use Log;

/*
 * Adjust the enrollment numbers we're passing in the section files
 * to sidewalk. This is for Kelly and the textbook purchasing team.
 *
 * DEPENDANCY: MUST BE RUN AFTER THE CUSTOMER PREF ADJUSTER
 */
class SectionEnrollmentAdjuster implements Adjuster
{

    /**
     * @var School
     */
    private $school;

    public function __construct(School $school)
    {
        $this->school = $school;
    }

    public function adjust()
    {
        Log::info("------------\n\nADJUSTING SECTION ENROLLMENT\n\n");

		$tbbDivisions = $this->school->tbbUsingPortalDivisions();

		if($tbbDivisions->isEmpty()){
			Log::info("There were no tbb divisions for ".$this->school->getName()."\n");
			return false;
		}

        foreach($tbbDivisions as $division )
        {
			$this->adjustSectionEnrollmentFor( $division );
		}

        Log::info('Ran Adjustments for Divisions: ', $tbbDivisions->lists('name'));
	}

    public function adjustSectionEnrollmentFor(Division $division)
	{
		Log::info("\n\nDIVISION: {$division->name}\n\n");

        $sectionsForDivision = $division->sections()
                    ->enabled()
                    ->with('butlerEnrollment')
                    ->get();

        if($sectionsForDivision->isEmpty()){
            Log::warning("Adjusting Section Enrollment: Can't find any sections for the ".$division->getName()." division.\n".
                "Attempted to find sections and their accompanying tbb enrollment counts, none found.");
        }

        foreach ($sectionsForDivision as $section) {

            if ($division->shouldAdjustEnrollmentByPercent()) {
                // if it has an adjustment percentage, recalcuate with it, compare to butler enrollment,
                // and save the larger of the two
                $newActEnrollment = $this->recalculateEnrollment( $section->act_enrollment, $division->getEnrollmentPercentage() );
                $largerEnrollment = $this->pickLarger( $newActEnrollment, $section->getButlerEnrollment() );

                $this->save( $section, $largerEnrollment);

            } else {
                // for sections belonging to all divisions NOT being adjusted by a percentage,
                // 	always use the calculated TB butler enrollment #
                if( is_null($butlerEnrollment = $section->getButlerEnrollment()) ){
                    // if its null it means the count was 0, so lets just set to zero and save
                    $butlerEnrollment = 0;
                    Log::info("...altered enrollment count from null to 0 >>> ");
                }

                Log::info("USE BUTLER: Butler = $butlerEnrollment, Actual = {$section->act_enrollment}: ");
                $this->save( $section, $butlerEnrollment );
            }
        }
    }

	public function recalculateEnrollment($enrollment, $adjustmentPercentage)
	{
		return ceil( $enrollment * $adjustmentPercentage );
	}

	public function pickLarger( $newActEnrollment, $butlerEnrollment )
	{
		if ($newActEnrollment >= $butlerEnrollment) {
			Log::info("COMPARING: adjusted($newActEnrollment) > butler($butlerEnrollment): ");
			return $newActEnrollment;
		}

		Log::info("COMPARING: butler($butlerEnrollment) > adjusted($newActEnrollment): ");
		return $butlerEnrollment;
	}

	public function save(Section $section, $enrollment )
	{
		if ( is_null($enrollment) ) {
			throw new \ErrorException("Enrollment can't be null. Attempting to save null enrollment");
		}

		Log::info("Update section {$section->id} ({$section->term}/{$section->department}/{$section->course}/{$section->section}) enrollment as $enrollment\n");
		$section->act_enrollment = $enrollment;
		return $section->save();
	}
}