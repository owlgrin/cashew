<?php namespace Owlgrin\Cashew\InvoiceItem;

use Owlgrin\Cashew\InvoiceItem\InvoiceItem;
use Carbon\Carbon;

/**
 * The Stripe implementation of invoice item
 */
class StripeInvoiceItem implements InvoiceItem {

	/**
	 * Raw invoice item data
	 * @var array
	 */
	protected $invoiceItem;

	/**
	 * Period of invoice item
	 * @var array
	 */
	protected $period;

	public function __construct($invoiceItem)
	{
		$this->invoiceItem = $invoiceItem;
	}

	/**
	 * Returns the identifier
	 * @return string
	 */
	public function id()
	{
		return $this->invoiceItem['id'];
	}

	/**
	 * Returns the customer identifier
	 * @return string
	 */
	public function customerId()
	{
		return $this->invoiceItem['customer'];
	}

	/**
	 * Returns the subscription identifier
	 * @return string
	 */
	public function subscriptionId()
	{
		return $this->invoiceItem['subscription'];
	}

	/**
	 * Returns the currency of invoiceItem
	 * @return string
	 */
	public function currency()
	{
		return $this->invoiceItem['currency'];
	}

	/**
	 * Returns the date of invoiceItem
	 * @param  boolean $formatted
	 * @return mixed
	 */
	public function date($formatted = true)
	{
		return $formatted
			? Carbon::createFromTimestamp($this->invoiceItem['date'])->toFormattedDateString()
			: $this->invoiceItem['date'];
	}

	/**
	 * Returns the start of period of invoiceItem
	 * @param  boolean $formatted
	 * @return mixed
	 */
	public function periodStart($formatted = true)
	{
		return $formatted
			? Carbon::createFromTimestamp($this->invoiceItem['period']['start'])->toFormattedDateString()
			: $this->invoiceItem['period']['start'];
	}

	/**
	 * Returns the end of period of invoiceItem
	 * @param  boolean $formatted
	 * @return mixed
	 */
	public function periodEnd($formatted = true)
	{
		return $formatted
			? Carbon::createFromTimestamp($this->invoiceItem['period']['end'])->toFormattedDateString()
			: $this->invoiceItem['period']['end'];
	}

	/**
	 * Returns the amount of invoiceItem
	 * @return string
	 */
	public function amount()
	{
		return number_format($this->invoiceItem['amount'] / 100, 2);
	}

	/**
	 * Returns the formatted amount
	 * @return string
	 */
	public function formattedAmount()
	{
		return $this->_formatted($this->amount());
	}

	/**
	 * Formats the number
	 * @param  number $amount
	 * @return string
	 */
	private function _formatted($amount)
	{
		return number_format(round(money_format('%i', $amount), 2), 2);
	}

	/**
	 * Returns the description of invoiceItem
	 * @return string
	 */
	public function description()
	{
		return $this->invoiceItem['description'];
	}

	/**
	 * Returns the metadata of invoiceItem
	 * @return string
	 */
	public function metadata()
	{
		return $this->invoiceItem['metadata']['feature'];
	}
}