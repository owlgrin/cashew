<?php namespace Owlgrin\Cashew\Gateway;

interface Gateway {
	public function create($card, $description);
	public function update($customer, $options);
	public function cancel($customer, $subscription);
	public function invoices($customer);
}