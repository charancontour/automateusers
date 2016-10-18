<?php namespace Automation\Users;

use Illuminate\Support\ServiceProvider;

class UserAutomationProvider extends ServiceProvider {

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->loadViewsFrom(__DIR__.'/views', 'automation');
		 $this->publishes([
        	__DIR__.'/views' => base_path('resources/views/automate/'),
    	]);
	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
		include __DIR__.'/routes.php';
        $this->app->make('Automation\Users\UserAutomationController');
	}

}
