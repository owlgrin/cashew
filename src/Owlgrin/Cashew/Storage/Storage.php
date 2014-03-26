<?php namespace Owlgrin\Cashew\Storage;

use Owlgrin\Cashew\Customer\Customer;
use Owlgrin\Cashew\Subscription\Subscription;
use Owlgrin\Cashew\Invoice\Invoice;

interface Storage {
	public function subscription($id, $byCustomer);
	public function create($userId, Customer $customer);
	public function customer($userId, Customer $customer);
	public function subscribe($userId, Subscription $subscription);
	public function update($userId, Customer $customer);
	public function updateStatus($userId, $status);
	public function cancel($userId, Subscription $subscription);
	public function resume($userId);
	public function expire($userId);
	public function storeInvoice($userId, Invoice $invoice);
	public function getInvoices($userId, $count);
}