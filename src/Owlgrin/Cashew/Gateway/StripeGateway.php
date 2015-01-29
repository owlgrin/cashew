<?php namespace Owlgrin\Cashew\Gateway;

use Stripe_Customer, Stripe_Invoice, Stripe_InvoiceItem, Stripe_Event;
use Stripe_ApiConnectionError, Stripe_InvalidRequestError, Stripe_CardError, Stripe_Error;
use Owlgrin\Cashew\Customer\StripeCustomer;
use Owlgrin\Cashew\Subscription\StripeSubscription;
use Owlgrin\Cashew\Invoice\StripeInvoice;
use Owlgrin\Cashew\Event\StripeEvent;
use Owlgrin\Cashew\Exceptions\Exception, Owlgrin\Cashew\Exceptions\CardException, Owlgrin\Cashew\Exceptions\NetworkException, Owlgrin\Cashew\Exceptions\InputException;

/**
 * The Stripe implementation of Gateway
 */
class StripeGateway implements Gateway {

	/**
	 * Creates the subscription
	 * @param  array $options
	 * @return Customer
	 */
	public function create($options)
	{
		try
		{
			$customer = Stripe_Customer::create($options);

			return new StripeCustomer($customer);
		}
		catch(Stripe_CardError $e)
		{
			throw new CardException;
		}
		catch(Stripe_InvalidRequestError $e)
		{
			throw new InputException;
		}
		catch(Stripe_ApiConnectionError $e)
		{
			throw new NetworkException;
		}
		catch(Stripe_Error $e)
		{
			throw new Exception;
		}
		catch(\Exception $e)
		{
			throw new \Exception($e->getMessage());
		}
	}

	/**
	 * Updates a subscription
	 * @param  string $customer
	 * @param  array  $options
	 * @return Customer
	 */
	public function update($customer, $options = array())
	{
		try
		{
			$subscription = Stripe_Customer::retrieve($customer)
				->updateSubscription($options);

			return new StripeCustomer(Stripe_Customer::retrieve($customer));
		}
		catch(Stripe_CardError $e)
		{
			throw new CardException;
		}
		catch(Stripe_InvalidRequestError $e)
		{	
			throw new InputException;
		}
		catch(Stripe_ApiConnectionError $e)
		{
			throw new NetworkException;
		}
		catch(Stripe_Error $e)
		{
			throw new Exception;
		}
		catch(\Exception $e)
		{
			throw new \Exception($e->getMessage());
		}
	}

	/**
	 * Cancels the subscription
	 * @param  string  $customer
	 * @param  boolean $atPeriodEnd
	 * @return Subscription
	 */
	public function cancel($customer, $atPeriodEnd = true)
	{
		try
		{
			$subscription = Stripe_Customer::retrieve($customer)
				->cancelSubscription(array('at_period_end' => $atPeriodEnd));

			return new StripeSubscription($subscription);
		}
		catch(Stripe_InvalidRequestError $e)
		{
			throw new InputException;
		}
		catch(Stripe_ApiConnectionError $e)
		{
			throw new NetworkException;
		}
		catch(Stripe_Error $e)
		{
			throw new Exception;
		}
		catch(\Exception $e)
		{
			throw new \Exception($e->getMessage());
		}
	}

	/**
	 * Returns the invoices for the subscription
	 * @param  string  $customer
	 * @param  integer $count
	 * @return array
	 */
	public function invoices($customer, $count = 10)
	{
		try
		{
			$stripeInvoices = Stripe_Customer::retrieve($customer)->invoices(compact('count'));

			$invoices = array();
			foreach($stripeInvoices['data'] as $invoice)
			{
				$invoices[] = new StripeInvoice($invoice);
			}

			return $invoices;
		}
		catch(Stripe_InvalidRequestError $e)
		{
			throw new InputException;
		}
		catch(Stripe_ApiConnectionError $e)
		{
			throw new NetworkException;
		}
		catch(Stripe_Error $e)
		{
			throw new Exception;
		}
		catch(\Exception $e)
		{
			throw new \Exception($e->getMessage());
		}
	}

	/**
	 * Returns the upcoming invoice
	 * @param  string $customer
	 * @return Invoice
	 */
	public function nextInvoice($customer)
	{
		try
		{
			$invoice = Stripe_Invoice::upcoming(compact('customer'));
			
			return new StripeInvoice($invoice);
		}
		catch(Stripe_InvalidRequestError $e)
		{
			throw new InputException;
		}
		catch(Stripe_ApiConnectionError $e)
		{
			throw new NetworkException;
		}
		catch(Stripe_Error $e)
		{
			throw new Exception;
		}
		catch(\Exception $e)
		{
			throw new \Exception($e->getMessage());
		}
	}

	/**
	 * Returns the event from the id
	 * @param  string $event
	 * @return Event
	 */
	public function event($event)
	{
		try
		{
			$event = Stripe_Event::retrieve($event);

			return new StripeEvent($event);
		}
		catch(Stripe_InvalidRequestError $e)
		{
			throw new InputException;
		}
		catch(Stripe_ApiConnectionError $e)
		{
			throw new NetworkException;
		}
		catch(Stripe_Error $e)
		{
			throw new Exception;
		}
		catch(\Exception $e)
		{
			throw new \Exception($e->getMessage());
		}
	}

	/**
	 * Add an invoice item.
	 * @param  array  $item
	 * @return Invoice
	 */
	public function invoiceItem($item)
	{
		try
		{
			return Stripe_InvoiceItem::create($item);
		}
		catch(Stripe_InvalidRequestError $e)
		{
			throw new InputException;
		}
		catch(Stripe_ApiConnectionError $e)
		{
			throw new NetworkException;
		}
		catch(Stripe_Error $e)
		{
			throw new Exception;
		}
		catch(\Exception $e)
		{
			throw new \Exception($e->getMessage());
		}
	}
}