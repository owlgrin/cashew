<?php namespace Owlgrin\Cashew\Exceptions;

class DatabaseException extends \Exception {

	const MESSAGE = 'Something went wrong in database.';

	public function __construct($message = null)
	{
		parent::__construct($message ?: self::MESSAGE);
	}
}