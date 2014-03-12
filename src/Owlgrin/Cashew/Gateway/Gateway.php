<?php namespace Owlgrin\Cashew\Gateway;

interface Gateway {
	public function create($card, $description);
	public function update($customer, $options);
	public function updateSubscription($customer, $subscription, $options);
	public function updateCustomer($customer, $options);
	public function cancel($customer, $subscription);
}