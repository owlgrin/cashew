<?php namespace Owlgrin\Cashew;

use Illuminate\Support\ServiceProvider;
use Config;
use Stripe;

class CashewServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bind('Owlgrin\Cashew\Gateway\Gateway', function()
		{
			// setting up the api key here so that user can simply start using Cashew
		    Stripe::setApiKey(Config::get('cashew::keys.secret'));
		    return new \Owlgrin\Cashew\Gateway\StripeGateway;
		});

		// Binding DB implementation be default, users may switch it out
		// with their own implementation, if they wish to.
		$this->app->bind('Owlgrin\Cashew\Storage\Storage', 'Owlgrin\Cashew\Storage\DbStorage');

		// binding the command to generate the tables
		$this->app->bindShared('command.cashew.table', function($app)
		{
			return new \Owlgrin\Cashew\Commands\CashewTableCommand;
		});

		// binding the command to allow user to expire customers manually
		$this->app->bindShared('command.cashew.expire', function($app)
		{
			return new \Owlgrin\Cashew\Commands\CashewExpireCommand;
		});

		//	telling laravel what we are providing to the app using the package
		$this->commands('command.cashew.table');
		$this->commands('command.cashew.expire');
		
		// we will bind as singleton as we want just one instance of the package
		// throughout the processing of whole request
		$this->app->singleton('cashew', 'Owlgrin\Cashew\Cashew');
	}

	public function boot()
	{
		$this->package('owlgrin/cashew');
	}
}
