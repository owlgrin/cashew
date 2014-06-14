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

### Contributing To Cashew

Contribution guidelines coming soon.

### License

Cashew is an open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)