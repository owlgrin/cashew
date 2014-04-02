<?php namespace Owlgrin\Cashew\Hooks;

use Illuminate\Support\Facades\Event as IlluminateEvent;
use Owlgrin\Cashew\Invoice\StorableInvoice;
use Owlgrin\Cashew\Storage\Storage;
use Owlgrin\Cashew\Hooks\Hook;
use Owlgrin\Cashew\Event\Event;
use Owlgrin\Cashew\Cashew;

class InvoiceSuccessHook implements Hook {

	protected $storage;
	protected $cashew;

	public function __construct(Storage $storage, Cashew $cashew)
	{
		$this->storage = $storage;
		$this->cashew = $cashew;
	}

	public function handle(Event $event)
	{
		$invoice = $event->invoice();
		$subscription = $this->storage->subscription($event->customer(), true);

		if($invoice->total() > 0.00) // only when total is greater than 0
		{
			if($invoice instanceof StorableInvoice)
			{
				$invoice->store($subscription['user_id']); // store invoice
			}

			if($this->cashew->user($subscription['user_id'])->hasCard() and $invoice->total() > 0.00)
			{
				$this->storage->updateStatus($subscription['user_id'], 'active'); // make subscription active
			}
		}


		IlluminateEvent::fire('cashew.payment.success', array($subscription['user_id'], $invoice));
	}
}