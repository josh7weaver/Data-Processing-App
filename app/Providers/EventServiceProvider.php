<?php namespace DataStaging\Providers;

use Config;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider {

	/**
	 * The event handler mappings for the application.
	 *
	 * @var array
	 */
	protected $listen;

    protected function defaultListeners()
    {
        return [
            'illuminate.log' => [
                'DataStaging\Handlers\Events\LogEventHandler',
            ],
        ];
    }

    protected function mergeHandlers()
    {
        $enabled = [];

        if( Config::get('app.log_sql') === true )
        {
            $enabled['illuminate.query'] = [
                    'DataStaging\Handlers\Events\DbEventHandler',
                ];
        }

//        dd(array_merge($this->defaultListeners(), $enabled));
        return array_merge($this->defaultListeners(), $enabled);
    }

	/**
	 * Register any other events for your application.
	 *
	 * @param  \Illuminate\Contracts\Events\Dispatcher  $events
	 * @return void
	 */
	public function boot(DispatcherContract $events)
	{
        $this->listen = $this->mergeHandlers();

		parent::boot($events);

		//
	}

}