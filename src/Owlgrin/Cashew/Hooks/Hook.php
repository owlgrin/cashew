<?php namespace Owlgrin\Cashew\Hooks;

use Owlgrin\Cashew\Event\Event;

/**
 * The Hook Contract
 */
interface Hook {
	/**
	 * Handles the Webhook call
	 * @param  Event  $payload
	 * @return mixed
	 */
	public function handle(Event $payload);
}