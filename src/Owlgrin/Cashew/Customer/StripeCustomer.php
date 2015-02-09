<?php namespace Owlgrin\Cashew\Customer;

use Owlgrin\Cashew\Customer\Customer;
use Owlgrin\Cashew\Subscription\StripeSubscription;
use Owlgrin\Cashew\Card\StripeCard;

/**
 * The Stripe implementation of the Customer contract
 */
class StripeCustomer implements Customer {

	/**
	 * Holds the raw object
	 * @var array|object
	 */
	protected $customer;

	/**
	 * Constructor
	 * @param mixed $customer
	 */
	public function __construct($customer)
	{
		$this->customer = $customer;
	}

	/**
	 * Returns the raw object
	 * @return array|object
	 */
	public function get()
	{
		return $this->customer;
	}

	/**
	 * Returns the unique identifier of the customer
	 * @return string
	 */
	public function id()
	{
		return $this->customer['id'];
	}

	/**
	 * Returns the Subscription for the customer
	 * @return Subscription
	 */
	public function subscription()
	{
		return new StripeSubscription($this->customer['subscriptions']['total_count'] > 0
			? $this->customer['subscriptions']['data'][0]
			: null);
	}

	/**
	 * Returns the card for the customer
	 * @return Card
	 */
	public function card()
	{
		return new StripeCard($this->customer['cards']['total_count'] > 0
			? $this->customer['cards']['data'][0]
			: null);
	}
}