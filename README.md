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

### Contributing To Cashew

Contribution guidelines coming soon.

### License

Cashew is an open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)