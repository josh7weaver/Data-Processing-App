<?php namespace DataStaging\Models;

use Carbon\Carbon;
use DataStaging\Contracts\FileImporterInterface;
use DataStaging\Contracts\HasDivisionName;
use DataStaging\Contracts\ImportableAndExportableModel;
use DataStaging\RowValidator;

/**
 * @property mixed  school_id
 * @property string campus - i.e. division
 * @property mixed  course
 * @property mixed  department
 * @property mixed  term
 * @property mixed  comment
 * @property string act_enrollment
 * @property string est_enrollment
 * @property string instructor
 * @property string section
 * @property string b_delete
 * @property bool   enabled
 */
class Section extends IoBaseModel implements HasDivisionName{

    const COLUMNS = 10;

    /**
     * Used to extract date... its kind of a hack. I'm sorry.
     */
    const COMMENT_PATTERN = '/(\b[0-9]+\/[0-9]+\/[0-9]+\b)(?:\s|\S){0,3}(\b[0-9]+\/[0-9]+\/[0-9]+\b)/';

	/**
	 * Specify the columns that have unique key constraints here
	 * 		-- declare in the model
	 * @var [array]
	 */
    protected $uniqueKeys = ['campus', 'term', 'department', 'course', 'section'];

	protected $guarded = [];

//    protected $casts = [

//        'est_enrollment' => 'integer',

//        'act_enrollment' => 'integer',
//        'b_delete' => 'boolean',
//        'enabled' => 'boolean',
//    ];
    /**
     * @throws \DataStaging\Exceptions\RowValidationException
     */
    public function validate()
    {
        $this->validator->checkCampusFieldIsValidDivisionName();
        $this->validator->checkEndDateIsAfterStartDate();
    }

    /**
     * Given a row of data, map each column into an associative array
     *   with keys corresponding to the column names in the database
     * @param array  $rowFromFile
     * @param School $school
     * @return ImportableAndExportableModel
     * @throws \DataStaging\Exceptions\RowValidationException
     */
    public static function newInstanceFromFile(array $rowFromFile, School $school)
    {
        RowValidator::checkColumnCountMatchesExpected($rowFromFile, static::COLUMNS);

        $instance = new self([
            'campus' => trim($rowFromFile[0]),
            'term' => trim($rowFromFile[1]),
            'department' => trim($rowFromFile[2]),
            'course' => trim($rowFromFile[3]),
            'section' => trim($rowFromFile[4]),
            'instructor' => trim($rowFromFile[5]),
            'est_enrollment' => trim($rowFromFile[6]),
            'act_enrollment' => trim($rowFromFile[7]),
            'comment' => trim($rowFromFile[8]),
            'b_delete' => trim($rowFromFile[9]),
            'school_id' => $school->getKey(),
            'enabled' => true,
        ]);
        $instance->setOriginRow($rowFromFile);

        return $instance;
    }

    public function division()
    {
        return $this->belongsTo( $this->getModel('Division'), 'campus', 'name');
    }

    public function butlerEnrollment()
    {
        return $this->hasOne( $this->getModel('viewtbbenrollment'));
    }

    public function getButlerEnrollment()
    {
        if(!is_null($this->butlerEnrollment))
        {
            return $this->butlerEnrollment->counted_enrollment;
        }
        else {
            // its null, so return 0
            return 0;
        }
    }

    /**
     * @return string
     */
    public function getInstructor()
    {
        return $this->instructor;
    }

    /**
     * @return Carbon|null
     * @throws \RuntimeException
     */
    public function getStartDate()
    {
        return $this->extractDate('start');
    }

    /**
     * @return Carbon|null
     * @throws \RuntimeException
     */
    public function getEndDate()
    {
        return $this->extractDate('end');
    }

    public function getDivision()
    {
        return $this->campus;
    }

    public function getTerm()
    {
        return $this->term;
    }

    public function getDepartment()
    {
        return $this->department;
    }

    public function getCourse()
    {
        return $this->course;
    }

    public function getSection()
    {
        return $this->section;
    }

    public function setEstEnrollmentAttribute($value)
    {
        $this->attributes['est_enrollment'] = (int) $value;
    }

    public function setActEnrollmentAttribute($value)
    {
        $this->attributes['act_enrollment'] = (int) $value;
    }

    /**
     * @param mixed $term
     */
    public function setTerm($term)
    {
        $this->term = $term;
    }

    /**
     * @param string $division
     */
    public function setDivision($division)
    {
        $this->campus = $division;
    }

