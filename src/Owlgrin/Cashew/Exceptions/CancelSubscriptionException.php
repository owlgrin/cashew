<?php namespace Owlgrin\Cashew\Exceptions;

class CancelSubscriptionException extends \Exception {

	const MESSAGE = 'Subscription already canceled.';

	public function __construct($message = null)
	{
		parent::__construct($message ?: self::MESSAGE);
	}
}