<?php namespace Owlgrin\Cashew\Customer;

interface Customer {
	public function get();
	public function id();
	public function subscription(); // subscriptions() method can be used with mutiple subscriptions in future
	public function card(); // cards() method can be used with multiple cards in future
}