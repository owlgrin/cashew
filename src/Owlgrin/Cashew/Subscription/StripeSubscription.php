<?php namespace Owlgrin\Cashew\Subscription;

use Owlgrin\Cashew\Subscription\Subscription;
use Carbon\Carbon;

class StripeSubscription implements Subscription {

	protected $subscription;

	public function __construct($subscription)
	{
		$this->subscription = $subscription;
	}

	public function get()
	{
		return $this->subscription;
	}

	public function id()
	{
		return $this->subscription ? $this->subscription['id'] : null;
	}

	public function plan()
	{
		return $this->subscription ? $this->subscription['plan']['id'] : null;
	}

	public function quantity()
	{
		return $this->subscription ? $this->subscription['quantity'] : null;
	}

	public function status()
	{
		return $this->subscription ? $this->subscription['status'] : null;
	}

	public function trialEnd($formatted = true)
	{
		if( ! $this->subscription) return null;
		if(is_null($this->subscription['trial_end'])) return null;

		return $formatted
			? Carbon::createFromTimestamp($this->subscription['trial_end'])->toDateString()
			: $this->subcription['trial_end'];
	}

	public function currentEnd($formatted = true)
	{
		if( ! $this->subscription) return null;
		if(is_null($this->subscription['current_period_end'])) return null;

		return $formatted
			? Carbon::createFromTimestamp($this->subscription['current_period_end'])->toDateString()
			: $this->subcription['current_period_end'];
	}

	public function end($formatted = true)
	{
		if( ! $this->subscription) return null;
		return (! is_null($this->subscription['trial_end']) and $this->subscription['trial_end'] > time())
			? $this->trialEnd($formatted)
			: $this->currentEnd($formatted);
	}
}