<?php namespace Owlgrin\Cashew\Invoice;

use Owlgrin\Cashew\Invoice\Invoice;
use Owlgrin\Cashew\Invoice\StorableInvoice;
use App, Carbon\Carbon;

class StripeInvoice implements Invoice, StorableInvoice {

	protected $invoice;

	public function __construct($invoice)
	{
		$this->storage = App::make('Owlgrin\Cashew\Storage\Storage');
		$this->invoice = $invoice;
	}

	public function id()
	{
		return $this->invoice['id'];
	}

	public function customerId()
	{
		return $this->invoice['customer'];
	}

	public function subscriptionId()
	{
		return $this->invoice['subscription'];
	}

	public function currency()
	{
		return $this->invoice['currency'];
	}

	public function date($formatted = true)
	{
		return $formatted
			? Carbon::createFromTimestamp($this->invoice['date'])->toFormattedDateString()
			: $this->invoice['date'];
	}

	public function periodStart($formatted = true)
	{
		return $formatted
			? Carbon::createFromTimestamp($this->invoice['period_start'])->toFormattedDateString()
			: $this->invoice['period_start'];
	}

	public function periodEnd($formatted = true)
	{
		return $formatted
			? Carbon::createFromTimestamp($this->invoice['period_end'])->toFormattedDateString()
			: $this->invoice['period_end'];
	}

	public function total()
	{
		return number_format($this->invoice['total'] / 100, 2);
	}

	public function formattedTotal()
	{
		return $this->_formatted($this->total());
	}

	public function subtotal()
	{
		return number_format($this->invoice['subtotal'] / 100, 2);
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

	public function store($userId)
	{
		$this->storage->storeInvoice($userId, $this);
	}

	private function _formatted($amount)
	{
		return number_format(round(money_format('%i', $amount), 2), 2);
	}
}