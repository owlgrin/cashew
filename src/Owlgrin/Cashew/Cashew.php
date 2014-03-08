<?php namespace Owlgrin\Cashew;

use Owlgrin\Cashew\Storage\Storage;
use Owlgrin\Cashew\Gateway\Gateway;
use Carbon\Carbon, Config;

class Cashew {
	protected $options = array(
		'coupon' => null,
		'trial_end' => null,
		'quantity' => 1
	);

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

	public function create($user, $card, $plan, $options = array())
	{
		try
		{
			if( ! Config::get('cashew::multiple') and $this->storage->subscription($user['id']))
			{
				throw new \Exception('Subscription already exist');
			}

			$options = array_merge($this->options, $options);
			$customer = $this->gateway->subscribe($user, $card, $plan, $options);
			$this->storage->store($user, $customer);

			$this->user($user['id']); // for further usage
		}
		catch(\Exception $e)
		{
			throw new \Exception($e->getMessage());
		}
	}

	public function update($userId, $options = array())
	{
		try
		{
			if( ! $this->subscription) throw new \Exception('No subscription');

			$customer = $this->gateway->updateCustomer($this->subscription['customer_id'], $options);
			$this->storage->update($customer);

			$this->refreshSubscription();
		}
		catch(\Exception $e)
		{
			throw new \Exception($e->getMessage());
		}
	}

	public function toPlan($userId, $plan, $prorate = true)
	{
		try
		{
			if( ! $this->subscription) throw new \Exception('No subscription');

			$subscription = $this->gateway->updateSubscription($this->subscription['customer_id'],
																$this->subscription['subscription_id'],
																array('plan' => $plan, 'prorate' => $prorate));
			$this->storage->toPlan($userId, $subscription);

			$this->refreshSubscription();
		}
		catch(\Exception $e)
		{
			throw new \Exception($e->getMessage());
		}
	}

	public function cancel($userId)
	{
		try
		{
			if( ! $this->subscription) throw new \Exception('No subscription');

			$subscription = $this->gateway->cancel($this->subscription['customer_id'],
													$this->subscription['subscription_id']);
			$this->storage->cancel($userId, $subscription);

			$this->refreshSubscription();
		}
		catch(\Exception $e)
		{
			throw new \Exception($e->getMessage());
		}
	}

	public function reactivate($userId, $plan = null)
	{
		try
		{
			if( ! $this->canceled($userId)) throw new \Exception('Cannot be reactivated');

			$subscription = $this->gateway->updateSubscription($this->subscription['customer_id'],
																$this->subscription['subscription_id'],
																array('plan' => $plan ? $plan : $this->subscription['plan']));
			$this->storage->reactivate($userId, $subscription);

			$this->refreshSubscription();
		}
		catch (\Exception $e)
		{
			throw new \Exception($e->getMessage());	
		}
	}

	public function onTrial()
	{
		if( ! $this->subscription) throw new \Exception('No subscription');

		return $this->subscription['status'] == self::STATUS_TRIAL
				and Carbon::today()->lte(Carbon::createFromFormat('Y-m-d H:i:s', $this->subscription['ends_at']));
	}

	public function onGrace()
	{
		if( ! $this->subscription) throw new \Exception('No subscription');

		return $this->subscription['status'] == self::STATUS_CANCEL
				and Carbon::today()->lte(Carbon::createFromFormat('Y-m-d H:i:s', $this->subscription['ends_at']));
	}

	public function expired()
	{
		if( ! $this->subscription) throw new \Exception('No subscription');

		return $this->subscription['status'] == self::STATUS_EXPIRE;
	}

	public function subscribed()
	{
		if( ! $this->subscription) throw new \Exception('No subscription');

		return $this->subscription['status'] == self::STATUS_ACTIVE
				and Carbon::today()->lte(Carbon::createFromFormat('Y-m-d H:i:s', $this->subscription['ends_at']));
	}

	public function canceled()
	{
		if( ! $this->subscription) throw new \Exception('No subscription');

		return $this->subscription['status'] == self::STATUS_CANCEL;
	}

	public function onPlan($plan)
	{
		if( ! $this->subscription) throw new \Exception('No subscription');

		return $this->subscription['plan'] == $plan;
	}
}