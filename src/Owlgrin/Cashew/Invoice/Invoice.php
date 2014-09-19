<?php namespace Owlgrin\Cashew\Invoice;

/**
 * The invoice contract
 */
interface Invoice {
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
	 * Returns the currency of invoice
	 * @return string
	 */
	public function currency();

	/**
	 * Returns the date of invoice
	 * @param  boolean $formatted
	 * @return integer|string
	 */
	public function date($formatted);

	/**
	 * Timestamp of starting of period of invoice
	 * @param  boolean $formatted
	 * @return integer|string
	 */
	public function periodStart($formatted);

	/**
	 * Timestamp of end of period of invoice
	 * @param  boolean $formatted
	 * @return integer|string
	 */
	public function periodEnd($formatted);

	/**
	 * Total amount of invoice
	 * @return float
	 */
	public function total();

	/**
	 * Returns the formatter total
	 * @return string
	 */
	public function formattedTotal();

	/**
	 * Returns the subtotal of invoice
	 * @return float
	 */
	public function subtotal();

	/**
	 * Returns the formatted subtotal
	 * @return string
	 */
	public function formattedSubtotal();

	/**
	 * Tells if the invoice has discount or not
	 * @return boolean
	 */
	public function hasDiscount();

	/**
	 * Returns the discount
	 * @return float
	 */
	public function discount();

	/**
	 * Returns the formatted discount
	 * @return string
	 */
	public function formattedDiscount();
}