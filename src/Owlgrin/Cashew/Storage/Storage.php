<?php namespace Owlgrin\Cashew\Storage;

use Owlgrin\Cashew\Customer\Customer;
use Owlgrin\Cashew\Subscription\Subscription;

interface Storage {
	public function subscription($id, $byCustomer);
	public function create($userId, Customer $customer);
	public function customer($userId, Customer $customer);
	public function subscribe($userId, Subscription $subscription);
	public function update($userId, Customer $customer);
	public function cancel($userId, Subscription $subscription);
	public function resume($userId);
	public function expire($userId);
}