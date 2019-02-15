<?php namespace DataStaging\Contracts;

use DataStaging\Contracts;

interface ImportableAndExportableFile
{

    public function columnCount();

    public function encoding();

    /**
     * @return Contracts\ImportableAndExportableModel|null
     */
    public function getAssociatedModel();

    public function path();

    public function putRow(array $row);

    /**
     * filter and/or transform each row, return an array
     *   of rows returned from the closure. If you return falsy, the
     *   row will be skipped and NOT included in the results
     * @param \Closure $callback
     * @return array
     */
    public function map(\Closure $callback);

    public function validEncodings();

    public function timeLastModified();
}