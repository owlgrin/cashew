<?php namespace Owlgrin\Cashew\Customer;

/**
 * The Customer contract
 */
interface Customer {
	/**
	 * Returns the raw customer object
	 * @return mixed
	 */
	public function get();

	/**
	 * Returns the unique identifier of the customer
	 * @return string
	 */
	public function id();

	/**
	 * Returns the subscription object for the customer
	 * @return Subscription
	 */
	public function subscription(); // subscriptions() method can be used with mutiple subscriptions in future

	/**
	 * Returns the card for the customer
	 * @return Card
	 */
	public function card(); // cards() method can be used with multiple cards in future
}