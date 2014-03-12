<?php namespace Owlgrin\Cashew;

use Owlgrin\Cashew\Storage\Storage;
use Owlgrin\Cashew\Gateway\Gateway;
use Carbon\Carbon, Config;

class Cashew {

	const STATUS_TRIAL = 'trialing';
	const STATUS_CANCEL = 'canceled';
	const STATUS_EXPIRE = 'expired';
	const STATUS_ACTIVE = 'active';

	protected $gateway;
	protected $storage;
	protected $user = null;
	protected $subscription = null;


	public function __construct(Gateway $gateway, Storage $storage)
	{
		$this->gateway = $gateway;
		$this->storage = $storage;
	}

	public function user($user)
	{
		$this->user = $user;
		$this->subscription = $this->storage->subscription($this->user);
		return $this;
	}

	public function refreshSubscription()
	{
		$this->subscription = $this->storage->subscription($this->user);
	}

	public function getUser()
	{
		return $this->user;
	}

	public function getSubscription()
	{
		return $this->subscription;
	}

	public function create($id, $trialDays = null)
	{
		try
		{
			if($this->storage->subscription($id)) throw new \Exception('Customer already exist');

			$this->storage->create($id, $this->getTrialEnd($trialDays));

			$this->user($id); // for further usage

			return $this;
		}
		catch(\Exception $e)
		{
			throw new \Exception($e->getMessage());
		}
	}

	public function subscribe($card, $description = '', $options)
	{
		try
		{
			if( ! $this->subscription) throw new \Exception('No subscription found');
			if( ! $this->subscription['customer_id']) $this->createCustomer($card, $description);

			$options['trial_end'] = $this->getTrialEnd(isset($options['trial_end']) ? $options['trial_end'] : null);

			$customer = $this->gateway->update($this->subscription['customer_id'], $options);

			$this->storage->subscribe($this->user, $customer->subscription());
		}
		catch(\Exception $e)
		{
			throw new \Exception($e->getMessage());
		}
	}

	public function card($card)
	{
		return $this->update(compact('card'));
	}

	public function toPlan($plan, $prorate = true)
	{
		return $this->update(compact('plan', 'prorate'));
	}

	public function update($options = array())
	{
		try
		{
			if( ! $this->subscription) throw new \Exception('No subscription found');

			$customer = $this->gateway->update($this->subscription['customer_id'], $options);
			$this->storage->update($this->user, $customer);

			$this->refreshSubscription();

			return $this;
		}
		catch(\Exception $e)
		{
			throw new \Exception($e->getMessage());
		}
	}

	public function cancelAtPeriodEnd()
	{
		return $this->cancel(true);
	}

	public function cancel($atPeriodEnd = false)
	{
		try
		{
			if( ! $this->subscription) throw new \Exception('No subscription found');

			$subscription = $this->gateway->cancel($this->subscription['customer_id'], $atPeriodEnd);
			$this->storage->cancel($this->user, $subscription);

			$this->refreshSubscription();

			return $this;
		}
		catch(\Exception $e)
		{
			throw new \Exception($e->getMessage());
		}
	}

	private function getTrialEnd($days = null)
	{
		// special case for ending trial right now
		if($days == 'now') return $days;

		// if number of days is passed, we will calculate the end based upon it
		if($days)
		{
			return Carbon::today()->addDays($days)->toDateString();
		}

		// otherwise, if there was an ongoing trial, keep that as the trial else null
		else
		{
			if( ! $this->subscription) return null; // if no previous subsccription

			return $this->subscription['trial_ends_at'] // if there's trial end in previous subscription
				? Carbon::createFromFormat('Y-m-d H:i:s', $this->subscription['trial_ends_at'])->getTimestamp()
				: null;
		}
	}

	private function createCustomer($card, $description)
	{
		try
		{
			$customer = $this->gateway->create($card, $description);
			$this->storage->customer($this->user, $customer);

			$this->refreshSubscription();
		}
		catch(\Exception $e)
		{
			throw new \Exception($e->getMessage());
		}
	}
}