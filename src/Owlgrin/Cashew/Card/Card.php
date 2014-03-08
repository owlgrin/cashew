<?php namespace Owlgrin\Cashew\Card;

use Owlgrin\Cashew\Card\Card;

interface Card {
	public function get();
	public function lastFour();
}