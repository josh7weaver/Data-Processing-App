<?php namespace DataStaging\Models;

use Illuminate\Database\Eloquent\Model;

class ViewAllEnrollment extends Model {

    /*
     * This is a model to give acess to the count_all_enrollments VIEW in
     * the database
     */

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'count_all_enrollments';

}
