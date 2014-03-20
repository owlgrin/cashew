<?php namespace Owlgrin\Cashew\Invoice;

interface Invoice {
	public function id();
	public function customerId();
	public function subscriptionId();
	public function currency();
	public function date($formatted);
	public function periodStart($formatted);
	public function periodEnd($formatted);
	public function total();
	public function formattedTotal();
	public function subtotal();
	public function formattedSubtotal();
	public function hasDiscount();
	public function discount();
	public function formattedDiscount();
}