<?php namespace DataStaging\Contracts;

use DataStaging\Models\School;

interface Adjuster
{
    /**
     * RemoveOptedOutEnrollmentsAdjuster constructor.
     * @param School $school
     */
    public function __construct(School $school);

    /**
     * Perform the necessary adjustments for the given adjuster
     * @return mixed
     */
    public function adjust();
}