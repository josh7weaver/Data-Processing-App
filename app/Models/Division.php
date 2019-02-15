<?php namespace DataStaging\Models;

use DataStaging\Traits\ModelGetter;
use DataStaging\Traits\EloquentScopes;
use Validator;

class Division extends \Eloquent {

	use EloquentScopes;
	use ModelGetter;

	protected $guarded = [];
    public $timestamps = false;

	protected $rules = [
		'name'                  => 'required',
		'school_id'           => 'required',
		'enrollment_percentage' => 'required'
	];

	public $errors;


	public function school()
	{
		return $this->belongsTo( $this->getModel('School') );
	}

	public function customers()
	{
		return $this->hasManyThrough( $this->getModel('Customer'), $this->getModel('School') );
	}

    public function sections()
    {
        return $this->hasMany($this->getModel('Section'), 'campus', 'name');
    }

    public function scopeHasPercentage($query)
    {
        return $query->whereNotNull('enrollment_percentage');
    }

    public function scopeUseButler($query)
	{
		return $query->where('use_butler', true);
	}

    public function getName()
    {
        return $this->name;
    }

    public function getEnrollmentPercentage()
    {
        return $this->enrollment_percentage;
    }

    public function getPrettyEnrollmentPercentage()
    {
        return $this->enrollment_percentage * 100;
    }

    public function getEnrollmentAdjustmentEnabled()
    {
        return $this->enrollment_adjustment_enabled;
    }

    public function isValid()
	{
		$validation = Validator::make( $this->attributes, $this->rules);

        if ($validation->fails()){
            $this->errors = $validation->messages();
            return false;
        } else {
            return true;
        }
	}

    /**
     * @return array
     */
    public function validDivisionNames()
    {
        return $this->lists('name');
    }

    public function shouldAdjustEnrollmentByPercent()
    {
        return $this->isEnrollmentPercentageSet() && $this->getEnrollmentAdjustmentEnabled();
    }

    protected function isEnrollmentPercentageSet()
    {
        return !is_null($this->enrollment_percentage);
    }

    public function convertEnrollmentPercentageToDouble()
    {
        $this->enrollment_percentage = ($this->enrollment_percentage / 100);
        return $this;
    }
}