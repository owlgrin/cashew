<?php namespace Owlgrin\Cashew\Invoice;

interface StorableInvoice {
	public function store($userId);
}