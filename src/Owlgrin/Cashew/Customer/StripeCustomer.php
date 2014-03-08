<?php namespace Owlgrin\Cashew\Customer;

use Owlgrin\Cashew\Customer\Customer;
use Owlgrin\Cashew\Subscription\StripeSubscription;

class StripeCustomer implements Customer {

	protected $customer;

	public function __construct($customer)
	{
		$this->customer = $customer;
	}

	public function get()
	{
		return $this->customer;
	}

	public function id()
	{
		return $this->customer['id'];
	}

	public function subscription()
	{
		return new StripeSubscription($this->customer['subscription']);
	}

	public function card()
	{
		return $this->customer['cards']['data'][0];
	}
}