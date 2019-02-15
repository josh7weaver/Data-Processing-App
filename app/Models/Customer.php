<?php namespace DataStaging\Models;

use DataStaging\Contracts\FileImporterInterface;
use DataStaging\Contracts\ImportableAndExportableModel;
use DataStaging\Mapper;
use DataStaging\RowValidator;
use DataStaging\Util;

/**
 * @property string  customer_acct
 * @property string student_acct
 * @property string bad_check_num
 * @property string salutation
 * @property string  firstname
 * @property string  lastname
 * @property string alias
 * @property string email
 * @property string  notes
 * @property string active_date
 * @property string comment
 * @property string  preference
 * @property string type_code
 * @property string card_code
 * @property string member_amount
 * @property string ship_addr_desc
 * @property string  ship_addr
 * @property string  ship_city
 * @property string  ship_state
 * @property string  ship_postal_code
 * @property string ship_country
 * @property string ship_phone1
 * @property string ship_phone2
 * @property string ship_phone3
 * @property string ship_ext1
 * @property string ship_ext2
 * @property string ship_ext3
 * @property string bill_addr_desc
 * @property string bill_addr
 * @property string bill_city
 * @property string bill_state
 * @property string bill_postal_code
 * @property string bill_country
 * @property string bill_phone1
 * @property string bill_phone2
 * @property string bill_phone3
 * @property string bill_ext1
 * @property string bill_ext2
 * @property string bill_ext3
 * @property string b_delete - bool in db
 */
class Customer extends IoBaseModel{

    const COLUMNS = 39;

	public $timestamps = false;

	/**
	 *  sets the columns that exist in the DB that do NOT exist in the CSV file
	 * 		-- OVERRIDDEN from parent class
	 * @var [array]
	 */
	protected $ignoredColumns = ['id','school_id', 'preference', 'created_at', 'updated_at', 'enabled'];

	/**
	 * Specify the columns that have unique key constraints here
	 * 		-- declare in the model
	 * @var [array]
	 */
	protected $uniqueKeys = ['school_id', 'customer_acct'];

	protected $guarded = [];

