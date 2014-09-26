<?php namespace Owlgrin\Cashew;

use Illuminate\Support\Facades\Facade;

/**
 * The Cashew Facade
 */
class CashewFacade extends Facade
{
	/**
	 * Returns the binding in IoC container
	 * @return string
	 */
	protected static function getFacadeAccessor()
	{
		return 'cashew';
	}
}