<?php namespace Owlgrin\Cashew\Invoice;

/**
 * The storable invoice contract
 */
interface StorableInvoice {
	/**
	 * Stores the invoice in a local storage
	 * @param  integer|string $userId
	 * @return mixed
	 */
	public function store($userId);
}