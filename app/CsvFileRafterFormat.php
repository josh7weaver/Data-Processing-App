<?php namespace DataStaging;

class CsvFileRafterFormat extends CsvFile
{
    /**
     * Transform and/or filter each row. Builds up an array of whatever is
     *  returned from the callback for each row and returns that array.
     * @param \Closure $callback
     * @return array
     */
    public function map(\Closure $callback)
    {
        $this->file->rewind();
        $this->getRow(); // drop header

        $rows = [];
        while( !$this->file->eof() )
        {
            $currentRow = $this->getRow();
            if($this->isRowEmpty($currentRow)) continue; // skip empty rows
            
            $currentRow = $this->getAssociatedModel()->coerceRafterToStandardFormat($currentRow, $this->school);

            $result = call_user_func($callback, $currentRow);

            if(isset($result) && $result !== false){
                $rows[] = $result;
            }
        }

        $this->file->rewind();
        return $rows;
    }
}