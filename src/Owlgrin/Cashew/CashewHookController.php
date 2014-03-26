<?php namespace Owlgrin\Cashew;

use Owlgrin\Cashew\Gateway\Gateway;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use App;

class CashewHookController extends Controller {

	protected $gateway;

	public function __construct(Gateway $gateway)
	{
		$this->gateway = $gateway;
	}

	private $hooks = array(
		'invoice.payment_failed' => 'InvoiceFailHook',
		'invoice.payment_succeeded' => 'InvoiceSuccessHook',
		'customer.subscription.updated' => 'SubscriptionUpdateHook'
	);

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

	protected function canBeHandled($type)
	{
		return array_key_exists($type, $this->hooks);
	}

	protected function getHook($type)
	{
		return App::make('Owlgrin\Cashew\Hooks\\' . $this->hooks[$type]);
	}

	protected function getPayload()
	{
		return (array) json_decode(Request::getContent(), true);
	}
}