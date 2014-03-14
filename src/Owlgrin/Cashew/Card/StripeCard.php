<?php namespace Owlgrin\Cashew\Card;

use Owlgrin\Cashew\Card\Card;

class StripeCard implements Card {

	protected $card;

	public function __construct($card)
	{
		$this->card = $card;
	}

	public function get()
	{
		return $this->card;
	}

	public function lastFour()
	{
		return $this->card ? $this->card['last4'] : null;
	}
}