<?php namespace DataStaging\Models;

use DataStaging\Contracts\CanBeExported;
use DataStaging\FileValidator;
use DataStaging\Mapper;
use DataStaging\RowValidator;
use DataStaging\Traits\ModelGetter;
use DataStaging\Traits\EloquentScopes;
use Illuminate\Database\Eloquent\Model;
use Log;

class ProcessLog extends Model
{
    protected $guarded = [];
    public $timestamps = false;
    public $table = 'process_log';
    protected $casts = [
        'context' => 'array'
    ];

    public function school()
    {
        return $this->hasOne(School::class, 'code', 'school_code');
    }

    public function validationData()
    {
        return $this->hasOne(ValidationData::class, 'code', 'validation_code');
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getContext()
    {
        return $this->context;
    }

    public function getFilePath()
    {
        if(isset($this->context['path']))
        {
            return $this->context['path'];
        }
    }

    /**
     * Could modify this to take an optional field, so you could retrieve say customer ID
     * based on the current file layout
     * @return mixed
     */
    public function getRow()
    {
        if(isset($this->context['currentRow']))
        {
            return $this->context['currentRow'];
        }
    }

    public function getFileType()
    {
        return $this->file_type;
    }

    public function getValidationCode()
    {
        return $this->validation_code;
    }

    public function scopeNonValidationErrors($query, $processToken, $schoolCode = null)
    {
        $query = $query->where('process_token', $processToken)
                ->where('level_code', '>=', 400)
                ->whereNull('validation_code');

        if($schoolCode){
            $query = $query->where('school_code', $schoolCode);
        }

        return $query;
    }
}