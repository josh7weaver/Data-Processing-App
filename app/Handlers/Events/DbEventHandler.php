<?php namespace DataStaging\Handlers\Events;

use Config;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldBeQueued;

class DbEventHandler {

	/**
	 * Create the event handler.
	 *
	 * @return void
	 */
	public function __construct()
	{
		//
	}

    /**
     * Handle the event.
     *
     * @param $sql
     * @param $bindings
     * @param $time
     */
	public function handle($query, $bindings, $time, $name)
	{

//        $data = compact('bindings', 'time', 'name');

        // Format binding data for sql insertion
        foreach ($bindings as $i => $binding)
        {
            if ($binding instanceof \DateTime)
            {
                $bindings[$i] = $binding->format('\'Y-m-d H:i:s\'');
            }
            else if (is_string($binding))
            {
                $bindings[$i] = "'$binding'";
            }
        }

        // Insert bindings into query
        $query = str_replace(array('%', '?'), array('%%', '%s'), $query);
        $query = vsprintf($query, $bindings);

        // LOG
        $logFile = storage_path('logs/query.log');

        $monolog = new Logger('log');

        $monolog->pushHandler(new StreamHandler($logFile), Logger::INFO);
        $monolog->info($query, compact('time'));
	}

}
