<?php namespace Owlgrin\Cashew\Hooks;

use Illuminate\Support\Facades\Event as IlluminateEvent;
use Owlgrin\Cashew\Invoice\StorableInvoice;
use Owlgrin\Cashew\Storage\Storage;
use Owlgrin\Cashew\Hooks\Hook;
use Owlgrin\Cashew\Event\Event;
use Owlgrin\Cashew\Gateway\Gateway;
use Owlgrin\Cashew\CashewFacade as Cashew;

use Carbon\Carbon;

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
	 * Instance of gateway implementation
	 * @var Gateway
	 */
	protected $gateway;

	public function __construct(Storage $storage, Gateway $gateway)
	{
		$this->storage = $storage;
		$this->gateway = $gateway;
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

		Cashew::user($subscription['user_id']);

		if($invoice instanceof StorableInvoice and $invoice->total() > 0.00)
		{
			$invoice->store($subscription['user_id']); // store invoice
		}

		$this->storage->update($subscription['user_id'], $this->gateway->customer($event->customer()));

		IlluminateEvent::fire('cashew.payment.success', array($subscription['user_id'], $invoice));
	}
}