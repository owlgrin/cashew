<?php namespace Owlgrin\Cashew\Hooks;

use Owlgrin\Cashew\Hooks\Hook;
use Owlgrin\Cashew\Events\Event;
use Owlgrin\Cashew\Cashew;

class InvoiceFailHook implements Hook {

	const FAIL_COUNT = 3;

	public function handle(Event $event)
	{
		if($event->failedMoreThan(self::FAIL_COUNT))
		{
			Cashew::expireCustomer($event->customer());
		}
	}
}