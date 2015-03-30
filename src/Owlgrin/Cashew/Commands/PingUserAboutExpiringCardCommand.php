<?php namespace Owlgrin\Cashew\Commands;

use Illuminate\Support\Facades\Event as IlluminateEvent;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Cashew, DB, Config;
use Carbon\Carbon, App;

/**
 * Command to expire users who ended their grace period
 */
class PingUserAboutExpiringCardCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'cashew:ping-user-expiring-card';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command to update the user about expiring card by sending mail';
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
			$this->info('Starting mailing....');

			$subscriptions = $this->getSubscriptions();
			$intervals = $this->argument('intervals');

			foreach($subscriptions as $index => $subscription)
			{
				if($this->isRequiredToPing($subscription['card_exp_date'], $intervals))
					$this->pingUser($subscription);
			}

			$this->info('Mailed successfully to required users');
		}
		catch(\Exception $e)
		{
			$this->error($e);
		}
	}

	/**
	 * Returns the list of subscriptions based on input
	 *
	 * @return array
	 */
	protected function getSubscriptions()
	{
		// If no user passed, get all subscriptions
		if(is_null($user = $this->option('user')))
		{
			return DB::table(Config::get('cashew::tables.subscriptions'))
				->where('last_four', '<>', 'null')
				->get();
		}

		// Otherwise, return the subscription for that particular user only
		return DB::table(Config::get('cashew::tables.subscriptions'))
				->where('user_id', $user)
				->where('last_four', '<>', 'null')
				->get();
	}

	protected function pingUser($subscription)
	{
		$daysLeft = $this->getDaysDiffFromToday($subscription['card_exp_date']);

		$this->info('Pinging about expiring card to user with ID: ' . $subscription['user_id']);

		IlluminateEvent::fire('cashew.card.expiring', array($subscription['user_id'], $daysLeft));
	}

	protected function isRequiredToPing($expiryDate, $intervals)
	{
		$daysLeft = $this->getDaysDiffFromToday($expiryDate);

		return in_array($daysLeft, $intervals);
	}

	private function getDaysDiffFromToday($date)
	{
		return Carbon::createFromFormat('Y-m-d', $date)->diffInDays(Carbon::today());
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('intervals', InputArgument::IS_ARRAY, '(Array) Intervals on which a required user to be pinged')
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			array('user', null, InputOption::VALUE_OPTIONAL, 'Unique identifier of the user to whom you want to ping about expiring card', null),
		);
	}
}