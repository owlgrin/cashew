<?php namespace Owlgrin\Cashew\Gateway;

use Stripe_Customer, Stripe_CardError, Stripe_Error;

class StripeGateway implements Gateway {
	public function subscribe($user, $card, $plan, $options = array())
	{
		try
		{
			$customer = Stripe_Customer::create(array(
				'card' => $card,
				'plan' => $plan,
				'trial_end' => $options['trial_end'],
				'quantity' => $options['quantity'],
				'coupon' => $options['coupon'],
				'description' => 'Customer for ' . $user['email'],
				'metadata' => array(
					'_id' => $user['id'],
					'_email' => $user['email']
				)
			));

			return $customer;
		}
		catch(Stripe_CardError $e)
		{
			throw new \Exception($e->getMessage());
		}
		catch(Stripe_Error $e)
		{
			throw new \Exception($e->getMessage());
		}
		catch(\Exception $e)
		{
			throw new \Exception($e->getMessage());
		}
	}

	public function updateSubscription($customer, $subscription, $options = array())
	{
		try
		{
			$subscription = Stripe_Customer::retrieve($customer)->subscriptions->retrieve($subscription);

			foreach($options as $option => $value)
			{
				if($value) $subscription->{$option} = $value;
			}

			return $subscription->save();
		}
		catch(Stripe_CardError $e)
		{
			throw new \Exception($e->getMessage());
		}
		catch(Stripe_Error $e)
		{
			throw new \Exception($e->getMessage());
		}
		catch(\Exception $e)
		{
			throw new \Exception($e->getMessage());
		}
	}

	public function updateCustomer($customer, $options = array())
	{
		try
		{
			$customer = Stripe_Customer::retrieve($customer);

			foreach($options as $option => $value)
			{
				if($value) $customer->{$option} = $value;
			}

			return $customer->save();
		}
		catch(Stripe_CardError $e)
		{
			throw new \Exception($e->getMessage());
		}
		catch(Stripe_Error $e)
		{
			throw new \Exception($e->getMessage());
		}
		catch(\Exception $e)
		{
			throw new \Exception($e->getMessage());
		}
	}

	public function cancel($customer, $subscription)
	{
		try
		{
			return Stripe_Customer::retrieve($customer)->subscriptions->retrieve($subscription)->cancel(array('at_period_end' => true));
		}
		catch(Stripe_CardError $e)
		{
			throw new \Exception($e->getMessage());
		}
		catch(Stripe_Error $e)
		{
			throw new \Exception($e->getMessage());
		}
		catch(\Exception $e)
		{
			throw new \Exception($e->getMessage());
		}
	}
}