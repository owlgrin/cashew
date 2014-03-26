<?php namespace Owlgrin\Cashew\Event;

interface Event {
	public function get();
	public function type();
	public function customer();
	public function invoice();
	public function subscription();
	public function attempts();
	public function failedMoreThan($count);
}