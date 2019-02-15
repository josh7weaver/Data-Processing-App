<?php namespace DataStaging\Contracts;

interface HasDivisionName
{
    /**
     * @return string|null  The division Name
     */
    public function getDivision();
}