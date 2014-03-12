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
		return $this->subscription['id'];
	}

	public function plan()
	{
		return $this->subscription['plan'] ? $this->subscription['plan']['id'] : null;
	}

	public function status()
	{
		return $this->subscription['status'];
	}

	public function trialEnd($formatted = true)
	{
		return $formatted
			? Carbon::createFromTimestamp($this->subscription['trial_end'])->toDateString()
			: $this->subcription['trial_end'];
	}

	public function currentEnd($formatted = true)
	{
		return $formatted
			? Carbon::createFromTimestamp($this->subscription['current_period_end'])->toDateString()
			: $this->subcription['current_period_end'];
	}
}