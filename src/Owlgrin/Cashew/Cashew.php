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
		return $this->update(array('plan' => $this->subscription['plan'], 'card' => $card));
	}

	public function toPlan($plan, $prorate = true)
	{
		return $this->update(compact('plan', 'prorate'));
	}

	public function increment($quantity = 1)
	{
		return $this->quantity($this->subscription['quantity'] + $quantity);
	}

	public function decrement($quantity = 1)
	{
		return $this->quantity($this->subscription['quantity'] - $quantity);
	}

	public function quantity($quantity)
	{
		return $this->update(array('plan' => $this->subscription['plan'], 'quantity' => $quantity));
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

	public function cancelNow()
	{
		return $this->cancel(false);
	}

	public function cancel($atPeriodEnd = true)
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

	public function resume($options = array())
	{
		if( ! $this->canceled()) throw new \Exception('Cannot be reactivated'); // cannot reactivate if not canceled

		// if new plan passed, then consider it else default to the previous plan
		$options['plan'] = isset($options['plan']) ? $options['plan'] : $this->subscription['plan'];
		
		// ending the trial right now
		$options['trial_end'] = 'now';

		// no prorate
		$options['prorate'] = false;

		return $this->subscribe(null, '', $options);
	}

	public function active()
	{
		return $this->onTrial() or $this->onGrace() or $this->subscribed();
	}

	public function inactive()
	{
		return ! $this->active();
	}

	public function onTrial()
	{
		if( ! $this->subscription) throw new \Exception('No subscription found');

		if(is_null($this->subscription['trial_ends_at'])) return false;

		return $this->subscription['status'] == self::STATUS_TRIAL
				and Carbon::today()->lt(Carbon::createFromFormat('Y-m-d H:i:s', $this->subscription['trial_ends_at']));
	}

	public function onGrace()
	{
		if( ! $this->subscription) throw new \Exception('No subscription found');

		if(is_null($this->subscription['subscription_ends_at'])) return false;

		return $this->subscription['status'] == self::STATUS_CANCEL
				and Carbon::today()->lt(Carbon::createFromFormat('Y-m-d H:i:s', $this->subscription['subscription_ends_at']));
	}

	public function expired()
	{
		if( ! $this->subscription) throw new \Exception('No subscription found');

		return $this->subscription['status'] == self::STATUS_EXPIRE;
	}

	public function subscribed()
	{
		if( ! $this->subscription) throw new \Exception('No subscription found');

		return $this->subscription['status'] == self::STATUS_ACTIVE;
	}

	public function canceled()
	{
		if( ! $this->subscription) throw new \Exception('No subscription found');

		return $this->subscription['status'] == self::STATUS_CANCEL;
	}

	public function onPlan($plan)
	{
		if( ! $this->subscription) throw new \Exception('No subscription found');

		return $this->subscription['plan'] == $plan;
	}

	private function getTrialEnd($days = null)
	{
		// special case for ending trial right now
		if($days == 'now') return $days;

		// if number of days is passed, we will calculate the end based upon it
		if($days)
		{
			return Carbon::today()->addDays($days)->getTimestamp();
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