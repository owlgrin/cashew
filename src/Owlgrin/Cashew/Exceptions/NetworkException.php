<?php namespace Owlgrin\Cashew\Exceptions;

class NetworkException extends \Exception {

	const MESSAGE = 'Couldn\'t process your request right now due to some network problem. Please try again in a minute';

	public function __construct($message = null)
	{
		parent::__construct($message ?: self::MESSAGE);
	}
}