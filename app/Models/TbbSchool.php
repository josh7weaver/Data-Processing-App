<?php namespace DataStaging\Models;

use DataStaging\Contracts\CanBeExported;
use DataStaging\Mapper;
use DataStaging\Traits\ModelGetter;
use DataStaging\Traits\EloquentScopes;
use Illuminate\Database\Eloquent\Model;
use Log;

class TbbSchool extends Model
{
    use ModelGetter;

    protected $guarded = [];
    public $timestamps = false;
    public $table = 'tbb_school_data';
    public $primaryKey = 'school_id';

    public function getDefaultPref()
    {
        return $this->default_pref;
    }

    public function scopeUsesPortal($query)
    {
        return $query->where('use_butler', true);
    }

    public function schoolSettings()
    {
        return $this->hasOne($this->getModel('School'), 'id', 'school_id');
    }

    public function customers()
    {
        return $this->hasMany($this->getModel('Customer'), 'school_id', 'school_id');
    }
}