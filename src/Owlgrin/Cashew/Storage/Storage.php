<?php namespace Owlgrin\Cashew\Storage;

use Owlgrin\Cashew\Customer\Customer;
use Owlgrin\Cashew\Subscription\Subscription;
use Owlgrin\Cashew\Invoice\Invoice;

interface Storage {
	/**
	 * Returns the subscription of a user
	 * @param  string $id
	 * @param  boolean $byCustomer Whether to consider the passed 'id' as user's id of customer id of Gateway
	 * @return array
	 */
	public function subscription($id, $byCustomer);

	/**
	 * Insert the subscription for a user
	 * @param  string   $userId
	 * @param  Customer $customer
	 * @return string
	 */
	public function create($userId, Customer $customer);

	/**
	 * Updates the customer details for a user
	 * @param  string   $userId
	 * @param  Customer $customer
	 * @return null
	 */
	public function customer($userId, Customer $customer);

	/**
	 * Add data about subscription
	 * @param  string       $userId
	 * @param  Subscription $subscription
	 * @return null
	 */
	public function subscribe($userId, Subscription $subscription);

	/**
	 * Updates the subscription
	 * @param  string   $userId
	 * @param  Customer $customer
	 * @return string
	 */
	public function update($userId, Customer $customer);

	/**
	 * Changes the status of subscription
	 * @param  string $userId
	 * @param  string $status
	 * @return null
	 */
	public function updateStatus($userId, $status);

	/**
	 * Cancels the subscription
	 * @param  string       $userId
	 * @param  Subscription $subscription
	 * @return string
	 */
	public function cancel($userId, Subscription $subscription);

	/**
	 * Resumes the subscription
	 * @param  string $userId
	 * @return string
	 */
	public function resume($userId);

	/**
	 * Marks subscription as expired
	 * @param  string $userId
	 * @return string
	 */
	public function expire($userId);

	/**
	 * Saves an invoice locally
	 * @param  string  $userId
	 * @param  Invoice $invoice
	 * @return string
	 */
	public function storeInvoice($userId, Invoice $invoice);

	/**
	 * Returns the local invoices
	 * @param  string $userId
	 * @param  integer $count
	 * @return array
	 */
	public function getInvoices($userId, $count);
}