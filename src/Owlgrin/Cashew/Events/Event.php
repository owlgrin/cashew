<?php namespace Owlgrin\Cashew\Events;

interface Event {
	public function get();
	public function type();
	public function customer();
	public function attempts();
	public function failedMoreThan($count);
}