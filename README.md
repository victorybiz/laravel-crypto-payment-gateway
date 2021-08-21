# Laravel Crypto Payment Gateway

[GoUrl.io](https://gourl.io) Crypto Payment Gateway for Laravel.  

[![Latest Version on Packagist](https://img.shields.io/packagist/v/victorybiz/laravel-crypto-payment-gateway.svg?style=flat-square)](https://packagist.org/packages/victorybiz/laravel-crypto-payment-gateway)
[![Total Downloads](https://img.shields.io/packagist/dt/victorybiz/laravel-crypto-payment-gateway.svg?style=flat-square)](https://packagist.org/packages/victorybiz/laravel-crypto-payment-gateway)
![GitHub Actions](https://github.com/victorybiz/laravel-crypto-payment-gateway/actions/workflows/main.yml/badge.svg)

### DEMO PREVIEW
1. Payment Not Received (Awaiting Payment)
   
![preview](https://github.com/victorybiz/laravel-crypto-payment-gateway/raw/main/demo.gif) 

2. Payment Received (Correct Amount)
   
![preview](https://github.com/victorybiz/laravel-crypto-payment-gateway/raw/main/demo2.gif) 

3. Payment Received (Unrecognized)
   
![preview](https://github.com/victorybiz/laravel-crypto-payment-gateway/raw/main/demo3.gif) 

## Table of Contents
- [Laravel Crypto Payment Gateway](#laravel-crypto-payment-gateway)
    - [DEMO PREVIEW](#demo-preview)
  - [Table of Contents](#table-of-contents)
  - [Installation](#installation)
  - [Requirements](#requirements)
    - [Dependencies](#dependencies)
  - [Configuration](#configuration)
        - [Define payment routes](#define-payment-routes)
        - [Define the public key and private keys in environment file](#define-the-public-key-and-private-keys-in-environment-file)
      - [Config Options](#config-options)
  - [Usage](#usage)
    - [Payment Data Submission](#payment-data-submission)
        - [Usage with Form Submit](#usage-with-form-submit)
        - [Usage with AJAX Request](#usage-with-ajax-request)
        - [Usage with Session Redirect (through controller, Livewire component or anywhere in your application)](#usage-with-session-redirect-through-controller-livewire-component-or-anywhere-in-your-application)
    - [The Callback](#the-callback)
        - [Callback Controller](#callback-controller)
        - [Callback Route](#callback-route)
        - [IPN (Instant Payment Notification)](#ipn-instant-payment-notification)
    - [Eloquent Model for `crypto_payments` table](#eloquent-model-for-crypto_payments-table)
  - [Advanced Usage](#advanced-usage)
    - [Instance of GoUrl.io PHP Class API (`cryptobox.class.php`)](#instance-of-gourlio-php-class-api-cryptoboxclassphp)
  - [Resources](#resources)
  - [Testing](#testing)
  - [Changelog](#changelog)
  - [Contributing](#contributing)
    - [Security](#security)
  - [Credits](#credits)
  - [License](#license)
  - [Laravel Package Boilerplate](#laravel-package-boilerplate)

<br>

<a name="installation"></a>

## Installation

You can install the package via composer:

```bash
composer require victorybiz/laravel-crypto-payment-gateway
```

Next, you should publish the configuration, migration and asset files using the `vendor:publish` Artisan command. The configuration, migration and asset files will be placed in your application's `config`, `database/migrations` and `public/vendor` directory respectively:

```bash
php artisan vendor:publish --provider="Victorybiz\LaravelCryptoPaymentGateway\LaravelCryptoPaymentGatewayServiceProvider"
```

<a name="requirements"></a>

## Requirements
This package is create a laravel wrapper on the [GoUrl.io](https://gourl.io)'s [CryptoAPI Payment Gatway](https://github.com/cryptoapi/Payment-Gateway).
* Register for Free or Login on the [GoUrl.io website](https://gourl.io).
* Create [new payment box](https://gourl.io/editrecord/coin_boxes/0).
* Ensure to specify your External wallet address and Callback Url
* Obtain the Public Key and Private of each coin's payment box you want to support.

<a name="core-dependencies"></a>

### Dependencies
The `compact` and `standard` box style uses **Alpinejs** and **TailwindCSS**. While the `gourl-boostrap` uses assets provided by [GoUrl.io](https://gourl.io). You do not need to install any of the dependencies, the package uses the CDN version of dependencies.

<a name="configuration"></a>

## Configuration
You need create the following files;
* A `PaymentController` with a `callback` method. [Learn more below](#the-callback).
* A static class method anywhere in your application to hook **IPN (Instant Payment Notification)**. Preferably you can just define the static method `public static function ipn($cryptoPaymentModel, $payment_details, $box_status){..}` in the same `PaymentController` for easy code management. [Learn more below](#the-ipn)
* Define the payment routes.
* Define the public key and private keys in environment file


##### Define payment routes
```php 
// routes/web.php

// You can protect the 'payments.crypto.pay' route with `auth` middleware to allow access by only authenticated user
Route::match(['get', 'post'], '/payments/crypto/pay', Victorybiz\LaravelCryptoPaymentGateway\Http\Controllers\CryptoPaymentController::class)
                ->name('payments.crypto.pay');

// You you need to create your own callback controller and define the route below
// The callback route should be a publicly accessible route with no auth
// However, you may also exclude the route from CSRF Protection by adding their URIs to the $except property of the VerifyCsrfToken middleware.
Route::post('/payments/crypto/callback', [App\Http\Controllers\Payment\PaymentController::class, 'callback'])
                ->withoutMiddleware(['web', 'auth']);
```
Learn more about [The Callback Route](#the-callback) below.

##### Define the public key and private keys in environment file
Defined the created payment box's public key and private key for the various coins in your `.env` file.
```
GOURL_PAYMENTBOX_BITCOIN_PUBLIC_KEY
GOURL_PAYMENTBOX_BITCOIN_PRIVATE_KEY

GOURL_PAYMENTBOX_BITCOINCASH_PUBLIC_KEY
GOURL_PAYMENTBOX_BITCOINCASH_PRIVATE_KEY
```

#### Config Options
See the published `config/laravel-crypto-payment-gateway.php` for available options to customize the payment box like changing logo and box style. 

<a name="usage"></a>

## Usage

### Payment Data Submission
The payment data can be submitted in the following ways;
* Form Submit
* AJAX Request
* Session Redirect (through controller, Livewire component or anywhere in your application)

Which ever method data field `amount` in BTC or  `amountUSD` need to be sent, both cannot be used. And optional data fields `userID`, `orderID` (if you want to reference the data to any model in app e.g Product model) and `redirect` (a url you want to redirect to once payment is received).


##### Usage with Form Submit
```html
<form id="payment-form" method="post" action="{{ route('payments.crypto.pay) }}">
  @csrf
  <input type="text" name="amountUSD" placeholder="Amount">
    <input type="hidden" name="userID" value="{{ Auth::user()->id }}">
  <input type="hidden" name="orderID" value="1">
  <input type="hidden" name="redirect" value="{{ url()->full() }}">
  <button type="submit">Pay</button>
</form>       
```

##### Usage with AJAX Request
```javascript
axios({
  method: "post",
  url: "/payments/crypto/pay",
  data: {
    amountUSD: 20.50,
    userID: 1,
    orderID: 101,
    redirect: 'https://domain.com/redirect-url',
  },
  headers: {
    'Accept': 'application/json'
    // Ensure you include your TOKEN as well
  },
})
  .then(function (response) {
    // The url is available in `data` key of the json response:
    // {
    //   status: true,
    //   message: 'Request successful.',
    //   data: 'https://your-domain.com/payments/crypto/pay?cryptopsid=some-unique-token-string'
    // }
    if (response.data.status === true) {
      const paymentUrl = response.data.data
      window.location = paymentUrl
    }
  })
  .catch(function (response) {
    //handle error
    console.log(response);
  });
```

##### Usage with Session Redirect (through controller, Livewire component or anywhere in your application)
You need to ensure you validate your data before sending to the payment gateway
```php
// This could be in a controller method or livewire component method
use Victorybiz\LaravelCryptoPaymentGateway\LaravelCryptoPaymentGateway;

$payment_url = LaravelCryptoPaymentGateway::startPaymentSession([
    'amountUSD' => $validatedData['amount'], // OR 'amount' when sending BTC value
    'orderID' => $product->id,
    'userID' => Auth::user()->id,
    'redirect' => url()->full(),
]);
// redirect to the payment page
redirect()->to($payment_url);
```

<a name="the-callback"></a>

### The Callback
When a user has made a payment, the [GoUrl.io](https://gourl.io)'s server will send payment data using HTTP POST method on your callback url specified in field Callback URL of your crypto payment box.

#####  Callback Controller
Create a `PaymentController` and call instance of Laravel Crypto Payment Gateway's callback method.
```php 
<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Victorybiz\LaravelCryptoPaymentGateway\LaravelCryptoPaymentGateway;

class PaymentController extends Controller
{
    /**
     * Cryptobox callback.
     */
    public function callback(Request $request)
    {   
      return LaravelCryptoPaymentGateway::callback();
    }
}
```

#####  Callback Route
Define the callback route to the created controller, the callback route should be a publicly accessible route with no auth. However, you may also exclude the route from CSRF Protection by adding their URIs to the `$except` property of the `VerifyCsrfToken` middleware.
If you use Cloudflare & Mod_Security & other CDN services, **[Click Here](https://gourl.io/api-php.html#payment_history)** to view the **GoUrl.io**'s server IPs you need to add in Whitelist.
```php 
// routes/web.php
 
Route::post('/payments/crypto/callback', [App\Http\Controllers\Payment\PaymentController::class, 'callback'])
                ->withoutMiddleware(['web', 'auth']);
```

<a name="the-ipn"></a>

#####  IPN (Instant Payment Notification)
Once the [GoUrl.io](https://gourl.io)'s `Cryptobox Class` finished processing the received callback behind the scene, then it make a call to a `cryptobox_new_payment(...)` function which allows you to define your processing after payment. To hook on to this function to handle IPN (Instant payment notification) with your custom processing like sending email notifications and giving value to the user once payment is confirmed, create a static class method.
This can be a static class method defined anywhere in your application.
Preferably you can just define the static method in the same `PaymentController` for easy code management.
```php 
<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Victorybiz\LaravelCryptoPaymentGateway\LaravelCryptoPaymentGateway;

class PaymentController extends Controller
{
    //...
    
    /**
     * Cryptobox IPN Example
     * 
     * @param \Victorybiz\LaravelCryptoPaymentGateway\Models\CryptoPaymentModel $cryptoPaymentModel
     * @param array $payment_details
     * @param string $box_status
     * @return bool
     */
    public static function ipn($cryptoPaymentModel, $payment_details, $box_status)
    {            
        if ($cryptoPaymentModel) {  
            /*
            // ADD YOUR CODE HERE
            // ------------------
            // For example, you have a model `UserOrder`, you can create new Bitcoin payment record to this model
            $userOrder = UserOrder::where('payment_id', $cryptoPaymentModel->paymentID)->first();
            if (!$userOrder) 
            {
                UserOrder::create([
                    'payment_id' => $cryptoPaymentModel->paymentID,
                    'user_id'    => $payment_details["user"],
                    'order_id'   => $payment_details["order"],
                    'amount'     => floatval($payment_details["amount"]),
                    'amountusd'  => floatval($payment_details["amountusd"]),
                    'coinlabel'  => $payment_details["coinlabel"],
                    'txconfirmed'  => $payment_details["confirmed"],
                    'status'     => $payment_details["status"],
                ]);
            }
            // ------------------

            // Received second IPN notification (optional) - Bitcoin payment confirmed (6+ transaction confirmations)
            if ($userOrder && $box_status == "cryptobox_updated")
            {
                $userOrder->txconfirmed = $payment_details["confirmed"];
                $userOrder->save();
            }
            // ------------------
            */

            // Onetime action when payment confirmed (6+ transaction confirmations)
            if (!$cryptoPaymentModel->processed && $payment_details["confirmed"])
            {
                // Add your custom logic here to give value to the user.
        
                // ------------------
                // set the status of the payment to processed
                // $cryptoPaymentModel->setStatusAsProcessed();

                // ------------------
                // Add logic to send notification of confirmed/processed payment to the user if any
            }
            
        }
        return true;
    }
}
```

<a name="ipn-config"></a>

Then update the published `config/laravel-crypto-payment-gateway.php` and set value for `hook_ipn` key to hook IPN (Instant Payment Notification) function to the static class method defined above.
 config file
```php
// config/laravel-crypto-payment-gateway.php

/**
 * Hook IPN (Instant payment notification) to the following static class method.
 * In this static class method, you can  add your custom logic and give value to the user once your confirmed payment box status.
 * You can also add payment notification logic. 
 * This can be a static class method defined anywhere in your application.
 * This can also be static method/action defined in controller class but route must not be defined for the action.
 * 
 * The Static Method Definition in your class:
 * @param \Victorybiz\LaravelCryptoPaymentGateway\Models\CryptoPaymentModel $cryptoPaymentModel
 * @param array $payment_details
 * @param string $box_status
 * @return bool
 * public static function ipn($cryptoPaymentModel, $payment_details, $box_status)
 * {
 *  // Add your custom logic here.
 *  return true;
 * }
 * 
 * Example: [\Victorybiz\LaravelCryptoPaymentGateway\Http\Controllers\CryptoPaymentController::class, 'ipn']
 */
'hook_ipn' => [\App\Http\Controllers\Payment\PaymentController, 'ipn'],

```

### Eloquent Model for `crypto_payments` table
This package provide eloquent model for the `crypto_payments`.
```php 
use Victorybiz\LaravelCryptoPaymentGateway\Models\CryptoPaymentModel;

$cryptoPayment = CryptoPaymentModel::find($paymentID);

$cryptoPayment = CryptoPaymentModel::where('paymentID', $paymentID);

$processedCryptoPayment = CryptoPaymentModel::where('processed', true);

```

<a name="advanced-usage"></a>

## Advanced Usage

### Instance of GoUrl.io PHP Class API (`cryptobox.class.php`)
This package provide access to instance of the core [GoUrl.io](https://gourl.io)'s **[cryptobox.class.php](https://github.com/cryptoapi/Payment-Gateway/blob/master/lib/cryptobox.class.php)** which this package uses under the hood.

```php
use Victorybiz\LaravelCryptoPaymentGateway\LaravelCryptoPaymentGateway;

$laravelCryptoPaymentGateway = new LaravelCryptoPaymentGateway;

$cryptobox = $laravelCryptoPaymentGateway->getCryptobox($options = []);

$payment_history = $cryptobox->payment_history($boxID = "", $orderID = "", $userID = "", $countryID = "", $boxType = "", $period = "7 DAY");
```

**[Click Here](https://gourl.io/api-php.html) to learn more about GoUrl.io PHP Class API and the available methods.**

<a name="testing"></a>

## Resources
* [GoUrl.io Website](https://gourl.io)
* [GoUrl.io's CryptoAPI Payment Gateway Repository](https://github.com/cryptoapi/Payment-Gateway)
* [PHP Class API Doc](https://gourl.io/api-php.html)
* [TailwindCSS](tailwindcss.com/) used on `compact` and `standard` box style.
* [AlpineJS](https://alpinejs.dev) used on `compact` and `standard` box style.
* [Alpine Clipboard](https://github.com/ryangjchandler/alpine-clipboard) used on `compact` and `standard` box style.
* [Alpine Tooltip](https://github.com/ryangjchandler/alpine-tooltip) used on `compact` and `standard` box style.
* [tc-lib-barcode](https://github.com/tecnickcom/tc-lib-barcode) used on `compact` and `standard` box style.
  

<a name="resources"></a>

## Testing

```bash
composer test
```

<a name="changelog"></a>

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

<a name="contributing"></a>

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

<a name="security"></a>

### Security

If you discover any security related issues, please email lavictorybiz@gmail.com instead of using the issue tracker.

<a name="credits"></a>

## Credits

-   [Victory Osayi Airuoyuwa](https://github.com/victorybiz)
-   [All Contributors](../../contributors)

<a name="license"></a>

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).
