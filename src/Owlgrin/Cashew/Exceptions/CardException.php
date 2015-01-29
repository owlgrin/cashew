<?php namespace Owlgrin\Cashew\Exceptions;

class CardException extends \Exception {

	const MESSAGE = 'The card was declined';

	public function __construct($message = null)
	{
		parent::__construct($message ?: self::MESSAGE);
	}
}