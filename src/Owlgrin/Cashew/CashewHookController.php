<?php namespace Owlgrin\Cashew;

use Owlgrin\Cashew\Gateway\Gateway;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use App;

class CashewHookController extends Controller {

	private $hooks = array(
		'invoice.payment_failed' => 'InvoiceFailHook',
		'invoice.payment_succeeded' => 'InvoiceSuccessHook'
	);

	public function handle()
	{
		$payload = $this->getPayload();

		$event = $this->gateway->event($payload['id']);
		$hook = $this->getHook($event['type']);

		$hook->handle($event);
	}

	protected getHook($type)
	{
		return App::make($this->hooks[$type]);
	}

	protected function getPayload()
	{
		return (array) json_decode(Request::getContent(), true);
	}
}