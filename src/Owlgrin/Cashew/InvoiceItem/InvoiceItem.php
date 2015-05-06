<?php namespace Owlgrin\Cashew\InvoiceItem;

/**
 * The invoice contract
 */
interface InvoiceItem {
	/**
	 * Returns the unique identifier
	 * @return mixed
	 */
	public function id();

	/**
	 * Returns the customer ID
	 * @return string
	 */
	public function customerId();

	/**
	 * Returns the subscription ID
	 * @return string
	 */
	public function subscriptionId();

	/**
	 * Returns the currency of invoice item
	 * @return string
	 */
	public function currency();

	/**
	 * Returns the date of invoice item
	 * @param  boolean $formatted
	 * @return integer|string
	 */
	public function date($formatted);

	/**
	 * Timestamp of starting of period of invoice item
	 * @param  boolean $formatted
	 * @return integer|string
	 */
	public function periodStart($formatted);

	/**
	 * Timestamp of end of period of invoice item
	 * @param  boolean $formatted
	 * @return integer|string
	 */
	public function periodEnd($formatted);

	/**
	 * Amount of invoice item
	 * @return float
	 */
	public function amount();

	/**
	 * Returns the formatter amount
	 * @return string
	 */
	public function formattedAmount();

	/**
	 * Returns the description
	 * @return string
	 */
	public function description();

	/**
	 * Returns the meta data
	 * @return string
	 */
	public function metadata();
}