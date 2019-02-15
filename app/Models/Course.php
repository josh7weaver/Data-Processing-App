<?php namespace DataStaging\Models;

use DataStaging\Contracts\FileImporterInterface;
use DataStaging\Contracts\HasDivisionName;
use DataStaging\RowValidator;

/**
 * @property mixed  campus - i.e. division
 * @property string b_delete
 * @property string comment
 * @property string description
 * @property string course
 * @property string department
 * @property string term
 */
class Course extends IoBaseModel implements HasDivisionName{

    const COLUMNS = 7;

	/**
	 * Specify the columns that have unique key constraints here
	 * 		-- declare in the model
	 * @var [array]
	 */
    protected $uniqueKeys = ['campus', 'term', 'department', 'course'];

	protected $guarded = [];

    /**
     * @throws \DataStaging\Exceptions\RowValidationException
     */
    public function validate()
    {
        $this->validator->checkCampusFieldIsValidDivisionName();
    }

    /**
     * Given a row of data, map each column into the appropriate object property
     *   and create new instance of the class.
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
            'description' => trim($rowFromFile[4]),
            'comment' => trim($rowFromFile[5]),
            'b_delete' => trim($rowFromFile[6]),
            'school_id' => $school->getKey(),
            'enabled' => true,
        ]);

        $instance->setOriginRow($rowFromFile);

        return $instance;
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

    /**
     * Concatenate the unique values for this course with | delimiter
     * @return string
     */
    public function courseKey()
    {
        return implode('|', array_flatten($this->getUniqueAttributes()));
    }

    public function getIwuData(School $school)
    {
        return IwunrCourse::query()
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
     *
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
            'description' => trim($rowFromFile[9]), // course name
            'comment' => trim($rowFromFile[11]),    // credit hours
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
            'description' => trim($rowFromFile[4]), // course name
            'comment' => trim($rowFromFile[5]),    // credit hours
            'b_delete' => 0,
        ]);
    }

}
