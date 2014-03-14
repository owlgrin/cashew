<?php namespace Owlgrin\Cashew\Storage;

use Owlgrin\Cashew\Storage\Storage;
use Owlgrin\Cashew\Customer\Customer;
use Owlgrin\Cashew\Subscription\Subscription;
use Owlgrin\Cashew\Card\Card;
use Carbon\Carbon, Config, DB;

class DbStorage implements Storage {
	
	public function subscription($id, $byCustomer = false)
	{
		if( ! $id) throw new \Exception('Cannot fetch subscription');
		
		return $byCustomer ? $this->subscriptionByCustomer($id) : $this->subscriptionByUser($id);
	}

	public function create($userId, Customer $customer)
	{
		try
		{
			$id = DB::table(Config::get('cashew::table'))->insertGetId(array(
				'user_id' => $userId,
				'customer_id' => $customer->id(),
				'subscription_id' => $customer->subscription()->id(),
				'trial_ends_at' => $customer->subscription()->trialEnd(),
				'plan' => $customer->subscription()->plan(),
				'quantity' => $customer->subscription()->quantity(),
				'last_four' => $customer->card()->lastFour(),
				'status' => $customer->subscription()->status(),
				'created_at' => DB::raw('now()'),
				'updated_at' => DB::raw('now()')
			));

			return $id;
		}
		catch(\PDOException $e)
		{
			throw new \Exception($e->getMessage());
		}
	}

	public function customer($userId, Customer $customer)
	{
		try
		{
			DB::table(Config::get('cashew::table'))
				->where('user_id', '=', $userId)
				->update(array(
					'customer_id' => $customer->id(),
					'last_four' => $customer->card()->lastFour(),
					'updated_at' => DB::raw('now()')
				));
		}
		catch(\PDOException $e)
		{
			throw new \Exception($e->getMessage());
		}
	}

	public function subscribe($userId, Subscription $subscription)
	{
		try
		{
			DB::table(Config::get('cashew::table'))
				->where('user_id', '=', $userId)
				->update(array(
					'subscription_id' => $subscription->id(),
					'trial_ends_at' => $subscription->trialEnd(),
					'subscription_ends_at' => null,
					'plan' => $subscription->plan(),
					'quantity' => $subscription->quantity(),
					'status' => $subscription->status(),
					'updated_at' => DB::raw('now()'),
					'subscribed_at' => DB::raw('now()')
				));
		}
		catch(\PDOException $e)
		{
			throw new \Exception($e->getMessage());
		}
	}

	public function update($userId, Customer $customer)
	{
		try
		{
			$subscription = $customer->subscription();

			$id = DB::table(Config::get('cashew::table'))
				->where('user_id', '=', $userId)
				->update(array(
					'subscription_id' => $subscription->id(),
					'trial_ends_at' => $subscription->trialEnd(),
					'subscription_ends_at' => $subscription->currentEnd(),
					'plan' => $subscription->plan(),
					'quantity' => $subscription->quantity(),
					'last_four' => $customer->card()->lastFour(),
					'status' => $subscription->status(),
					'updated_at' => DB::raw('now()')
				));

			return $id;
		}
		catch(\PDOException $e)
		{
			throw new \Exception($e->getMessage());
		}
	}

	public function resume($userId)
	{
		try
		{
			$id = DB::table(Config::get('cashew::table'))
				->where('user_id', '=', $userId)
				->update(array(
					'subscription_ends_at' => null,
					'canceled_at' => null,
				));

			return $id;
		}
		catch(\PDOException $e)
		{
			throw new \Exception($e->getMessage());
		}
	}

	public function cancel($userId, Subscription $subscription)
	{
		try
		{
			$id = DB::table(Config::get('cashew::table'))
				->where('user_id', '=', $userId)
				->update(array(
					'subscription_ends_at' => $subscription->end(),
					'status' => 'canceled',
					'updated_at' => DB::raw('now()'),
					'canceled_at' => DB::raw('now()')
				));

			return $id;
		}
		catch(\PDOException $e)
		{
			throw new \Exception($e->getMessage());
		}
	}

	public function expire($userId)
	{
		try
		{
			$id = DB::table(Config::get('cashew::table'))
				->where('user_id', '=', $userId)
				->update(array(
					'status' => 'expired',
					'updated_at' => DB::raw('now()'),
					'expired_at' => DB::raw('now()')
				));

			return $id;
		}
		catch(\PDOException $e)
		{
			throw new \Exception($e->getMessage());
		}
	}

	private function subscriptionByUser($userId)
	{
		return DB::table(Config::get('cashew::table'))
			->where('user_id', '=', $userId)
			->first();
	}

	private function subscriptionByCustomer($customerId)
	{
		return DB::table(Config::get('cashew::table'))
			->where('customer_id', '=', $customerId)
			->first();
	}
}