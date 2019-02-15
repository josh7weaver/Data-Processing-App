<?php namespace DataStaging\Models;

use Illuminate\Database\Eloquent\Model;

class ProcessLock extends Model
{
    protected $guarded = [];
    public $timestamps = false;
    public $table = 'process_lock';
    public $primaryKey = 'school_id';
}