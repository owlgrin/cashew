<?php namespace Owlgrin\Cashew\Hooks;

use Illuminate\Support\Facades\Event as IlluminateEvent;
use Owlgrin\Cashew\Storage\Storage;
use Owlgrin\Cashew\Hooks\Hook;
use Owlgrin\Cashew\Event\Event;

class InvoiceSuccessHook implements Hook {

	protected $storage;

	public function __construct(Storage $storage)
	{
		$this->storage = $storage;
	}

	public function handle(Event $event)
	{
		$subscription = $this->storage->subscription($event->customer(), true);
		IlluminateEvent::fire('cashew.payment.success', array(array('user' => $subscription['user_id'], 'invoice' => $event->invoice())));
	}
}