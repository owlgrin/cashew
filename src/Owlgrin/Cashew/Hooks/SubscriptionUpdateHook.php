<?php namespace Owlgrin\Cashew\Hooks;

use Illuminate\Support\Facades\Event as IlluminateEvent;
use Owlgrin\Cashew\Storage\Storage;
use Owlgrin\Cashew\Hooks\Hook;
use Owlgrin\Cashew\Event\Event;

class SubscriptionUpdateHook implements Hook {

	protected $storage;

	public function __construct(Storage $storage)
	{
		$this->storage = $storage;
	}

	public function handle(Event $event)
	{
		$subscription = $this->storage->subscription($event->customer(), true);

		// if user turns from trialing to active, we will make the status as active
		if($subscription->status() == 'active')
		{
			$this->storage->updateStatus($subscription['user_id'], $subscription->status());
		}

		IlluminateEvent::fire('cashew.subscription.update', array($subscription['user_id'], $event->subscription()));
	}
}