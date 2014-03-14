<?php namespace Owlgrin\Cashew\Hooks;

use Owlgrin\Cashew\Events\Event;

interface Hook {
	public function handle(Event $payload);
}