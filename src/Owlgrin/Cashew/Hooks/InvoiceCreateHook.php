<?php namespace Owlgrin\Cashew\Hooks;

use Illuminate\Support\Facades\Event as IlluminateEvent;
use Owlgrin\Cashew\Storage\Storage;
use Owlgrin\Cashew\Event\Event;
use Carbon;
/**
 * Hook to handle created inovice.
 */
class InvoiceCreateHook implements Hook {

	/**
	 * Instance of storage implementation
	 * @var Storage
	 */
	protected $storage;

	public function __construct(Storage $storage)
	{
		$this->storage = $storage;
	}

	/**
	 * Handles the event
	 * @param  Event  $event
	 * @return void
	 */
	public function handle(Event $event)
	{
		$subscription = $this->storage->subscription($event->customer(), true);

		// To avoid the case when invoice created at the time of canceling subscription.
		if($subscription['status'] == 'expired') return;

		// we are not doing anything special here,
		// just firing the event to be handeled by the app.
		IlluminateEvent::fire('cashew.invoice.created', array($subscription['user_id'], $event->invoice()));
	}
}