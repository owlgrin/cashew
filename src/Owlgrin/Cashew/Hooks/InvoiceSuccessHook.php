<?php namespace Owlgrin\Cashew\Hooks;

use Illuminate\Support\Facades\Event as IlluminateEvent;
use Owlgrin\Cashew\Invoice\StorableInvoice;
use Owlgrin\Cashew\Storage\Storage;
use Owlgrin\Cashew\Hooks\Hook;
use Owlgrin\Cashew\Event\Event;
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
		$invoice = $event->invoice();
		$subscription = $this->storage->subscription($event->customer(), true);

		Cashew::user($subscription['user_id']);

		if($invoice instanceof StorableInvoice and $invoice->total() > 0.00)
		{
			$invoice->store($subscription['user_id']); // store invoice
		}

		if(Cashew::hasCard())
		{
			$this->storage->updateStatus($subscription['user_id'], 'active'); // make subscription active
		}

		if($this->shouldBeExpired($invoice, $subscription))
		{
			Cashew::expireCustomer($event->customer());

			IlluminateEvent::fire('cashew.user.expire', array($subscription['user_id']));
		}


		IlluminateEvent::fire('cashew.payment.success', array($subscription['user_id'], $invoice));
	}

	private function shouldBeExpired($invoice, $subscription)
	{
		return (! Cashew::hasCard() and $invoice->total() == 0.00 and $this->isTrialOver($subscription));
	}

	private function isTrialOver($subscription)
	{
		if(is_null($subscription['trial_ends_at'])) return true;

		return Carbon::createFromFormat('Y-m-d H:i:s', $subscription['trial_ends_at'])->startOfDay()->lt(Carbon::today());
	}
}