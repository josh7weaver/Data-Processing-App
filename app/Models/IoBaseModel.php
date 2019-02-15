<?php namespace DataStaging\Models;

use DataStaging\Contracts\CanBeExported;
use DataStaging\MysqlBuilder;
use DataStaging\RowValidator;
use DataStaging\Traits\EloquentScopes;
use DataStaging\Traits\EloquentAccessorsAndIO;
use DataStaging\Contracts\ImportableAndExportableModel;
use DataStaging\Traits\ModelGetter;
use Eloquent;
use Illuminate\Database\QueryException;
use Schema;

abstract class IoBaseModel extends Eloquent implements ImportableAndExportableModel{

	use EloquentScopes;
    use ModelGetter;

    public $timestamps = false;

	/**
	 * $this->ignoredColumns sets the columns that exist in the DB that do NOT exist in the CSV file
	 * 		You can override in given model (i.e. if Course Model has extra field other models don't,
	 * 		which is also not in csv file)
     *
     * Used for both importing and exporting.
	 * @var [array]
	 */
	protected $ignoredColumns = ['id', 'school_id', 'created_at', 'updated_at', 'enabled'];

    /**
     * Specify the columns that have unique key constraints here
     * 		-- DECLARE IN THE MODEL
     * @var [array]
     */
    protected $uniqueKeys = [];

    /**
     * Cast b_delete to an integer for the export process so it writes a zero. This only happens when you call to_array(), NOT getAttributes()
     */
    protected $casts = [
        'b_delete' => 'integer'
    ];

    /**
     * the row that created this model
     */
    protected $originRow;

    /**
     * @var RowValidator
     */
    protected $validator;

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setValidator();

        $this->postConstructorHook();
    }

    /**
     * Can be overridden in children to allow access to the constructor
     */
    protected function postConstructorHook()
    {
        return null;
    }

    /**
     * Persist the given record to the store and return the ID
     * @return mixed
     * @throws \Exception
     */
    public function persist()
    {
        $instance = static::updateOrCreate($this->getUniqueAttributes(), $this->getNonUniqueAttributes());

        if(!$instance->getKey()) throw new \Exception("didn't save right...");

        return $instance->getKey();
    }

    public function exportToFile(CanBeExported $school, $filename, $includeHeader = false)
    {
            // getting all the elements from the DB
            $modelCollection = $this->select($this->getColumnListingForFile())
                                    ->where('school_id', $school->getKey())
                                    ->enabled()
                                    ->get();
        }

        if($modelCollection->isEmpty()) throw new \Exception("There are no enabled model rows for " . get_class($this) . "\n");

        // writing to the file
        $file = $school->getFileInstanceFor($filename);

        if($includeHeader){
            $file->putRow($this->getColumnListingForFile());
        }

        foreach ($modelCollection as $instance)
        {
            $file->putRow(
                // toArray() is ok bc we're only selecting mappable attributes above in select() statement
                // can't use getAttributes() bc it doesn't run through the accessors or casts, and we need to cast b_delete to int for export
                $instance->toArray()
            );
        }
    }

    /**
     * This returns the db columns to import/export - i.e columns mappable to csv file
     *    Item order communicates CSV col order to map(), override to customize which cols to use
     * @return array [array] - numerical indexed array of column names. [0=>'name', ... etc]
     */
    public function getColumnListingForFile()
    {
        $sharedColumns = array_diff( $this->getColumnListing() , $this->ignoredColumns ); 	// remove the ignored columns that don't exist in CSV (diff on values)
        return array_values($sharedColumns); 											// reindex the array
    }

    protected function getColumnListing()
    {
        return Schema::getColumnListing( $this->getTable() );
    }

    public function getColumnCount()
    {
        if(is_array($this->getOriginRow()))
        {
            return count($this->getOriginRow());
        }
    }

    public function getOriginRow()
    {
        return $this->originRow;
    }

    public function getExpectedColumnCount()
    {
        return static::COLUMNS;
    }

    public function getAttributesForFile()
    {
        return array_diff_key($this->getAttributes(), array_flip($this->ignoredColumns));
    }

    public function getUniqueAttributes()
    {
        return array_intersect_key($this->getAttributes(), array_flip($this->getUniqueKeys()));
    }

    public function getNonUniqueAttributes()
    {
        return array_diff_key($this->getAttributes(), $this->getUniqueAttributes());
    }

    public function getValidator()
    {
        return $this->validator;
    }

    public function getUniqueKeys()
    {
        return $this->uniqueKeys;
    }

    public function setOriginRow(array $originRow)
    {
        $this->originRow = $originRow;
    }

    /*
     * If this concrete instance has attributes, then this is a hydrated instance
     * that needs a validator. Otherwise its a generic Instance that could be used
     * to run queries or access constants. In the case that its a generic instance,
     * setting the validator could cause an infinite loop if initialized inside the
     * RowValidator class.
     */
    public function setValidator()
    {
        if(empty($this->getAttributes())) return;

        $this->validator = app(RowValidator::class, [$this]);
    }

    /**
     * Allows you to call ::getBaseName() statically or from object instance
     * @return string
     */
    public function scopeGetBaseName()
    {
        return class_basename(static::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function shouldBeDisabled()
    {
        return $this->b_delete; // 0 or 1
    }
}