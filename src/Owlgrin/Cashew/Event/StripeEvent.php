<?php namespace Owlgrin\Cashew\Event;

use Owlgrin\Cashew\Event\Event;
use Owlgrin\Cashew\Invoice\StripeInvoice;
use Owlgrin\Cashew\Subscription\StripeSubscription;

/**
 * The Stripe implementation of the events
 */
class StripeEvent implements Event {

	/**
	 * The raw event object
	 * @var array|object
	 */
	protected $event;

	public function __construct($event)
	{
		$this->event = $event;
	}

	/**
	 * Returns the raw event object
	 * @return array|object
	 */
	public function get()
	{
		return $this->event;
	}

	/**
	 * Returns the type of event
	 * @return string
	 */
	public function type()
	{
		return $this->event['type'];
	}

	/**
	 * Returns the customer for the event
	 * @return string|null
	 */
	public function customer()
	{
		return $this->event['data']['object']['customer'] ?: null;
	}

	/**
	 * Returns the invoice for the event if it is for an invoice
	 * @return Invoice
	 */
	public function invoice()
	{
		if( ! starts_with($this->type(), 'invoice.')) throw new \Exception('Uncompatible type');

		return new StripeInvoice($this->event['data']['object']);
	}

	/**
	 * Returns the subscription for the event
	 * @return Subscription
	 */
	public function subscription()
	{
		return new StripeSubscription($this->event['data']['object']);
	}

	/**
	 * Returns the number of attempts the event has already made
	 * @return integer
	 */
	public function attempts()
	{
		return $this->event['data']['object']['attempt_count'] ?: null;
	}

	/**
	 * Determines if the event has failed more than a given number of times
	 * @param  integer $count
	 * @return bool
	 */
	public function failedMoreThan($count)
	{
		return $this->attempts() > $count;
	}
}