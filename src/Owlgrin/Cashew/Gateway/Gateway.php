<?php namespace Owlgrin\Cashew\Gateway;

interface Gateway {
	public function create($user, $meta);
	public function subscribe($customer, $card, $plan, $options);
	public function updateSubscription($customer, $subscription, $options);
	public function updateCustomer($customer, $options);
	public function cancel($customer, $subscription);
}