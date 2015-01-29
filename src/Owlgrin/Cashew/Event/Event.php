<?php namespace Owlgrin\Cashew\Event;

/**
 * The Event contract
 */
interface Event {
	/**
	 * Returns the raw data
	 * @return array
	 */
	public function get();

	/**
	 * Returns the type of the event
	 * @return string
	 */
	public function type();

	/**
	 * Returns the customer
	 * @return string
	 */
	public function customer();

	/**
	 * Returns the invoice if the event is of invoice type
	 * @return Invoice
	 */
	public function invoice();

	/**
	 * Returns the subscription object for the event
	 * @return Subscription
	 */
	public function subscription();

	/**
	 * Returns the number of attempts the event has made
	 * @return integer
	 */
	public function attempts();

	/**
	 * Decides if the event has failed more than a number of times
	 * @param  integer $count
	 * @return boolean
	 */
	public function failedMoreThan($count);
}