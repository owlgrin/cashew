<?php namespace Owlgrin\Cashew\Gateway;

use Stripe_Customer, Stripe_CardError, Stripe_Error;
use Owlgrin\Cashew\Customer\StripeCustomer;
use Owlgrin\Cashew\Subscription\StripeSubscription;

class StripeGateway implements Gateway {
	public function create($id, $meta = array())
	{
		try
		{
			$customer = Stripe_Customer::create(array(
				'description' => 'Customer with User ID: ' . $id,
				'metadata' => $meta
			));

			return new StripeCustomer($customer);
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

	public function subscribe($customer, $card, $plan, $options = array())
	{
		try
		{
			Stripe_Customer::retrieve($customer)
				->subscriptions->create(array(
					'card' => $card,
					'plan' => $plan,
					'trial_end' => $options['trial_end'],
					'quantity' => $options['quantity'],
					'coupon' => $options['coupon']
				));

			return new StripeCustomer(Stripe_Customer::retrieve($customer));
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

			return new StripeSubscription($subscription->save());
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

			return new StripeCustomer($customer->save());
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
			$subscription = Stripe_Customer::retrieve($customer)->subscriptions->retrieve($subscription)->cancel(array('at_period_end' => true));

			return new StripeSubscription($subscription);
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