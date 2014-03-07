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
		    Stripe::setApiKey(Config::get('cashew::keys.secret'));
		    return new \Owlgrin\Cashew\Gateway\StripeGateway;
		});

		$this->app->bind('Owlgrin\Cashew\Storage\Storage', 'Owlgrin\Cashew\Storage\DbStorage');
		
		$this->app->singleton('cashew', 'Owlgrin\Cashew\Cashew');
	}

	public function boot()
	{
		$this->package('owlgrin/cashew');
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
