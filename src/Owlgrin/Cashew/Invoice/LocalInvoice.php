<?php namespace Owlgrin\Cashew\Invoice;

use Owlgrin\Cashew\Invoice\Invoice;
use Carbon\Carbon;

class LocalInvoice implements Invoice {

	protected $invoice;

	public function __construct($invoice)
	{
		$this->invoice = $invoice;
	}

	public function id()
	{
		return $this->invoice['invoice_id'];
	}

	public function customerId()
	{
		return $this->invoice['customer_id'];
	}

	public function subscriptionId()
	{
		return $this->invoice['subscription_id'];
	}

	public function currency()
	{
		return $this->invoice['currency'];
	}

	public function date($formatted = true)
	{
		return $formatted
			? Carbon::createFromFormat('Y-m-d H:i:s', $this->invoice['date'])->toFormattedDateString()
			: Carbon::createFromFormat('Y-m-d H:i:s', $this->invoice['date'])->getTimestamp();
	}

	public function periodStart($formatted = true)
	{
		return $formatted
			? Carbon::createFromFormat('Y-m-d H:i:s', $this->invoice['period_start'])->toFormattedDateString()
			: Carbon::createFromFormat('Y-m-d H:i:s', $this->invoice['period_start'])->getTimestamp();
	}

	public function periodEnd($formatted = true)
	{
		return $formatted
			? Carbon::createFromFormat('Y-m-d H:i:s', $this->invoice['period_end'])->toFormattedDateString()
			: Carbon::createFromFormat('Y-m-d H:i:s', $this->invoice['period_end'])->getTimestamp();
	}

	public function total()
	{
		return number_format($this->invoice['total'], 2);
	}

	public function formattedTotal()
	{
		return $this->_formatted($this->total());
	}

	public function subtotal()
	{
		return number_format($this->invoice['subtotal'], 2);
	}

	public function formattedSubtotal()
	{
		return $this->_formatted($this->subtotal());
	}

	public function hasDiscount()
	{
		return $this->invoice['total'] > 0 and $this->invoice['subtotal'] != $this->invoice['total'];
	}

	public function discount()
	{
		return number_format($this->subtotal() - $this->total(), 2);
	}

	public function formattedDiscount()
	{
		return $this->_formatted($this->discount());
	}

	private function _formatted($amount)
	{
		return number_format(round(money_format('%i', $amount), 2), 2);
	}
}