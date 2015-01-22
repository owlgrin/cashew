<?php namespace Owlgrin\Cashew\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Cashew;

/**
 * Command to expire users who ended their grace period
 */
class CashewSubscribeCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'cashew:subscribe';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command to subscribe an user in Cashew(Stripe)';
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
			$this->info('Starting subscribing....');

			$user = $this->argument('user');
			$plan = $this->argument('plan');
			
			Cashew::create($user, array(
				'description' => '[Registered] Customer with User ID: ' . $user,
				'plan' => $plan,
				'quantity' => '1'
			));

			$this->info('Subscribed successfully!');
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
			array('user', InputArgument::REQUIRED, 'Unique identifier of the user to be subscribed'),
			array('plan', InputArgument::REQUIRED, 'Stripe plan to which the user to be subscribed')
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