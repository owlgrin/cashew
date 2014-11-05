<?php namespace Owlgrin\Cashew\Hooks;

use Illuminate\Support\Facades\Event as IlluminateEvent;
use Owlgrin\Cashew\Event\Event;

/**
 * Hook to handle created inovice.
 */
class InvoiceCreateHook implements Hook {

	/**
	 * Handles the event
	 * @param  Event  $event
	 * @return void
	 */
	public function handle(Event $event)
	{
		IlluminateEvent::fire('cashew.invoice.created', array($event->invoice()));
	}
}