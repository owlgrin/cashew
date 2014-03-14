<?php namespace Owlgrin\Cashew\Hooks;

use Owlgrin\Cashew\Event\Event;

interface Hook {
	public function handle(Event $payload);
}