<?php namespace DataStaging;

use Carbon\Carbon;

class Util
{
    /**
     * Transform a collection of objects to an array of strings
     * (which contain all info about the original object properties)
     * @param array $collection
     * @return array
     */
    public static function stringifyCollection(array $collection)
    {
        return array_map(function($item)
        {
            $prettified = '';

            // assumes we have a collection of objects
            foreach(get_object_vars($item) as $key => $value)
            {
                $prettified .= "$key => $value, ";
            }

            return rtrim($prettified, ',');

        }, $collection);
    }

    /**
     * trim and utf8 encode a string
     * @param $string
     * @return string
     */
    public static function sanitize($string)
    {
        return utf8_encode(trim($string));
    }

    /**
     * returns time based on current year/month/day/hour,
     * so as long as different instances of this handler are kicked off at the same time,
     * they will all have the same process token.
     *
     * @param Carbon $time
     * @return int
     */
    public static function createProcessToken(Carbon $time = null)
    {
        if($time == null){
            $time = Carbon::now();
        }

        return (int) $time->format('YmdH'); // "2015092813"
    }
}