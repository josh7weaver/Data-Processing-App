<?php namespace DataStaging\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ValidationData extends Model
{
    protected $guarded = [];
    public $timestamps = false;
    public $table = 'validation_data';

    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * @return file | row
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param $code
     * @return Model|static
     * @throws ModelNotFoundException
     */
    public function findByCode($code)
    {
        return $this->newQuery()
                ->where('code', $code)
                ->firstOrFail();
    }
}