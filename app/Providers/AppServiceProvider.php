<?php namespace DataStaging\Providers;

use Illuminate\Auth\Guard;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot(Guard $auth)
	{
		view()->composer('partials.nav', function($view) use ($auth){
            $view->with('currentUser', $auth->user());
        });

		\Dotenv::required([
			'CAMPUS',
			'TERM_CODE',
			'DEPARTMENT_CODE',
			'COURSE_CODE',
			'SECTION_CODE',
		]);
	}

	/**
	 * Register any application services.
	 *
	 * This service provider is a great spot to register your various container
	 * bindings with the application. As you can see, we are registering our
	 * "Registrar" implementation here. You can add your own bindings too!
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bind(
			'Illuminate\Contracts\Auth\Registrar',
			'DataStaging\Services\Registrar'
		);

//		Was being used in the RowValidator, but not currently.
//		$this->app->instance('store.models', collect());
	}

}
