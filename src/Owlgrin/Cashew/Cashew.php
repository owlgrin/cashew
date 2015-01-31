<?php namespace Owlgrin\Cashew;

use Owlgrin\Cashew\Storage\Storage;
use Owlgrin\Cashew\Gateway\Gateway;
use Owlgrin\Cashew\Exceptions\Exception;
use Carbon\Carbon, Config, Event;

/**
 * The Cashew core
 */
class Cashew {

	/**
	 * The available status for a subscription
	 */
	const STATUS_TRIAL = 'trialing';
	const STATUS_CANCEL = 'canceled';
	const STATUS_EXPIRE = 'expired';
	const STATUS_ACTIVE = 'active';

	/**
	 * The Gateway instance
	 * @var Gateway
	 */
	protected $gateway;

	/**
	 * The Storage instance
	 * @var Storage
	 */
	protected $storage;

	/**
	 * The user id
	 * @var string|number
	 */
	protected $user = null;

	/**
	 * The subscription for the user
	 * @var array
	 */
	protected $subscription = null;


	public function __construct(Gateway $gateway, Storage $storage)
	{
		$this->gateway = $gateway;
		$this->storage = $storage;
	}

	/**
	 * Sets the user and subscription for later use
	 * @param  string|number $user
	 * @return Cashew
	 */
	public function user($user)
	{
		$this->user = $user;
		$this->subscription = $this->storage->subscription($this->user);
		return $this;
	}

	/**
	 * Refreshes the subscription
	 * @return void
	 */
	public function refreshSubscription()
	{
		$this->subscription = $this->storage->subscription($this->user);
	}

	/**
	 * Returns the user
	 * @return string|number
	 */
	public function getUser()
	{
		return $this->user;
	}

	/**
	 * Returns the subscription
	 * @return array
	 */
	public function getSubscription()
	{
		return $this->subscription;
	}

	/**
	 * Creates a new subscription
	 * @param  string|number $id
	 * @param  array $options
	 * @return Cashew
	 */
	public function create($id, $options)
	{
		if($this->storage->subscription($id)) throw new Exception('Customer already exist');

		$options['trial_end'] = $this->getTrialEnd(isset($options['trial_end']) ? $options['trial_end'] : null);
		$customer = $this->gateway->create($options);
		$this->storage->create($id, $customer);

		$this->user($id); // for further usage

		return $this;
	}

	/**
	 * Update the card for a subscription
	 * @param  array|string $card
	 * @param  array  $options
	 * @return Cashew
	 */
	public function card($card, $options = array())
	{
		$options['card'] = $card;
		$options['plan'] = $this->subscription['plan'];
		return $this->update($options);
	}

	/**
	 * Adds a coupon to subscription
	 * @param  string $coupon
	 * @return Cashew
	 */
	public function coupon($coupon)
	{
		return $this->update(array('coupon' => $coupon, 'plan' => $this->subscription['plan']));
	}

	/**
	 * Change the subscription plan
	 * @param  string  $plan
	 * @param  boolean $prorate
	 * @param  boolean $maintainTrial
	 * @return Cashew
	 */
	public function toPlan($plan, $prorate = true, $maintainTrial = true)
	{
		$trialEnd = $this->onTrial() ? $this->getTrialEnd() : null;

		return $this->update(array('plan' => $plan, 'trial_end' => $trialEnd, 'prorate' => $prorate, 'quantity' => $this->subscription['quantity']));
	}

	/**
	 * Increments the quantity in subscription
	 * @param  integer $quantity
	 * @return Cashew
	 */
	public function increment($quantity = 1)
	{
		return $this->quantity($this->subscription['quantity'] + $quantity);
	}

	/**
	 * Decrements the quantity in subscription
	 * @param  integer $quantity
	 * @return Cashew
	 */
	public function decrement($quantity = 1)
	{
		return $this->quantity($this->subscription['quantity'] - $quantity);
	}

	/**
	 * Updates the quantity in the subscription
	 * @param  integer $quantity
	 * @return Cashew
	 */
	public function quantity($quantity)
	{
		return $this->update(array('plan' => $this->subscription['plan'], 'quantity' => $quantity));
	}

	/**
	 * Updates the subscription
	 * @param  array  $options
	 * @return Cashew
	 */
	public function update($options = array())
	{
		if( ! $this->subscription) throw new Exception('No subscription found');

		$customer = $this->gateway->update($this->subscription['customer_id'], $options);
		$this->storage->update($this->user, $customer);

		$this->refreshSubscription();

		return $this;
	}

	/**
	 * Expires a subscription
	 * @return void
	 */
	public function expire()
	{
		$this->cancelNow();
		$this->storage->expire($this->user);
	}

