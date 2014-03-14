<?php namespace Owlgrin\Cashew\Gateway;

interface Gateway {
	public function create($options);
	public function update($customer, $options);
	public function cancel($customer, $subscription);
	public function invoices($customer);
	public function nextInvoice($customer);
	public function event($event);
}