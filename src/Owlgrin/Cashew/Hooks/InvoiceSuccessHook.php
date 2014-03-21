<?php namespace Owlgrin\Cashew\Hooks;

use Illuminate\Support\Facades\Event as IlluminateEvent;
use Owlgrin\Cashew\Invoice\StorableInvoice;
use Owlgrin\Cashew\Storage\Storage;
use Owlgrin\Cashew\Hooks\Hook;
use Owlgrin\Cashew\Event\Event;
use DB;

class InvoiceSuccessHook implements Hook {

	protected $storage;

	public function __construct(Storage $storage)
	{
		$this->storage = $storage;
	}

	public function handle(Event $event)
	{
		$invoice = $event->invoice();
		$subscription = $this->storage->subscription($event->customer(), true);

		if($invoice instanceof StorableInvoice) $invoice->store($subscription['user_id']);

		IlluminateEvent::fire('cashew.payment.success', array($subscription['user_id'], $invoice));
	}
}