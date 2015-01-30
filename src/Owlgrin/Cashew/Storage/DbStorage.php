<?php namespace Owlgrin\Cashew\Storage;

use Owlgrin\Cashew\Exceptions as CashewExceptions;
use Owlgrin\Cashew\Storage\Storage;
use Owlgrin\Cashew\Customer\Customer;
use Owlgrin\Cashew\Subscription\Subscription;
use Owlgrin\Cashew\Invoice\Invoice;
use Owlgrin\Cashew\Invoice\LocalInvoice;
use Owlgrin\Cashew\Card\Card;
use Carbon\Carbon, Config, DB;
use PDOException;

/**
 * The database implementation of Storage
 */
class DbStorage implements Storage {

	/**
	 * Returns the subscription
	 * @param  integer  $id
	 * @param  boolean $byCustomer
	 * @return array
	 */
	public function subscription($id, $byCustomer = false)
	{
		if( ! $id) 
			throw new CashewExceptions\InputException('Cannot fetch subscription');

		return $byCustomer ? $this->subscriptionByCustomer($id) : $this->subscriptionByUser($id);
	}

	/**
	 * Creates a new subscription
	 * @param  string   $userId
	 * @param  Customer $customer
	 * @return integer
	 */
	public function create($userId, Customer $customer)
	{
		try
		{
			$id = DB::table(Config::get('cashew::tables.subscriptions'))->insertGetId(array(
				'user_id'         => $userId,
				'customer_id'     => $customer->id(),
				'subscription_id' => $customer->subscription()->id(),
				'trial_ends_at'   => $customer->subscription()->trialEnd(),
				'plan'            => $customer->subscription()->plan(),
				'quantity'        => $customer->subscription()->quantity(),
				'last_four'       => $customer->card()->lastFour(),
				'card_exp_date'   => $customer->card()->expiryDate(),
				'status'          => $customer->subscription()->status(),
				'created_at'      => DB::raw('now()'),
				'updated_at'      => DB::raw('now()')
			));

			return $id;
		}
		catch(PDOException $e)
		{
			throw new CashewExceptions\DatabaseException;			
		}
	}

	/**
	 * Updates the subscription by user
	 * @param  string   $userId
	 * @param  Customer $customer
	 * @return void
	 */
	public function customer($userId, Customer $customer)
	{
		try
		{
			DB::table(Config::get('cashew::tables.subscriptions'))
				->where('user_id', '=', $userId)
				->update(array(
					'customer_id'   => $customer->id(),
					'last_four'     => $customer->card()->lastFour(),
					'card_exp_date' => $customer->card()->expiryDate(),
					'updated_at'    => DB::raw('now()')
				));
		}
		catch(PDOException $e)
		{
			throw new CashewExceptions\DatabaseException;			
		}
	}

	/**
	 * Updates the subscription by subscription
	 * @param  string       $userId
	 * @param  Subscription $subscription
	 * @return void
	 */
	public function subscribe($userId, Subscription $subscription)
	{
		try
		{			
			DB::table(Config::get('cashew::tables.subscriptions'))
				->where('user_id', '=', $userId)
				->update(array(
					'subscription_id'      => $subscription->id(),
					'trial_ends_at'        => $subscription->trialEnd(),
					'subscription_ends_at' => null,
					'plan'                 => $subscription->plan(),
					'quantity'             => $subscription->quantity(),
					'status'               => $subscription->status(),
					'updated_at'           => DB::raw('now()'),
					'subscribed_at'        => DB::raw('now()')
				));
		}
		catch(PDOException $e)
		{
			throw new CashewExceptions\DatabaseException;			
		}
	}

