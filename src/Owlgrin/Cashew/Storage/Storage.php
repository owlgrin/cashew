<?php namespace Owlgrin\Cashew\Storage;

use Owlgrin\Cashew\Customer\Customer;
use Owlgrin\Cashew\Subscription\Subscription;

interface Storage {
	public function subscription($id);
	public function create($userId, Customer $customer);
	public function subscribe($userId, Customer $customer);
	public function update(Customer $customer);
	public function toPlan($userId, Subscription $subscription);
	public function cancel($userId, Subscription $subscription);
	public function reactivate($userId, Subscription $subscription);
}