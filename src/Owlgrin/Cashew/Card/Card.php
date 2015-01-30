<?php namespace Owlgrin\Cashew\Card;

use Owlgrin\Cashew\Card\Card;

/**
 * The Card contract
 */
interface Card {
	/**
	 * Returns the original object
	 * @return mixed
	 */
	public function get();

	/**
	 * Returns the last four digits of the card
	 * @return string
	 */
	public function lastFour();

	/**
	 * Returns the expiry date of the card
	 * @return string
	 */
	public function expiryDate();
}