<?php

return array(
	/**
	 * The API keys of your Stripe account
	 */
	'keys' => array(
		/**
		 * This is the secret key of your Stripe account. Please take care that
		 * this key should never be revealed publicly.
		 */
		'secret' => 'your-secret-key-here',
		/**
		 * This is the publishable key of your Stripe account. This key can be used
		 * on public pages, such as javascript code.
		 */
		'publishable' => 'your-publishable-key-here'
	),
	/**
	 * The following options tell Cashew to work seamlessly with
	 * the the storage. We use this SQL tables to record certain
	 * information about the subscriptions.
	 */
	'tables' => array(
		/**
		 * This table is required to keep track of the
		 * various subscriptions of our users.
		 */
		'subscriptions' => '_cashew_subscriptions',
		/**
		 * This table is required to store a copy of invoices
		 * locally to make things faster.
		 */
		'invoices' => '_cashew_invoices'
	),
	/**
	 * This option tells how many attempts to be made before,
	 * expiring the user. By default, it is set to a sensible
	 * default of 3 times.
	 */
	'attempts' => 3,

	/**
	 * Default plan to which the newly created user should be subscribed to.
	 */
	'plan' => 'plan-name'
);