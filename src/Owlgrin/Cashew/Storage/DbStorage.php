<?php namespace Owlgrin\Cashew\Storage;

use Owlgrin\Cashew\Storage\Storage;
use Owlgrin\Cashew\Subscription\Subscription;
use Carbon\Carbon, Config, DB;

class DbStorage implements Storage {
	
	public function subscription($id)
	{
		if( ! $id) throw new \Exception('Cannot fetch subscription');
		
		return $this->subscriptionByUser($id);
	}

	public function store($user, $customer)
	{
		try
		{
			$subscription = $customer['subscription'];
			$card = $customer['cards']['data'][0];

			$id = DB::table(Config::get('cashew::table'))->insertGetId(array(
				'user_id' => $user['id'],
				'customer_id' => $customer['id'],
				'subscription_id' => $subscription['id'],
				'ends_at' => Carbon::createFromTimestamp($subscription['current_period_end'])->toDateString(),
				'plan' => $subscription['plan']['id'],
				'last_four' => $card['last4'],
				'status' => $subscription['status'],
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

	public function update($customer)
	{
		try
		{
			$subscription = $customer['subscription'];
			$card = $customer['cards']['data'][0];

			$id = DB::table(Config::get('cashew::table'))
				->where('customer_id', '=', $customer['id'])
				->update(array(
					'subscription_id' => $subscription['id'],
					'ends_at' => Carbon::createFromTimestamp($subscription['current_period_end'])->toDateString(),
					'plan' => $subscription['plan']['id'],
					'last_four' => $card['last4'],
					'status' => $subscription['status'],
					'updated_at' => DB::raw('now()')
				));

			return $id;
		}
		catch(\PDOException $e)
		{
			throw new \Exception($e->getMessage());
		}
	}

	public function toPlan($userId, Subscription $subscription)
	{
		try
		{
			$id = DB::table(Config::get('cashew::table'))
				->where('user_id', '=', $userId)
				->where('subscription_id', '=', $subscription->id())
				->update(array(
					'ends_at' => $subscription->currentEnd(),
					'plan' => $subscription->plan(),
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

	public function cancel($userId, Subscription $subscription)
	{
		try
		{
			$id = DB::table(Config::get('cashew::table'))
				->where('user_id', '=', $userId)
				->where('subscription_id', '=', $subscription->id())
				->update(array(
					'status' => 'canceled',
					'updated_at' => DB::raw('now()')
				));

			return $id;
		}
		catch(\PDOException $e)
		{
			throw new \Exception($e->getMessage());
		}
	}

	public function reactivate($userId, Subscription $subscription)
	{
		try
		{
			DB::table(Config::get('cashew::table'))
				->where('user_id', '=', $userId)
				->where('subscription_id', '=', $subscription->id())
				->update(array(
					'ends_at' => $subscription->currentEnd(),
					'plan' => $subscription->plan(),
					'status' => $subscription->status(),
					'updated_at' => DB::raw('now()')
				));
		}
		catch (\Exception $e) {
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