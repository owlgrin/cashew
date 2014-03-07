<?php namespace Owlgrin\Cashew;

use Illuminate\Support\Facades\Facade;

class CashewFacade extends Facade
{
	protected static function getFacadeAccessor()
	{
		return 'cashew';
	}
}