	/**
	 * Updates a subscription
	 * @param  string   $userId
	 * @param  Customer $customer
	 * @return integer
	 */
	public function update($userId, Customer $customer)
	{
		try
		{
			$subscription = $customer->subscription();

			$id = DB::table(Config::get('cashew::tables.subscriptions'))
				->where('user_id', '=', $userId)
				->update(array(
					'subscription_id'      => $subscription->id(),
					'trial_ends_at'        => $subscription->trialEnd(),
					'subscription_ends_at' => null, // null because update should never be used to stop the subscription
					'plan'                 => $subscription->plan(),
					'quantity'             => $subscription->quantity(),
					'last_four'            => $customer->card()->lastFour(),
					'card_exp_date'        => $customer->card()->expiryDate(),
					'status'               => $subscription->status(),
					'updated_at'           => DB::raw('now()')
				));

			return $id;
		}
		catch(PDOException $e)
		{
			throw new CashewExceptions\DatabaseException;			
		}
	}

	/**
	 * Updates the status of subscription
	 * @param  string $userId
	 * @param  string $status
	 * @return void
	 */
	public function updateStatus($userId, $status)
	{
		try
		{
			DB::table(Config::get('cashew::tables.subscriptions'))
				->where('user_id', '=', $userId)
				->update(array(
					'status'     => $status,
					'updated_at' => DB::raw('now()')
				));
		}
		catch(PDOException $e)
		{
			throw new CashewExceptions\DatabaseException;			
		}
	}

	/**
	 * Resumes a subscription
	 * @param  string $userId
	 * @return integer
	 */
	public function resume($userId)
	{
		try
		{
			$id = DB::table(Config::get('cashew::tables.subscriptions'))
				->where('user_id', '=', $userId)
				->update(array(
					'subscription_ends_at' => null,
					'canceled_at'          => null,
				));

			return $id;
		}
		catch(PDOException $e)
		{
			throw new CashewExceptions\DatabaseException;			
		}
	}

	/**
	 * Cancels a subscription
	 * @param  string       $userId
	 * @param  Subscription $subscription
	 * @return integer
	 */
	public function cancel($userId, Subscription $subscription)
	{
		try
		{
			$id = DB::table(Config::get('cashew::tables.subscriptions'))
				->where('user_id', '=', $userId)
				->update(array(
					'subscription_ends_at' => $subscription->end(),
					'status'      => 'canceled',
					'updated_at'  => DB::raw('now()'),
					'canceled_at' => DB::raw('now()')
				));

			return $id;
		}
		catch(PDOException $e)
		{
			throw new CashewExceptions\DatabaseException;			
		}
	}

	/**
	 * Expires a subscription
	 * @param  string $userId
	 * @return integer
	 */
	public function expire($userId)
	{
		try
		{
			$id = DB::table(Config::get('cashew::tables.subscriptions'))
				->where('user_id', '=', $userId)
				->update(array(
					'status'     => 'expired',
					'updated_at' => DB::raw('now()'),
					'expired_at' => DB::raw('now()')
				));

			return $id;
		}
		catch(PDOException $e)
		{
			throw new CashewExceptions\DatabaseException;			
		}
	}

	/**
	 * Stores an invoice
	 * @param  string  $userId
	 * @param  Invoice $invoice
	 * @return integer
	 */
	public function storeInvoice($userId, Invoice $invoice)
	{
		try
		{
			$id = DB::table(Config::get('cashew::tables.invoices'))
				->insertGetId(array(
					'user_id'         => $userId,
					'customer_id'     => $invoice->customerId(),
					'subscription_id' => $invoice->subscriptionId(),
					'invoice_id'      => $invoice->id(),
					'currency'        => $invoice->currency(),
					'date'            => Carbon::createFromTimestamp($invoice->date(false))->toDateTimeString(),
					'period_start'    => Carbon::createFromTimestamp($invoice->periodStart(false))->toDateTimeString(),
					'period_end'      => Carbon::createFromTimestamp($invoice->periodEnd(false))->toDateTimeString(),
					'total'           => $invoice->total(),
					'subtotal'        => $invoice->subtotal(),
					'discount'        => $invoice->discount(),
					'created_at'      => DB::raw('now()'),
					'updated_at'      => DB::raw('now()')
				));

			return $id;
		}
		catch(PDOException $e)
		{
			throw new CashewExceptions\DatabaseException;			
		}
	}

