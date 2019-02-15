<?php namespace DataStaging\Contracts;

interface CanBeExported
{
    /**
     * This returns the Class name for the file type to be used
     * for exporting the given school to a file
     * @param        $name
     * @param string $mode
     * @return mixed
     */
    public function getFileInstanceFor($name, $mode = 'w');

    public function getFullExportFilePath($filename);

    public function getFullExportPath();

    /**
     * This is the primary key
     * @return mixed
     */
    public function getKey();

}