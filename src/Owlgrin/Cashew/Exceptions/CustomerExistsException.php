<?php namespace Owlgrin\Cashew\Exceptions;

class CustomerExistsException extends \Exception {

	const MESSAGE = 'Customer already exists.';

	public function __construct($message = null)
	{
		parent::__construct($message ?: self::MESSAGE);
	}
}