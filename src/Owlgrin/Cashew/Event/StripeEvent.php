<?php namespace Owlgrin\Cashew\Event;

use Owlgrin\Cashew\Event\Event;
use Owlgrin\Cashew\Invoice\StripeInvoice;
use Owlgrin\Cashew\Subscription\StripeSubscription;

class StripeEvent implements Event {
	
	protected $event;

	public function __construct($event)
	{
		$this->event = $event;
	}

	public function get()
	{
		return $this->event;
	}

	public function type()
	{
		return $this->event['type'];
	}

	public function customer()
	{
		return $this->event['data']['object']['customer'] ?: null;
	}

	public function invoice()
	{
		if( ! starts_with($this->type(), 'invoice.')) throw new \Exception('Uncompatible type');

		return new StripeInvoice($this->event['data']['object']);
	}

	public function subscription()
	{
		return new StripeSubscription($this->event['data']['object']);
	}

	public function attempts()
	{
		return $this->event['data']['object']['attempt_count'] ?: null;
	}

	public function failedMoreThan($count)
	{
		return $this->attempts() > $count;
	}
}