<?php namespace Owlgrin\Cashew\Exceptions;

class NoSubscriptionException extends \Exception {

	const MESSAGE = 'No subscription exists.';

	public function __construct($message = null)
	{
		parent::__construct($message ?: self::MESSAGE);
	}
}