<?php namespace Owlgrin\Cashew\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Owlgrin\Cashew\Storage\Storage;
use Cashew;
use Config;

/**
 * Command to expire users who ended their grace period
 */
class CashewSwitchPlanCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'cashew:switch-plan';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command to switch an user\'s subscription plan in Cashew(Stripe)';
	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */

	protected $storage;

	public function __construct(Storage $storage)
	{
		parent::__construct();
		$this->storage = $storage;
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		try
		{
			$plan = $this->argument('plan');
			$subscriptions = $this->getSubscriptions();

			foreach($subscriptions as $index => $subscription)
			{
				$this->info('Starting switching user with ID: '. $subscription['user_id'] .' to '. $plan . ' plan.');

				Cashew::user($subscription['user_id'])->toPlan($plan);

				$this->info('User with ID: '. $subscription['user_id'] .' switched successfully to '. $plan . ' plan.');
			}
		}
		catch(\Exception $e)
		{
			$this->error($e);
		}
	}

	protected function getSubscriptions()
	{
		$user = $this->argument('user');

		if($user === '_ALL')
			return $this->storage->subscriptions();

		return [$this->storage->subscription($user)];
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('user', InputArgument::REQUIRED, 'Unique identifier of the user whose subscription plan to be switched.'),
			array('plan', InputArgument::REQUIRED, 'Stripe plan to which the user to be switched.')
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
			// array('user', null, InputOption::VALUE_OPTIONAL, 'Unique identifier of the user whose subscription plan to be switched.', null),
		);
	}
}