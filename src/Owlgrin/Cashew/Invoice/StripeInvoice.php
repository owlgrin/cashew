<?php namespace Owlgrin\Cashew\Invoice;

use Owlgrin\Cashew\Invoice\Invoice;
use Owlgrin\Cashew\Invoice\StorableInvoice;
use App, Carbon\Carbon;

/**
 * The Stripe implementation of invoice
 */
class StripeInvoice implements Invoice, StorableInvoice {

	/**
	 * Raw invoice datas
	 * @var array
	 */
	protected $invoice;

	/**
	 * Period of invoice
	 * @var array
	 */
	protected $period;

	public function __construct($invoice)
	{
		$this->storage = App::make('Owlgrin\Cashew\Storage\Storage');
		$this->invoice = $invoice;
	}

	/**
	 * Returns the identifier
	 * @return string
	 */
	public function id()
	{
		return $this->invoice['id'];
	}

	/**
	 * Returns the customer identifier
	 * @return string
	 */
	public function customerId()
	{
		return $this->invoice['customer'];
	}

	/**
	 * Returns the subscription identifier
	 * @return string
	 */
	public function subscriptionId()
	{
		return $this->invoice['subscription'];
	}

	/**
	 * Returns the currency of invoice
	 * @return string
	 */
	public function currency()
	{
		return $this->invoice['currency'];
	}

	/**
	 * Returns the date of invoice
	 * @param  boolean $formatted
	 * @return mixed
	 */
	public function date($formatted = true)
	{
		return $formatted
			? Carbon::createFromTimestamp($this->invoice['date'])->toFormattedDateString()
			: $this->invoice['date'];
	}

	/**
	 * Returns the start of period of invoice
	 * @param  boolean $formatted
	 * @return mixed
	 */
	public function periodStart($formatted = true)
	{
		return $formatted
			? Carbon::createFromTimestamp($this->invoice['period_start'])->toFormattedDateString()
			: $this->invoice['period_start'];
	}

	/**
	 * Returns the end of period of invoice
	 * @param  boolean $formatted
	 * @return mixed
	 */
	public function periodEnd($formatted = true)
	{
		return $formatted
			? Carbon::createFromTimestamp($this->invoice['period_end'])->toFormattedDateString()
			: $this->invoice['period_end'];
	}

	/**
	 * Returns the total of invoice
	 * @return string
	 */
	public function total()
	{
		return number_format($this->invoice['total'] / 100, 2);
	}

	/**
	 * Returns the formatted total
	 * @return string
	 */
	public function formattedTotal()
	{
		return $this->_formatted($this->total());
	}

	/**
	 * Returns the subtotal of invoice
	 * @return string
	 */
	public function subtotal()
	{
		return number_format($this->invoice['subtotal'] / 100, 2);
	}

	/**
	 * Returns the formatted subtotal of invoice
	 * @return string
	 */
	public function formattedSubtotal()
	{
		return $this->_formatted($this->subtotal());
	}

	/**
	 * Checks if invoice has discount or not
	 * @return boolean
	 */
	public function hasDiscount()
	{
		return $this->invoice['total'] > 0 and $this->invoice['subtotal'] != $this->invoice['total'];
	}

	/**
	 * Returns the discount of invoice
	 * @return string
	 */
	public function discount()
	{
		return number_format($this->subtotal() - $this->total(), 2);
	}

	/**
	 * Returns the formatted discount in invoice
	 * @return string
	 */
	public function formattedDiscount()
	{
		return $this->_formatted($this->discount());
	}

	/**
	 * Stores the invoice in local storage
	 * @param  string $userId
	 * @return number
	 */
	public function store($userId)
	{
		$this->storage->storeInvoice($userId, $this);
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
}