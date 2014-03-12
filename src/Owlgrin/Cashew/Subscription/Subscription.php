<?php namespace Owlgrin\Cashew\Subscription;

interface Subscription {
	public function get();
	public function id();
	public function plan();
	public function status();
	public function trialEnd();
	public function currentEnd();
	public function end();
}