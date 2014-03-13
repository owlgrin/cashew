<?php namespace Owlgrin\Cashew\Invoice;

interface Invoice {
	public function currency();
	public function date();
	public function total();
	public function formattedTotal();
	public function subtotal();
	public function formattedSubtotal();
	public function hasDiscount();
	public function discount();
	public function formattedDiscount();
}