	/**
	 * Expires using customer id
	 * @param  string $customerId
	 * @return void
	 */
	public function expireCustomer($customerId)
	{
		// set the subscription by user
		$subscription = $this->storage->subscription($customerId, true);
		$this->user($subscription['user_id']);

		// and then call the method which already expires using user
		$this->expire();
	}

	/**
	 * Cancels a subscription immediately
	 * @return Cashew
	 */
	public function cancelNow()
	{
		return $this->cancel(false);
	}

	/**
	 * Cancels a subscription
	 * @param  boolean $atPeriodEnd
	 * @return Cashew
	 */
	public function cancel($atPeriodEnd = true)
	{
		if( ! $this->subscription) throw new Exception('No subscription found');
		if($this->canceled()) throw new Exception('Already canceled');

		$subscription = $this->gateway->cancel($this->subscription['customer_id'], $atPeriodEnd);
		$this->storage->cancel($this->user, $subscription);

		// refreshing subscription to take the updated value
		$this->refreshSubscription();

		return $this;
	}

	/**
	 * Resumes a subscription
	 * @param  array  $options
	 * @param  string|null $card
	 * @return void
	 */
	public function resume($options = array(), $card = null)
	{
		if( ! $this->canceled() and ! $this->expired()) throw new Exception('Cannot be reactivated'); // cannot reactivate if not canceled and not expired

		// if new plan passed, then consider it else default to the previous plan
		$options['plan'] = isset($options['plan']) ? $options['plan'] : $this->subscription['plan'];

		// ending the trial right now
		$options['trial_end'] = $this->getTrialEnd(isset($options['trial_end']) ? $options['trial_end'] : null);

		// continuing from the previous quantity
		$options['quantity'] = isset($options['quantity']) ? $options['quantity'] : $this->subscription['quantity'];

		// no prorate
		$options['prorate'] = false;

		$shouldBeRestored = $this->expired(); // if subscription was resumed after expiration, we will fire an event later

		$this->update(array_merge($options, compact('card')));
		$this->storage->resume($this->user);

		if($shouldBeRestored) Event::fire('cashew.user.restore', array($this->subscription['user_id']));
	}

	/**
	 * Returns the invoices of a customer
	 * @param  boolean $fromLocal
	 * @param  integer $count
	 * @return array
	 */
	public function invoices($fromLocal = true, $count = 10)
	{
		if( ! $this->subscription) throw new Exception('No subscription found');

		// we are allowing invoices to be fetched from local copy because that is super fast
		return $fromLocal
			? $this->storage->getInvoices($this->subscription['user_id'], $count)
			: $this->gateway->invoices($this->subscription['customer_id'], $count);
	}

	/**
	 * Returns the upcoming invoice
	 * @return Invoice
	 */
	public function nextInvoice()
	{
		if( ! $this->subscription) throw new Exception('No subscription found');

		return $this->gateway->nextInvoice($this->subscription['customer_id']);
	}

	public function status()
	{
		return $this->subscription['status'];
	}

	public function active()
	{
		return $this->onTrial() or $this->onGrace() or $this->subscribed();
	}

	public function inactive()
	{
		return ! $this->active();
	}

	public function hasCard()
	{
		return is_null($this->subscription['last_four']) ? false : true;
	}

	public function onTrial()
	{
		if( ! $this->subscription) throw new Exception('No subscription found');

		if(is_null($this->subscription['trial_ends_at'])) return false;

		return $this->status() == self::STATUS_TRIAL;
	}

	public function onGrace()
	{
		if( ! $this->subscription) throw new Exception('No subscription found');

		if(is_null($this->subscription['subscription_ends_at'])) return false;

		return $this->status() == self::STATUS_CANCEL
				and Carbon::today()->lt(Carbon::createFromFormat('Y-m-d H:i:s', $this->subscription['subscription_ends_at']));
	}

	public function expired()
	{
		if( ! $this->subscription) throw new Exception('No subscription found');

		return $this->status() == self::STATUS_EXPIRE;
	}

	public function subscribed()
	{
		if( ! $this->subscription) throw new Exception('No subscription found');

		return $this->status() == self::STATUS_ACTIVE;
	}

	public function canceled()
	{
		if( ! $this->subscription) throw new Exception('No subscription found');

		return $this->status() == self::STATUS_CANCEL;
	}

	public function onPlan($plan)
	{
		if( ! $this->subscription) throw new Exception('No subscription found');

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

			if($this->subscription['trial_ends_at']) // if there's trial end in previous subscription
			{
				return Carbon::createFromFormat('Y-m-d H:i:s', $this->subscription['trial_ends_at'])->getTimestamp();
			}
			else return null;
		}
	}
}