<?php namespace Owlgrin\Cashew\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Cashew;

/**
 * Command to expire users who ended their grace period
 */
class CashewExtendTrialCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'cashew:extend:trial';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command to extend the trial period an user';
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
			$this->info('Starting extending trial....');

			$user = $this->argument('user');
			
			$days = $this->option('trial-days');
			
			$options = array('trial_end' => $days);
			
			Cashew::user($user)->resume($options);
			
			$this->info('Trial exteded successfully!');
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
			array('user', InputArgument::REQUIRED, 'Unique identifier of the user whose trial period to be extended'),
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
			array('trial-days', null, InputOption::VALUE_OPTIONAL, 'Number of days by which the trial period to be extended', 15),
		);
	}
}