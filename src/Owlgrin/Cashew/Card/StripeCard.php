<?php namespace Owlgrin\Cashew\Card;

use Owlgrin\Cashew\Card\Card;

/**
 * The Stripe implementation for the card
 */
class StripeCard implements Card {

	/**
	 * Instance that stores the raw card object
	 * @var array
	 */
	protected $card;

	/**
	 * Constructor
	 * @param array $card Raw card object that is returned from the Stripe API
	 */
	public function __construct($card)
	{
		$this->card = $card;
	}

	/**
	 * Returns that raw object
	 * @return array
	 */
	public function get()
	{
		return $this->card;
	}

	/**
	 * Returns the last four digits of the card
	 * @return string
	 */
	public function lastFour()
	{
		return $this->card ? $this->card['last4'] : null;
	}

	/**
	 * Returns the expiry date of the card
	 * @return string
	 */
	public function expiryDate()
	{
		return $this->card ? $this->card['exp_year'] . '-' . $this->card['exp_month'] . '-01': null;
	}
}