<?php namespace DataStaging\Contracts;

use DataStaging\Models\School;

interface ImportableAndExportableModel{

    /**
     * Main entrance to save all rows from a file at once
     * @param array $collection
     * @return
     */
//	public function bulkImport(array $collection);

    /**
     * @param CanBeExported $school
     * @param               $path
     * @param bool|false    $includeHeader
     * @return mixed
     * @throws \Exception
     */
    public function exportToFile(CanBeExported $school, $path, $includeHeader = false);

    public function getAttributesForFile();

    /**
     * The count() of the origin row if model was created from a file.
     * @return int
     */
    public function getColumnCount();

    /**
     * Access the static::COLUMNS constant within an instance
     * @return int
     */
    public function getExpectedColumnCount();

    /**
     * If the model was created from a file row, this
     * is the original row as an array.
     * @return array
     */
    public function getOriginRow();

    public function getUniqueKeys();

    /**
     * Currently only used for tests
     * @return \DataStaging\RowValidator
     */
    public function getValidator();

    /**
     * Given a row of data, map each column into an associative array
     *   with keys corresponding to the column names in the database
     * @param array  $rowFromFile
     * @param School $school
     * @return ImportableAndExportableModel
     */
    public static function newInstanceFromFile(array $rowFromFile, School $school);

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
    public function coerceRafterToStandardFormat(array $rowFromFile, School $school);

    /**
     * Persist the given record to the store and return the ID
     * @return mixed
     */
    public function persist();

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
    public function coerceThreeFileFormatToStandardFormat(array $rowFromFile, School $school);

    /**
     * Set the originRow property
     * @param array $originRow
     * @return mixed
     */
    public function setOriginRow(array $originRow);

    /**
     * Run whatever validations are assigned to the current model.
     * Each model can define its own validations run through a validator instance
     * stored in the $this->validator property, which can be accessed via $this->getValidator()
     * @return mixed
     * @throws \DataStaging\Exceptions\RowValidationException
     */
    public function validate();
}
