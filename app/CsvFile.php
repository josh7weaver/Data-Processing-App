<?php namespace DataStaging;

use Carbon\Carbon;
use DataStaging\Contracts\ImportableAndExportableFile;
use SplFileObject;

class CsvFile implements ImportableAndExportableFile
{

	protected $file;
    protected $mode;
    protected $path;
    protected $columnCount;
    protected $encoding;
    protected $validEncodings = ['utf-8', 'us-ascii'];

    /**
     * @var \DataStaging\Contracts\ImportableAndExportableModel|null
     */
    protected $associatedModel;

    /**
     * @var \DataStaging\Models\School|null
     */
    protected $school;

    /**
     * __construct sets the class attributes - pay attention to 'mode',
     *    if you try to write without spcifying 'w', write will fail.
     * @param        $path
     * @param string $mode
     * @param array  $args
     */
	public function __construct($path, $mode = 'r', array $args = [])
	{
		$this->path = $path;
        $this->mode = $mode;
        $this->file = $this->open($this->path, $this->mode);

        $this->validEncodings = isset($args['validEncodings']) ? $args['validEncodings'] : $this->validEncodings;
        $this->associatedModel = isset($args['model']) ? $args['model'] : null;
        $this->school = isset($args['school']) ? $args['school'] : null;

        if($this->isModeSetToRead())
        {
            $this->columnCount = $this->countColumns();
            $this->encoding = $this->detectEncoding();
        }
	}

    public function encoding()
    {
        return $this->encoding;
    }

    /**
     * @return Contracts\ImportableAndExportableModel|null
     */
    public function getAssociatedModel()
    {
        return $this->associatedModel;
    }

    public function validEncodings()
    {
        return $this->validEncodings;
    }

    public function columnCount()
    {
        return $this->columnCount;
    }

    public function file()
    {
        return $this->file;
    }

    public function getRow()
    {
        return $this->file->fgetcsv();;
    }

    public function mode()
    {
        return $this->mode;
    }

    public function name()
    {
        return $this->file->getFilename();
    }

    public function path()
    {
        return $this->path;
    }

    public function putRow(array $row )
    {
        $this->checkStreamIsWritable();

        $result = $this->file->fputcsv($row);

        if ($result === FALSE) {
            throw new \ErrorException("The row was not written to the file: ". $row, 1);
        }

        return $result;
    }

    public function putRows(array $rows)
    {
        foreach ($rows as $row) {
            $this->putRow( $row );
        }
    }

    /**
     * Transform and/or filter each row. Builds up an array of whatever is
     *  returned from the callback for each row and returns that array.
     * @param \Closure $callback
     * @return array
     */
    public function map(\Closure $callback)
    {
        $this->file->rewind();

        $rows = [];
        while( !$this->file->eof() )
        {
            $currentRow = $this->getRow();
            if($this->isRowEmpty($currentRow)) continue; // skip empty rows

            $result = call_user_func($callback, $currentRow);

            if(isset($result) && $result !== false){
                $rows[] = $result;
            }
        }

        $this->file->rewind();
        return $rows;
    }

    public function toArray()
    {
        return $this->map(function($row)
        {
            return $row;
        });
    }

    public function timeLastModified()
    {
        $unixTimestamp = filemtime($this->path());

        return Carbon::createFromTimestamp($unixTimestamp);
    }

// PROTECTED
    protected function checkStreamIsWritable()
    {
        if ( $this->isModeSetToRead() ) {
            throw new \ErrorException("You can't write to this file because the mode is not a writable mode: " . $this->mode(), 1);
        }
    }

    protected function countColumns()
    {
        $this->file->rewind();
        $count = count($this->file->fgetcsv());
        $this->file->rewind();

        return $count;
    }

    protected function detectEncoding()
    {
        return trim( shell_exec('file --mime-encoding -L ' . escapeshellarg($this->path) . ' | cut -d: -f2') );
    }

    protected function isModeSetToRead()
    {
        return $this->mode() == 'r';
    }

    protected function isRowEmpty($row)
    {
        return $row === [null] || empty($row);
    }

    protected function open()
    {
        return new SplFileObject($this->path, $this->mode);
    }
}