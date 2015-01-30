<?php namespace Owlgrin\Cashew\Exceptions;

class ReactivateSubscriptionException extends \Exception {

	const MESSAGE = 'Subscription cannot be reactivated.';

	public function __construct($message = null)
	{
		parent::__construct($message ?: self::MESSAGE);
	}
}