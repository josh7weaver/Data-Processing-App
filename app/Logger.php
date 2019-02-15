<?php namespace DataStaging;

use DataStaging\Logging\PDOHandler;
use DataStaging\Logging\ExtendedDbLoggingProcessor;
use DataStaging\Mailers\BufferedLoggingMailer;
use DataStaging\Models\School;
use DB;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\BufferHandler;
use Monolog\Handler\SwiftMailerHandler;
use Monolog\Formatter\HtmlFormatter;
use Monolog\Logger as Monolog;
use Log;

class Logger{

	public $log;
	public $mailer;

	public function __construct()
	{
		$this->log = Log::getMonolog();
	}

	public function addDbHandler(School $school, $level = Monolog::NOTICE, $bubble = true)
	{
		$pdoHandler = new PDOHandler(DB::getPdo(), $level, $bubble);
		$pdoHandler->setSchool($school);

		$this->log->pushHandler($pdoHandler);
		return $this;
	}

	public function addExtendedDbProcessor()
	{
		$this->log->pushProcessor(new ExtendedDbLoggingProcessor);
		return $this;
	}

	public function addBufferedMailHandler(BufferedLoggingMailer $mailer, $level = Monolog::WARNING)
	{
		$mailHandler = new SwiftMailerHandler( $mailer->getSwiftMailer() , $mailer->getMessage(), $level);
		$mailHandler->setFormatter( new HtmlFormatter() );

		$this->log->pushHandler( new BufferHandler($mailHandler, 0, $level) );
		return $this;
	}

    public function addFileHandler($filename, $bubble = true)
    {
        $this->log->pushHandler( new StreamHandler( storage_path() . "/logs/$filename.log", Monolog::DEBUG, $bubble ) );
        return $this;
    }

	public function addTimestampFileHandler($bubble = false)
	{
		// timestamp file, don't bubble
		$this->log->pushHandler( new StreamHandler( storage_path() . '/logs/datastager_'.date("Y-m-d_H:i:s").'.log', Monolog::DEBUG, $bubble ) );
		return $this;
	}

	public function resetHandlers()
	{
		// exception is thrown if you pop an empty handler stack
		while ( count( $this->log->getHandlers() ) > 1 ) {
			$this->log->popHandler();
		}

		return $this;
	}
}