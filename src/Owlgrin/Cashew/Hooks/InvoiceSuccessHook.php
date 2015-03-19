<?php namespace Owlgrin\Cashew\Hooks;

use Illuminate\Support\Facades\Event as IlluminateEvent;
use Owlgrin\Cashew\Invoice\StorableInvoice;
use Owlgrin\Cashew\Storage\Storage;
use Owlgrin\Cashew\Hooks\Hook;
use Owlgrin\Cashew\Event\Event;
use Owlgrin\Cashew\Cashew;

/**
 * Hook to handle successful payment
 */
class InvoiceSuccessHook implements Hook {

	/**
	 * Instance of storage implementation
	 * @var Storage
	 */
	protected $storage;

	/**
	 * Cahew instance
	 * @var Cashew
	 */
	protected $cashew;

	public function __construct(Storage $storage, Cashew $cashew)
	{
		$this->storage = $storage;
		$this->cashew = $cashew;
	}

	/**
	 * Handles the event
	 * @param  Event  $event
	 * @return void
	 */
	public function handle(Event $event)
	{
		$invoice = $event->invoice();
		$subscription = $this->storage->subscription($event->customer(), true);

		if($invoice instanceof StorableInvoice and $invoice->total() > 0.00)
		{
			$invoice->store($subscription['user_id']); // store invoice
		}

		if($this->cashew->user($subscription['user_id'])->hasCard())
		{
			$this->storage->updateStatus($subscription['user_id'], 'active'); // make subscription active
		}

		IlluminateEvent::fire('cashew.payment.success', array($subscription['user_id'], $invoice));
	}
}