	/**
	 * Returns the invoices
	 * @param  string  $userId
	 * @param  integer $count
	 * @return array
	 */
	public function getInvoices($userId, $page, $limit)
	{
		try
		{
			$query = DB::table(Config::get('cashew::tables.invoices'))
				->where('user_id', $userId)
				->orderBy('created_at', 'DESC');
			
			$invoices = $query->get();	
			$paginated_invoices = $query->skip($limit*($page-1))->take($limit)->get();
			
			foreach($paginated_invoices as $key => $invoice) 
			{
				$invoice['subtotal'] = (float) $invoice['subtotal'];
				$invoice['total']    = (float) $invoice['total'];
				$invoice['discount'] = (float) $invoice['discount'];
				
				$paginated_invoices[$key] = new LocalInvoice($invoice);
			}
			
			return ['data' => $paginated_invoices, 'meta' => ['total' => count($invoices)]];
		}
		catch(PDOException $e)
		{
			throw new CashewExceptions\DatabaseException;			
		}
	}

	/**
	 * Returns the invoice
	 * @param  string  $userId
	 * @param  string $invoiceId
	 * @return array
	 */
	public function getInvoice($userId, $invoiceId)
	{
		try
		{
			$invoice = DB::table(Config::get('cashew::tables.invoices'))
				->where('invoice_id', $invoiceId)
				->where('user_id', $userId)
				->first();

			$invoice['subtotal'] = (float) $invoice['subtotal'];
			$invoice['total']    = (float) $invoice['total'];
			$invoice['discount'] = (float) $invoice['discount'];

			return new LocalInvoice($invoice);
		}
		catch(PDOException $e)
		{
			throw new CashewExceptions\DatabaseException;			
		}
	}

	/**
	 * Returns the last invoice
	 * @param  string  $userId
	 * @return array
	 */
	public function getLastInvoice($userId)
	{
		try
		{
			$invoice = DB::table(Config::get('cashew::tables.invoices'))
				->where('user_id', $userId)
				->orderBy('created_at', 'DESC')
				->first();
			
			$invoice['subtotal'] = (float) $invoice['subtotal'];
			$invoice['total']    = (float) $invoice['total'];
			$invoice['discount'] = (float) $invoice['discount'];

			return new LocalInvoice($invoice);
		}
		catch(PDOException $e)
		{
			throw new CashewExceptions\DatabaseException;			
		}
	}

	/**
	 * Returns expiring card subscriptions
	 * @param  integer $month
	 * @param  integer $year
	 * @return array
	 */
	public function getSubscriptionsWithExpiringCard($dates)
	{
		try
		{
			return DB::table(Config::get('cashew::tables.subscriptions'))
				->where('status', '<>', 'canceled')
				->whereBetween('card_exp_date', $dates)
				->get();
		}
		catch(PDOException $e)
		{
			throw new CashewExceptions\DatabaseException;			
		}
	}

	/**
	 * Returns the subscription by user
	 * @param  string $userId
	 * @return array
	 */
	private function subscriptionByUser($userId)
	{
		try
		{
			return DB::table(Config::get('cashew::tables.subscriptions'))
				->where('user_id', '=', $userId)
				->first();
		}
		catch(PDOException $e)
		{
			throw new CashewExceptions\DatabaseException;			
		}
	}

	/**
	 * Returns the subscription by customer
	 * @param  string $customerId
	 * @return array
	 */
	private function subscriptionByCustomer($customerId)
	{
		try
		{
			return DB::table(Config::get('cashew::tables.subscriptions'))
				->where('customer_id', '=', $customerId)
				->first();
		}
		catch(PDOException $e)
		{
			throw new CashewExceptions\DatabaseException;			
		}
	}
}