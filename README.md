## Cashew
======

Cashew allows you to integrate Stripe subscription billing in the most easiet way. We handle all the headache in maintaining the edge cases in the the subscription billing, while exposing an easy API to consume.

### Installation

To install the package, include the following in your `composer.json`.

```php
"owlgrin/cashew": "dev-master"
```

And then include the following service provider in your `app.php`.

```php
...
'Owlgrin\Cashew\CashewServiceProvider'
...
```

And lastly add the following in the facades list in the same `app.php` file to easily use the package.

```php
...
'Cashew' => 'Owlgrin\Cashew\CashewFacade',
...
```

### Usage

Cashew works by mapping everything about a user to the primary key of that users in your app. To setup everything, you would need to migrate few tables, which you can do with the following command.

`php artisan migration:publish --package owlgrin/cashew`

#### New Subscription

To create a new subscription, you can simply pass the user identifier (usually the primary key) along with an associative array with keys 'card', 'email', etc. (all optional).

```php
Cashew::create(Auth::user()->id, array('trial_end' => 'now', 'coupon' => 'earlybird'));
```

Now, once there exists a subscription for the user, you can register that for each request using `user` method. (Probably, when user logs in.)

```php
if(Auth::attempt($username, $password))
{
	Cashew::user(Auth::user()->id);
}
```

Once set, you can access the different methods on the subscription using the following methods.

#### Updating the Card Details

You can update the card details using the `card` method.

```php
Cashew::card('tok_o48H37h8eh');
```

If you want to update other details while updating card, like 'trial_end', you can do it through the second parameter.

```php
Cashew::card('tok_o48H37h8eh', array('trial_end' => 'now'));
```

#### Applying a Coupon

You can apply a coupon to a subscription using the 'coupon' method.

```php
Cashew::coupon('20OFF');
```

#### Changing the Plan

Swapping the plans in a subscription billing is not a easy job. There are many things to be taken care of. We do this for you and you simply can change the plan of a subscription like this.

```php
Cashew::toPlan('premium');
```

By default, Cashew prorates the plan change, but you can do it via the second parameter.

```php
Cashew::toPlan('premium', false); // no prorate
```

If there is some trial period left in the subscription when the plan is changed, by default, we will maintain the trial period. You can force Cashew to stop the trial period immediately via the third parameter.

```php
Cashew::toPlan('premium', true, false); // stop trial period immediately
```

#### Working with Quantity

You may have requirement to charge your users on each unit of the services they use. For example, you may charge them on 'per user' basis. Cashew makes it easy to work with quantity.

You can increment a quantity using the 'increment' method.

```php
Cashew::increment();
```

Similarly, 'decrement' method allows you to decrease the quantity by one.

```php
Cashew::decrement();
```

You can also pass the quantity as the first parameter to these methods to override the default value of one. The following code increments the quantity by three.

```php
Cashew::increment(3);
```

> Same is valid for 'decrement' method too.

#### Updating a Subscription

Although, we have already made helper methods like `toPlan`, `coupon`, `card` to allow you to make specific updates easily. Still, if you would like to use the raw update method to update your subscription on your own,you can use the `update` method.

```php
Cashew::update(array('plan' => 'new-plan', 'coupon' => 'yipee'));
```

The method accepts just one argument - an associative array - with all the options that Strip API accepts.

#### Canceling a Subsription

You can cancel a user's subscription using the `cancel` method.

```php
Cashew::cancel();
```

User will not be charged until you resume his/her subscription again. However, this method will cancel the subscription at the end of the current subscription period. Thus, the user will now be on grace period.

If you want to cancel the subscription right away, use `cancelNow` method instead.

```php
Cashew::cancelNow();
```

#### Resuming a subscription

In every subscription application, subscriptions expire. Often, these are due to temporary reasons like trial end, and they are to be resumed later. Cashew provides `resume` method to easily do so. There are many caveats in resuming a subscription and cashew takes care of all of them for you.

```php
Cashew::resume();
```

This was the simplest use. You may require to pass some options whenr resuming, which can passed as associative array, like so.

```php
Cashew::resume(array('plan' => 'premium', 'quantity' => 3));
```

Many times, you will need to resume the subscription after it has expired due to end of trial period. Often, you will ask for your user's card to resume the subscription. You can pass in the card details as the second argument to `resume` method.

```php
Cashew::resume(array('plan' => 'premium'), 'tok_dff38rm347gBYF7');
```

#### Expiring Subscriptions

You may sometimes want t expire the subscriptions manually based upon your business domain. For instance, you might have a cron job running, which will expire the subscriptions that are beyond their trial periods. For such instances, you can use the `expire` method.

```php
Cashew::expire();
```

Sometimes, you may want to expire your user's account based on the Stripe's customer ID for the user. Then, `expireCustomer` method is just for the same purpose.

```php
Cashew::expireCustomer($customerId);
```

#### Checking is user has provided card

You may run a business where you ask for card upfront or not. If you want to check if the user has card associated with his subcription, you can do it using the `hasCard` method.

```php
if($user->birthday == Carbon\Carbon::today() && Cashew::hasCard())
{
	// send birthday cake
}
```

#### Get the invoices of user

It is always recommended to show the payment history of your users in their billing section. To help you with this, 'invoices' method will fetch you all the previous invoices that are paid by the user.

```php
Cashew::invoices();
```

By default, it will fetch the invoices details that are saved locally in your database. But, you may need more information for each invoice, and thus want to fetch the details through API. To do so, pass the first parameter as `false` to this method.

```php
// fetch from API
Cashew::invoices(false);
```

Also, by default, it will fetch the lastest 10 invoices. In case, you want to fetch all the invoices, pass the number as the second parameter.

```php
// fetch 25 invoices
Cashew::invoices(true, 25);
```

#### Getting upcoming invoice for a user

If is always recommened that you show your users, their upcoming invoices so that there won't be any surprises for the when they will be billed.

Cashew provides a handy `nextInvoice` method to fetch the upcoming invoice for a subscription.

```php
Cashew::nextInvoice();
```

#### Getting status of the subscription

You may need to do different action depending upon the status of the subscription for a user. To let you easily find the status of the subscription, you may use `status` method.

```php
if(Cashew::status() === 'active')
{
	$user->sendGift();
}
```

#### Helper status determiners

Cashew provides you with some helper methods to determine the subscription for the user.

**Active**

To check if user has an active subscription. Active subscription means that the user is allowed to be considered as a paid customer. Precisely, the method returns true if either of these is true:

	- is the user, super user?
	- is he on trial?
	- is he on grace period?
	- does he have an active subscription?

```php
if( ! Cashew::active())
{
	Redirect::to(...);
}
```

## Documentation about these, coming soon

#### inactive
#### isSuper
#### hasCard
#### onTrial
#### onGrace
#### expired
#### subscribed
#### canceled
#### onPlan

### Contributing To Cashew

Contribution guidelines coming soon.

### License

Cashew is an open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)