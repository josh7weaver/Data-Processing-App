<?php namespace DataStaging\Models;

use DataStaging\Contracts\FileImporterInterface;
use DataStaging\Contracts\ImportableAndExportableFile;
use DataStaging\Contracts\ImportableAndExportableModel;
use DataStaging\RowValidator;

/**
 * @property string b_delete
 * @property string comment
 * @property string section
 * @property string course
 * @property string department
 * @property string term
 * @property string campus
 * @property string student_id
 */
class Enrollment extends IoBaseModel{

    const COLUMNS = 8;

	/**
	 * Specify the columns that have unique key constraints here
	 * 		-- declare in the model
	 * @var [array]
	 */
    protected $uniqueKeys = ['campus','student_id','term', 'department', 'course', 'section'];

	protected $guarded = [];

    /**
     * @throws \DataStaging\Exceptions\RowValidationException
     */
    public function validate()
    {
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
            'student_id' => trim($rowFromFile[0]),
            'campus' => trim($rowFromFile[1]),
            'term' => trim($rowFromFile[2]),
            'department' => trim($rowFromFile[3]),
            'course' => trim($rowFromFile[4]),
            'section' => trim($rowFromFile[5]),
            'comment' => trim($rowFromFile[6]),
            'b_delete' => trim($rowFromFile[7]),
            'school_id' => $school->getKey(),
            'enabled' => true,
        ]);
        $instance->setOriginRow($rowFromFile);

        return $instance;
    }

    public function getCustomerAcct()
    {
        return $this->student_id;
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

    public function setDivision($division)
    {
        $this->campus = $division;
    }
    /**
     * Concatenate the unique values in this enrollment corresponding to customer with | delimiter
     * @return string
     */
    public function customerKey()
    {
        return $this->school->getKey() . '|' .
        $this->getCustomerAcct();
    }

    /**
     * Concatenate the unique values in this enrollment corresponding to section with | delimiter
     * @return string
     */
    public function sectionKey()
    {
        return $this->getDivision() . '|' .
            $this->getTerm() . '|' .
            $this->getDepartment() . '|' .
            $this->getCourse() . '|' .
            $this->getSection();
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
            'student_id' => trim($rowFromFile[0]),
            'campus' => $school->getSingleDivisionNameOrDefault(trim($rowFromFile[1])),
            'term' => trim($rowFromFile[2]),
            'department' => trim($rowFromFile[3]),
            'course' => trim($rowFromFile[4]),
            'section' => trim($rowFromFile[5]),
            'comment' => '',
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
            'student_id' => trim($rowFromFile[0]),
            'campus' => $school->getSingleDivisionNameOrDefault(trim($rowFromFile[1])),
            'term' => trim($rowFromFile[2]),
            'department' => trim($rowFromFile[3]),
            'course' => trim($rowFromFile[4]),
            'section' => trim($rowFromFile[5]),
            'comment' => trim($rowFromFile[6]),
            'b_delete' => 0,
        ]);
    }

}
