<?php namespace Owlgrin\Cashew\Events;

use Owlgrin\Cashew\Events\Event;

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

	public function customer()
	{
		return $this->event['data']['object']['customer'] ?: null;
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