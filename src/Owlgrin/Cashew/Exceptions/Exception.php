<?php namespace Owlgrin\Cashew\Exceptions;

class Exception extends \Exception {

	const MESSAGE = 'Something went wrong when processing your request. Please try again in a minute';

	public function __construct($message = null)
	{
		parent::__construct($message ?: self::MESSAGE);
	}
}