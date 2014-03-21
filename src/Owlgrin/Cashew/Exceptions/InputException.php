<?php namespace Owlgrin\Cashew\Exceptions;

class InputException extends \Exception {

	const MESSAGE = 'The request couldn\'t be completed because of some errors in your input';

	public function __construct($message = null)
	{
		parent::__construct($message ?: self::MESSAGE);
	}
}