    /**
     * @throws \DataStaging\Exceptions\RowValidationException
     */
    public function validate()
    {
        // DISABLE THIS CHECK for now as there were too many
        //  foreign students getting flagged as invalid that should not have been.
        //  requires more thought.
        //$this->validator->checkAddressIsValid();
        $this->validator->checkPostalCodeIsCorrectLength();
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
            'customer_acct' => Util::sanitize($rowFromFile[0]),
            'student_acct' => Util::sanitize($rowFromFile[1]),
            'bad_check_num' => Util::sanitize($rowFromFile[2]),
            'salutation' => Util::sanitize($rowFromFile[3]),
            'firstname' => Util::sanitize($rowFromFile[4]),
            'lastname' => Util::sanitize($rowFromFile[5]),
            'alias' => Util::sanitize($rowFromFile[6]),
            'email' => Util::sanitize($rowFromFile[7]),
            'notes' => Util::sanitize($rowFromFile[8]),
            'active_date' => Util::sanitize($rowFromFile[9]),
            'comment' => Util::sanitize($rowFromFile[10]),
            'type_code' => Util::sanitize($rowFromFile[11]),
            'card_code' => Util::sanitize($rowFromFile[12]),
            'member_amount' => Util::sanitize($rowFromFile[13]),
            'ship_addr_desc' => Util::sanitize($rowFromFile[14]),
            'ship_addr' => Util::sanitize($rowFromFile[15]),
            'ship_city' => Util::sanitize($rowFromFile[16]),
            'ship_state' => Util::sanitize($rowFromFile[17]),
            'ship_postal_code' => Util::sanitize($rowFromFile[18]),
            'ship_country' => Util::sanitize($rowFromFile[19]),
            'ship_phone1' => Util::sanitize($rowFromFile[20]),
            'ship_phone2' => Util::sanitize($rowFromFile[21]),
            'ship_phone3' => Util::sanitize($rowFromFile[22]),
            'ship_ext1' => Util::sanitize($rowFromFile[23]),
            'ship_ext2' => Util::sanitize($rowFromFile[24]),
            'ship_ext3' => Util::sanitize($rowFromFile[25]),
            'bill_addr_desc' => Util::sanitize($rowFromFile[26]),
            'bill_addr' => Util::sanitize($rowFromFile[27]),
            'bill_city' => Util::sanitize($rowFromFile[28]),
            'bill_state' => Util::sanitize($rowFromFile[29]),
            'bill_postal_code' => Util::sanitize($rowFromFile[30]),
            'bill_country' => Util::sanitize($rowFromFile[31]),
            'bill_phone1' => Util::sanitize($rowFromFile[32]),
            'bill_phone2' => Util::sanitize($rowFromFile[33]),
            'bill_phone3' => Util::sanitize($rowFromFile[34]),
            'bill_ext1' => Util::sanitize($rowFromFile[35]),
            'bill_ext2' => Util::sanitize($rowFromFile[36]),
            'bill_ext3' => Util::sanitize($rowFromFile[37]),
            'b_delete' => Util::sanitize($rowFromFile[38]),
            'school_id' => $school->getKey(),
            'enabled' => true,
        ]);
        $instance->setOriginRow($rowFromFile);

        return $instance;
	}

    public function getCustomerAcct()
    {
        return $this->customer_acct;
    }

    public function getPreference()
    {
        return $this->preference;
    }

    public function getName()
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    public function getComment()
    {
        return $this->comment;
    }

    public function getNotes()
    {
        return $this->notes;
    }

    public function getShipAddress()
    {
        return $this->ship_addr;
    }

    public function getShipCity()
    {
        return $this->ship_city;
    }

    public function getShipState()
    {
        return $this->ship_state;
    }

    public function getShipPostalCode()
    {
        return $this->ship_postal_code;
    }

    public function getInstructor()
    {
        return $this->customer_acct;
    }

    /**
     * Build the valid comment string based on pref and customer account
     * @param $preference - one of valid keys from Mapper::PREFERENCE_TO_COMMENT_MAP():
     *                      'rent', 'buy new', 'buy used', or 'opt out'
     * @param $customerAcct - corresponds to customer_acct field in DB
     * @return string
     * @throws \InvalidArgumentException
     */
    public function setComment($preference, $customerAcct)
    {
        $preference = $this->getCommentFormOfPreference($preference);
        $this->comment = "$preference / $customerAcct";
    }

    /**
     * @param $preference
     * @return string
     * @throws \InvalidArgumentException
     */
    private function getCommentFormOfPreference($preference)
    {
        $prefToCommentMap = Mapper::PREFERENCE_TO_COMMENT_MAP();
        $preference = strtolower($preference);

        if(!array_key_exists($preference, $prefToCommentMap)){
            throw new \InvalidArgumentException("The Preference must be in the map. Preference '$preference' given; Map contains '" . implode("','",array_keys($prefToCommentMap)) . "'");
        }

        return $prefToCommentMap[$preference];
    }

    /**
     * Concatenate the unique values for this customer with | delimiter
     * @return string
     */
    public function customerKey()
    {
        return implode('|', array_flatten($this->getUniqueAttributes()));
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
            'customer_acct' => trim($rowFromFile[0]),
            'student_acct' => $this->determineSecondaryAccount($rowFromFile),
            'bad_check_num' => '',
            'salutation' => '',
            'firstname' => trim($rowFromFile[1]),
            'lastname' => trim($rowFromFile[2]),
            'alias' => $this->determineAlias($rowFromFile),
            'email' => trim($rowFromFile[3]),
            'notes' => $this->getMiscFields($rowFromFile),
            'active_date' => '',
            'comment' => '', // let the adjuster catch this.
            'type_code' => '',
            'card_code' => '',
            'member_amount' => '',
            'ship_addr_desc' => '',
            'ship_addr' => trim($rowFromFile[11]),
            'ship_city' => trim($rowFromFile[12]),
            'ship_state' => trim($rowFromFile[13]),
            'ship_postal_code' => trim($rowFromFile[14]),
            'ship_country' => trim($rowFromFile[15]),
            'ship_phone1' => trim($rowFromFile[6]), // phone
            'ship_phone2' => trim($rowFromFile[6]), // mobile phone
            'ship_phone3' => '',
            'ship_ext1' => '',
            'ship_ext2' => '',
            'ship_ext3' => '',
            'bill_addr_desc' => '',
            'bill_addr' => '',
            'bill_city' => '',
            'bill_state' => '',
            'bill_postal_code' => '',
            'bill_country' => '',
            'bill_phone1' => '',
            'bill_phone2' => '',
            'bill_phone3' => '',
            'bill_ext1' => '',
            'bill_ext2' => '',
            'bill_ext3' => '',
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
            'customer_acct' => trim($rowFromFile[0]),
            'student_acct' => trim($rowFromFile[0]),
            'bad_check_num' => '',
            'salutation' => '',
            'firstname' => trim($rowFromFile[1]),
            'lastname' => trim($rowFromFile[2]),
            'alias' => trim($rowFromFile[3]),
            'email' => trim($rowFromFile[4]),
            'notes' => trim($rowFromFile[14]),
            'active_date' => '',
            'comment' => $this->formatCommentFromFields($rowFromFile),
            'type_code' => '',
            'card_code' => '',
            'member_amount' => '',
            'ship_addr_desc' => trim($rowFromFile[6]),
            'ship_addr' => trim($rowFromFile[7]),
            'ship_city' => trim($rowFromFile[8]),
            'ship_state' => trim($rowFromFile[9]),
            'ship_postal_code' => trim($rowFromFile[10]),
            'ship_country' => trim($rowFromFile[11]),
            'ship_phone1' => trim($rowFromFile[12]), // phone
            'ship_phone2' => '',
            'ship_phone3' => '',
            'ship_ext1' => '',
            'ship_ext2' => '',
            'ship_ext3' => '',
            'bill_addr_desc' => trim($rowFromFile[13]),
            'bill_addr' => '',
            'bill_city' => '',
            'bill_state' => '',
            'bill_postal_code' => '',
            'bill_country' => '',
            'bill_phone1' => '',
            'bill_phone2' => '',
            'bill_phone3' => '',
            'bill_ext1' => '',
            'bill_ext2' => '',
            'bill_ext3' => '',
            'b_delete' => 0,
        ]);
    }


    private function getMiscFields($rowFromFile)
    {
        return "program: " . trim($rowFromFile[8]) . " | " .
                "sms_opt_in: " . trim($rowFromFile[10]) . " | " .
                "year_in_school: ". trim($rowFromFile[7]) . " | " .
                "birthdate: ". trim($rowFromFile[5]) . " | " .
                "other_emails: ". trim($rowFromFile[4]);
    }

    /**
     * If a faculty member, we need return the instructor name in this field.
     * If its a student, just re-use the student id.
     * @param $rowFromFile
     * @return string
     */
    private function determineSecondaryAccount($rowFromFile)
    {
        if($this->isFaculty($rowFromFile)){
            return $this->getInstructorName($rowFromFile);
        }
        else{
            return trim($rowFromFile[0]); // student id
        }
    }

    /**
     * Only return an alias field if we're dealing with faculty member
     * @param $rowFromFile
     * @return string
     */
    private function determineAlias($rowFromFile)
    {
        if($this->isFaculty($rowFromFile)){
            return $this->getFacultyIndicator($rowFromFile);
        }
    }

    private function getInstructorName($rowFromFile)
    {
        // first_name last_name
        return trim($rowFromFile[1]) ." ". trim($rowFromFile[2]);
    }

    private function getFacultyIndicator($rowFromFile)
    {
        // we are hiding the faculty indicator in the year_in_school field currently
        return trim($rowFromFile[7]); // year_in_school;
    }

    private function isFaculty($rowFromFile)
    {
        return str_contains(strtolower($this->getFacultyIndicator($rowFromFile)), 'faculty');
    }

    private function formatCommentFromFields($rowFromFile)
    {
        if($rowFromFile[15])
        {
            $preference = $this->getCommentFormOfPreference(trim($rowFromFile[15]));
        }
        else
        {
            $preference = "";
        }

        $customerAcct = trim($rowFromFile[5]);

        return "$preference / $customerAcct";
    }
}
