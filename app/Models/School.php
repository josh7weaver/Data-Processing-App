<?php namespace DataStaging\Models;

use DataStaging\Contracts\CanBeExported;
use DataStaging\Mapper;
use DataStaging\Traits\ModelGetter;
use DataStaging\Traits\EloquentScopes;
use Illuminate\Database\Eloquent\Model;
use Log;

class School extends Model implements CanBeExported
{

	use EloquentScopes;
    use ModelGetter;

    protected $guarded = [];
    public $timestamps = false;

    /**
     * The file corresponding to the given model in the filesystem should contain this pattern.
     * Gets the value for course_pattern, section_pattern, enrollment_pattern, or customer_pattern in the db
     * @param $modelName
     * @return string|null
     */
    public function getFileNamePattern($modelName)
    {
        if(!in_array($modelName, array_keys(Mapper::NAME_TO_IO_MODEL_MAP()))){
            throw new \InvalidArgumentException("'$modelName' is not a valid model name.");
        };

        return $this->{$modelName.'_pattern'};
    }

    /**
     * @return School Name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return School Code
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Get whether or not we want to pre-process a school's files
     */
    public function getIsPreProcessingEnabled()
    {
        return $this->enable_preprocessing;
    }

    /**
     * Get the relative path to the import directory
     * @return string
     */
    public function getImportPath()
    {
        return $this->import_path;
    }

    /**
     * Get the absolute path to the import directory where school data files are stored.
     * @return string
     */
    public function getFullImportPath()
    {
        return getenv('IMPORT_DIR').$this->getImportPath();
    }

    /**
     * Get the relative path to the export directory
     * @return string
     */
    public function getExportPath()
    {
        return $this->export_path;
    }

    /**
     * Get absolute path to the export directory where files are dropped for sidewalk
     * @return string
     */
    public function getFullExportPath()
    {
        return getenv('EXPORT_DIR') . $this->getExportPath();
    }

    /**
     * create absolute path for given file in the export directory
     * @param $filename
     * @return string
     */
    public function getFullExportFilePath($filename)
    {
        return $this->getFullExportPath()."/$filename.".$this->getFileExtension();
    }

    /**
     * Get relative path for each schools file backup dir
     * @return string
     */
    public function getBackupPath()
    {
        return $this->backup_path;
    }

    public function getFileType()
    {
        return $this->file_type;
    }

    public function getFileExtension()
    {
        return $this->file_extension;
    }

    public function getSingleDivisionNameOrDefault($defaultName)
    {
        return $this->single_division_name ? $this->single_division_name : $defaultName;
    }

    /**
     * @param string $name
     * @param string $mode
     * @return \DataStaging\Contracts\ImportableAndExportableFile
     */
    public function getFileInstanceFor($name, $mode = 'w')
    {
        $exportFileClass = Mapper::FILE_TYPE_TO_MODEL_MAP()[$this->getFileType()];

        $path = $this->getFullExportFilePath($name);

        return new $exportFileClass($path, $mode);
    }

	public function courses()
	{
		return $this->hasMany( $this->getModel('Course'));
	}

    public function sections()
    {
        return $this->hasMany( $this->getModel('Section'));
    }

    public function customers()
    {
        return $this->hasMany( $this->getModel('Customer'));
    }

    public function enrollments()
    {
        return $this->hasMany( $this->getModel('Enrollment'));
    }

    public function divisions()
	{
		return $this->hasMany($this->getModel('Division'));
	}

    /**
     * Get the related tbb_school_data data as TbbSchool Object
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function tbbSettings()
    {
        return $this->hasOne($this->getModel('TbbSchool'));
    }

    public function processLogEntries()
    {
        return $this->hasMany(ProcessLog::class, 'school_code', 'code');
    }

    public function validationErrors()
    {
        return $this->hasMany(ValidationErrorView::class, 'school_code', 'code');
    }

    /**
     * Use division relationship to get all divisions using our portal & enabled
     * @return mixed
     */
    public function tbbUsingPortalDivisions()
    {
        return $this->divisions()->enabled()->useButler()->get();
    }

    /**
     * Create array of schools and school names to be used in html dropdowns
     * @param null $elementToPrepend
     * @return array
     */
    public static function buildDropdownOptions( $elementToPrepend = null )
	{
		$schoolNames = is_null($elementToPrepend) ? [] : [$elementToPrepend];

		foreach (static::enabled()->get(['id', 'name']) as $school) {
			$schoolNames[ $school['id'] ] = $school['name'];
		}

		return $schoolNames;
	}

    public function scopeEnabled($query)
    {
        return $query->where('enabled', true);
    }
}