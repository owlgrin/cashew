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



### Contributing To Cashew

Contribution guidelines coming soon.

### License

Cashew is an open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)