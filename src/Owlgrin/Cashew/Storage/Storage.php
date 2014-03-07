<?php namespace Owlgrin\Cashew\Storage;

interface Storage {
	public function subscription($id);
	public function store($user, $customer);
	public function update($customer);
	public function toPlan($customer, $subscription);
	public function cancel($userId, $subscription);
	public function reactivate($userId, $subscription);
}