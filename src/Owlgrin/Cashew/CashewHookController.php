<?php namespace Owlgrin\Cashew;

use Owlgrin\Cashew\Gateway\Gateway;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use App;

class CashewHookController extends Controller {

	/**
	 * The Gateway instance
	 * @var Owlgrin\Cashew\Gateway\Gateway
	 */
	protected $gateway;

	/**
	 * The constructor
	 * @param Gateway $gateway
	 */
	public function __construct(Gateway $gateway)
	{
		$this->gateway = $gateway;
	}

	/**
	 * Hash to map type of hooks with their handlers
	 * @var array
	 */
	private $hooks = array(
		'invoice.payment_failed' => 'InvoiceFailHook',
		'invoice.payment_succeeded' => 'InvoiceSuccessHook',
		'customer.subscription.updated' => 'SubscriptionUpdateHook'
	);

	/**
	 * Handles the event
	 * @return Illuminate\Http\Response
	 */
	public function handle()
	{
		try
		{
			$payload = $this->getPayload();

			$event = $this->gateway->event($payload['id']);
			if($this->canBeHandled($event->type()))
			{
				$hook = $this->getHook($event->type());
				$hook->handle($event);
			}

			return Response::make('Hook handled successfully', 200);
		}
		catch(\Exception $e)
		{
			return Response::make('Hook handled unsuccessfully: ' . $e->getMessage(), 400);
		}
	}

	/**
	 * Checks if the event can be handled
	 * @param  string $type The type of event
	 * @return boolean      Can the event be handled or not?
	 */
	protected function canBeHandled($type)
	{
		return array_key_exists($type, $this->hooks);
	}

	/**
	 * Returns the instance of the handler for the event
	 * @param  string $type The type of event
	 * @return Owlgrin\Cashew\Hooks\Hook       The handler instance
	 */
	protected function getHook($type)
	{
		return App::make('Owlgrin\Cashew\Hooks\\' . $this->hooks[$type]);
	}

	/**
	 * Returns the parsed payload from the webhook request
	 * @return array
	 */
	protected function getPayload()
	{
		return (array) json_decode(Request::getContent(), true);
	}
}