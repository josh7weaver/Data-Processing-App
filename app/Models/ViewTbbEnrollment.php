<?php namespace DataStaging\Models;

use Carbon\Carbon;
use DataStaging\Traits\ModelGetter;
use Illuminate\Database\Eloquent\Model;
use Schema;
use SplTempFileObject;

/*
 * This is a model to give acess to the count_tbb_enrollments VIEW in
 * the database
 */

class ViewTbbEnrollment extends Model
{
    use ModelGetter;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'count_tbb_enrollments';

    protected $filename;
    protected $headers;
    protected $csv;

    public function section()
    {
        return $this->hasOne( $this->getModel('Section'), 'id', 'section_id');
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getTableColumns()
    {
        return Schema::getColumnListing($this->getTable());
    }

    public function toCsvString($includeHeaders = true)
    {
        $this->initialize();

        if($includeHeaders) $this->csv->fputcsv($this->getTableColumns());

        $this->chunk(10000, function($tbbSections)
        {
            foreach ($tbbSections as $tbbSection)
            {
                // add the current row to the CSV file
                $this->csv->fputcsv( $tbbSection->toArray() );
            }
        });

        return $this->csvToString();
    }

    protected function csvToString()
    {
        $this->csv->rewind();

        return $this->getCsvContentsFromBuffer();
    }

    /**
     * Because the SplTempFileObject doesn't have a way to read the entire file
     * into a variable without looping, just start another buffer, dump the contents
     * to the buffer, then close buffer and return content as string
     * @return string
     */
    protected function getCsvContentsFromBuffer()
    {
        ob_start();
        $this->csv->fpassthru();
        return ob_get_clean();
    }

    protected function initialize()
    {
        $this->filename = Carbon::now()->toDateString() . '_count tbb enrollments.csv';
        $this->headers = [
            'content-type' => 'application/octet-stream',
            'content-transfer-encoding' => 'binary',
            'content-disposition' => "attachment; filename='{$this->filename}';",
        ];
        $this->csv = new SplTempFileObject();
    }

}
