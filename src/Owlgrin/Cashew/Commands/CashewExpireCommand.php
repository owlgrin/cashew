<?php namespace Owlgrin\Cashew\Commands;

use Illuminate\Support\Facades\Event as IlluminateEvent;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use DB, Config;

/**
 * Command to expire users who ended their grace period
 */
class CashewExpireCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'cashew:expire';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command to convert canceled users into expired when grace period ends';
	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		try
		{

			$this->info('Expiring...');

			$this->expire();

			$this->info('Expired!');
		}
		catch(PDOException $e)
		{
			$this->error($e);
		}
	}

	/**
	 * The main method which does all the work
	 * @return void
	 */
	private function expire()
	{
		// get all the subscriptions which ends today
		$subscriptions = DB::table(Config::get('cashew::tables.subscriptions'))
			->where('status', '=', 'canceled')
			->where(DB::raw('date(subscription_ends_at)'), '=', DB::raw('curdate()'))
			->get();

		foreach($subscriptions as $index => $subscription)
		{
			// mark them as expired
			DB::table(Config::get('cashew::tables.subscriptions'))
				->where('id', '=', $subscription['id'])
				->update(array(
					'status' => 'expired',
					'expired_at' => DB::raw('now()')
				));

			// and fire an event to handle the event by the app accordingly
			IlluminateEvent::fire('cashew.user.expire', array($subscription['user_id']));
		}
	}
}