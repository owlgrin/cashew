<?php namespace Owlgrin\Cashew\Storage;

use Owlgrin\Cashew\Subscription\Subscription;

interface Storage {
	public function subscription($id);
	public function store($user, $customer);
	public function update($customer);
	public function toPlan($userId, Subscription $subscription);
	public function cancel($userId, Subscription $subscription);
	public function reactivate($userId, Subscription $subscription);
}