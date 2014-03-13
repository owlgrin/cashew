<?php namespace Owlgrin\Cashew\Gateway;

use Stripe_Customer, Stripe_CardError, Stripe_Error;
use Owlgrin\Cashew\Customer\StripeCustomer;
use Owlgrin\Cashew\Subscription\StripeSubscription;

class StripeGateway implements Gateway {

	public function create($card, $description = '')
	{
		try
		{
			$customer = Stripe_Customer::create(array(
				'card' => $card,
				'description' => $description
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

	public function update($customer, $options = array())
	{
		try
		{
			$subscription = Stripe_Customer::retrieve($customer)
				->updateSubscription($options);

			return new StripeCustomer(Stripe_Customer::retrieve($customer));
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

	public function cancel($customer, $atPeriodEnd = true)
	{
		try
		{
			$subscription = Stripe_Customer::retrieve($customer)
				->cancelSubscription(array('at_period_end' => $atPeriodEnd));

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