<?php namespace Owlgrin\Cashew\Gateway;

/**
 * The Gateway Contract
 */
interface Gateway {
	/**
	 * Creates a subscription
	 * @param  array $options
	 * @return Customer
	 */
	public function create($options);

	/**
	 * Deletes a subscription
	 * @param  string $customer
	 */
	public function delete($customer);

	/**
	 * Updates a customer
	 * @param  string $customer
	 * @param  array $options
	 * @return Customer
	 */
	public function update($customer, $options);

	/**
	 * Cancels a subscription
	 * @param  string $customer
	 * @param  boolean $atPeriodEnd
	 * @return Subscription
	 */
	public function cancel($customer, $atPeriodEnd);

	/**
	 * Returns the invoices
	 * @param  string $customer
	 * @return array
	 */
	public function invoices($customer);

	/**
	 * Returns the next invoice
	 * @param  string $customer
	 * @return Invoice
	 */
	public function nextInvoice($customer);

	/**
	 * Returns an event
	 * @param  string $event
	 * @return Event
	 */
	public function event($event);

	/**
	 * Update invoice item in invoice
	 * @param  array $item
	 * @return Invoice Item
	 */
	public function invoiceItem($item);

	/**
	 * Get customer
	 * @param  string $id
	 * @return Customer
	 */
	public function customer($id);
}