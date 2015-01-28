<?php namespace Owlgrin\Cashew\Commands;

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
	protected $name = 'cashew:ping:user:expiring:card';

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
			
			foreach($subscriptions as $index => $subscription) 
			{
				if($this->isRequiredToPing($subscription))
					$this->pingUser($subscription);
			}

			$this->info('Mailed successfully to required users');
		} 
		catch(\Exception $e) 
		{
			$this->error($e);
		}
	}

	protected function getSubscriptions()
	{
		$user = $this->option('user');

		if(is_null($user))
		{
			return DB::table(Config::get('cashew::tables.subscriptions'))
				->where('last_four', '<>', 'null')
				->get();			
		}

		return DB::table(Config::get('cashew::tables.subscriptions'))
				->where('user_id', $user)
				->where('last_four', '<>', 'null')
				->get();
	}

	protected function pingUser($subscription)
	{
		$intervals = explode(',', $this->option('intervals'));

		$daysLeft = Carbon::createFromFormat('Y-m-d', $subscription['card_exp_date'])->diffInDays(Carbon::today());

		if(in_array($daysLeft, $intervals))
		{
			$user = App::make('App\Repos\User\UserRepo')->find($subscription['user_id']);
			$user['days_left'] = $daysLeft;
			
			$this->info('Sending mail to User with ID: ' . $subscription['user_id']);
			
			App::make('App\Mailers\UserMailer')->to($user)->cardExpiring(['user' => $user])->send();
		}
	}

	protected function isRequiredToPing($subscription)
	{
		$expireDate = Carbon::createFromFormat('Y-m-d', $subscription['card_exp_date']);
		$duration = Carbon::today()->addDays(60);
		
		if($expireDate->lte($duration)) return true;

		return false;
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			// array('user', InputArgument::REQUIRED, 'Unique identifier of the user to whom you want to ping about expiring card')
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
			array('intervals', null, InputOption::VALUE_OPTIONAL, '(Array) Intervals on which a required user to be pinged', null),
		);
	}
}