    /**
     * extract start and end date from the date string that MAY be in the comment field
     * @param        $dateType - can be 'start' or 'end'
     * @return Carbon|null
     * @throws \RuntimeException
     */
    public function extractDate($dateType)
    {
        if($this->isCommentBlank()) throw new \RuntimeException(strtoupper($dateType) ." date cannot be extracted because the comment field is blank.");

        // expecting "2015/01/14 - 2015/05/07"
        // if found, $startAndEndDate will be an array [0=>full match, 1=>start date, 2=>end date]
        $patternFound = preg_match(self::COMMENT_PATTERN, $this->comment, $startAndEndDate);

        if(!$patternFound) throw new \RuntimeException(strtoupper($dateType) ." date cannot be extracted because the comment field does not contain a match with the expected pattern of 'YYYY/mm/dd - YYYY/mm/dd'.");

        switch($dateType)
        {
            case 'start':
                return $this->createDateFromString($startAndEndDate[1]);
            case 'end':
                return $this->createDateFromString($startAndEndDate[2]);
            default:
                return null;
        }
    }

    /**
     * @param string $dateString - must match given format param
     * @param string $format
     * @return Carbon
     * @throws \InvalidArgumentException
     */
    protected function createDateFromString($dateString, $format = 'Y/m/d')
    {
        return Carbon::createFromFormat($format, trim($dateString));
    }

    protected function isCommentBlank()
    {
        return is_null($this->comment) || $this->comment == '';
    }

    /**
     * Concatenate the unique values in this section corresponding to course with | delimiter
     * @return string
     */
    public function courseKey()
    {
        return $this->getDivision() . '|' .
            $this->getTerm() . '|' .
            $this->getDepartment() . '|' .
            $this->getCourse();
    }

    /**
     * Concatenate the unique values for this section with | delimiter
     * @return string
     */
    public function sectionKey()
    {
        return implode('|', array_flatten($this->getUniqueAttributes()));
    }

    public function getIwuData(School $school)
    {
        return IwunrSection::query()
            ->where('school_id', $school->getKey())
            ->select($this->getColumnListingForFile())
            ->get();
    }

    /**
     * Given a row from a Rafter format file, return the standard format for columns of the
     *   corresponding file for normal data files.
     *
     * Adoption -> Course
     * Adoption -> Section
     * Enrollment -> Enrollment
     * Student -> Customer
     *`
     * @param array  $rowFromFile
     * @param School $school
     * @return array
     */
    public function coerceRafterToStandardFormat(array $rowFromFile, School $school)
    {
        return array_values([
            'campus' => $school->getSingleDivisionNameOrDefault(trim($rowFromFile[0])),
            'term' => trim($rowFromFile[1]),
            'department' => trim($rowFromFile[6]), // this is department code
            'course' => trim($rowFromFile[8]),      // course code
            'section' => trim($rowFromFile[10]),
            'instructor' => trim($rowFromFile[12]), // professor field, fn ln
//            'instructor' => trim($rowFromFile[17]), // faculty_id
            'est_enrollment' => trim($rowFromFile[15]),
            'act_enrollment' => trim($rowFromFile[16]),
            'comment' => $this->buildComment($rowFromFile[4], $rowFromFile[5], 'm/d/Y'),    // start - end dates:  Y/m/d - Y/m/d
            'b_delete' => 0,
        ]);
    }

    /**
     * Given a row from a New format file, return the standard format for columns of the
     *   corresponding file for normal data files.
     *
     * Catalog -> Course
     * Catalog -> Section
     * Enrollment -> Enrollment
     * Customer -> Customer
     *
     * @param array  $rowFromFile
     * @param School $school
     * @return array
     */
    public function coerceThreeFileFormatToStandardFormat(array $rowFromFile, School $school)
    {
        return array_values([
            'campus' => $school->getSingleDivisionNameOrDefault(trim($rowFromFile[0])),
            'term' => trim($rowFromFile[1]),
            'department' => trim($rowFromFile[2]), // this is department code
            'course' => trim($rowFromFile[3]),      // course code
            'section' => trim($rowFromFile[6]),
            'instructor' => trim($rowFromFile[7]), // professor field, fn ln
            'est_enrollment' => trim($rowFromFile[8]),
            'act_enrollment' => trim($rowFromFile[9]),
            'comment' => $this->buildComment($rowFromFile[10], $rowFromFile[11], 'Y-m-d'),    // start - end dates:  Y-m-d - Y-m-d
            'b_delete' => 0,
        ]);
    }

    /**
     * Returns string of "START_DATE - END_DATE" in format "Y/m/d - Y/m/d"
     * @param $startDateColumn
     * @param $endDateColumn
     * @param $inputDateFormat - valid date format i.e. m/d/Y
     * @return string
     */
    private function buildComment($startDateColumn, $endDateColumn, $inputDateFormat)
    {
        $startDate = $this->createDateFromString(trim($startDateColumn), $inputDateFormat);
        $endDate = $this->createDateFromString(trim($endDateColumn), $inputDateFormat);

        return $startDate->format('Y/m/d') . ' - ' . $endDate->format('Y/m/d');
    }
}
