<?php namespace Owlgrin\Cashew\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Cashew;

/**
 * Command to expire users who ended their grace period
 */
class CashewCancelCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'cashew:cancel';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command to cancel an user Cashew(Stripe) subscription';
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
			$this->info('Starting canceling....');

			$user = $this->argument('user');

			Cashew::user($user)->cancel();

			$this->info('Canceled successfully!');
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
			array('user', InputArgument::REQUIRED, 'Unique identifier of the user whose subscription to be canceled')
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