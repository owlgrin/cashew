<?php namespace Owlgrin\Cashew\Storage;

use Owlgrin\Cashew\Customer\Customer;
use Owlgrin\Cashew\Subscription\Subscription;

interface Storage {
	public function subscription($id);
	public function create($userId, $trialEnd);
	public function customer($userId, Customer $customer);
	public function subscribe($userId, Subscription $subscription);
	public function update($userId, Customer $customer);
	// public function toPlan($userId, Subscription $subscription);
	public function cancel($userId, Subscription $subscription);
	public function reactivate($userId, Subscription $subscription);
}