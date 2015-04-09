<?php namespace Owlgrin\Cashew\Hooks;

use Illuminate\Support\Facades\Event as IlluminateEvent;
use Owlgrin\Cashew\Storage\Storage;
use Owlgrin\Cashew\Hooks\Hook;
use Owlgrin\Cashew\Event\Event;
use Owlgrin\Cashew\CashewFacade as Cashew;
use Config;

/**
 * Hook to handle failed payment
 */
class InvoiceFailHook implements Hook {

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
	 * Handles the webhook call
	 * @param  Event  $event
	 * @return void
	 */
	public function handle(Event $event)
	{
		$subscription = $this->storage->subscription($event->customer(), true);

		Cashew::user($subscription['user_id']);

		if($event->failedMoreThan(Config::get('cashew::attempts')))
		{
			Cashew::expireCustomer($event->customer());

			IlluminateEvent::fire('cashew.user.expire', array($subscription['user_id']));
		}
		else
		{
			IlluminateEvent::fire('cashew.payment.fail', array($subscription['user_id'], $event->invoice()));
		}
	}
}