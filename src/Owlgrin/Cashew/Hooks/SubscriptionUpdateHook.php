<?php namespace Owlgrin\Cashew\Hooks;

use Illuminate\Support\Facades\Event as IlluminateEvent;
use Owlgrin\Cashew\Storage\Storage;
use Owlgrin\Cashew\Hooks\Hook;
use Owlgrin\Cashew\Event\Event;

/**
 * Hook to handle updation to subscriptions
 */
class SubscriptionUpdateHook implements Hook {

	/**
	 * Instance of Storage implementation
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

		// if user turns from trialing to active, we will make the status as active
		// if($subscription['status'] == 'trialing' and $event->subscription()->status() == 'active')
		// {
		// 	$this->storage->updateStatus($subscription['user_id'], $event->subscription()->status());
		// }

		IlluminateEvent::fire('cashew.subscription.update', array($subscription['user_id'], $event->subscription()));
	}
}