<?php namespace Owlgrin\Cashew\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Cashew;

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

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		try
		{
			$user = $this->argument('user');
			$plan = $this->argument('plan');

			$this->info('Checking whether user is subscribed or not....');

			if(Cashew::user($user)->getSubscription())
			{
				$this->info('Starting switching....');

				Cashew::toPlan($plan);

				$this->info('Switched successfully!');
			}
		}
		catch(\Exception $e)
		{
			$this->error($e);
		}
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('user', InputArgument::REQUIRED, 'Unique identifier of the user whose subscription plan to be switched'),
			array('plan', InputArgument::REQUIRED, 'Stripe plan to which the user to be switched')
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
			// array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
		);
	}
}