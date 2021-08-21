<?php

/**
 * GoUrl.io Bitcoin/Altcoin - PHP API for Laravel
 *
 * See: https://gourl.io/api-php.html
 */
return [
    /**
     * Box Style
     * 1. 'compact' (default)
     * 2. 'standard'
     * 3. 'gourl-bootstrap'
     */
    'box_style' => 'compact',

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
    'hook_ipn' => [],

    /**
     * Default coin
     */
    'default_coin' => 'bitcoin',

    /**
     * Place values from your gourl.io signup page here.
     */
    'paymentbox' => [
        'bitcoin' => [
            'public_key' => env('GOURL_PAYMENTBOX_BITCOIN_PUBLIC_KEY', null),
            'private_key' => env('GOURL_PAYMENTBOX_BITCOIN_PRIVATE_KEY', null),
            'enabled' => true,
        ],
        'bitcoincash' => [
            'public_key' => env('GOURL_PAYMENTBOX_BITCOINCASH_PUBLIC_KEY', null),
            'private_key' => env('GOURL_PAYMENTBOX_BITCOINCASH_PRIVATE_KEY', null),
            'enabled' => true,
        ],
        'bitcoinsv' => [
            'public_key' => env('GOURL_PAYMENTBOX_BITCOINSV_PUBLIC_KEY', null),
            'private_key' => env('GOURL_PAYMENTBOX_BITCOINSV_PRIVATE_KEY', null),
            'enabled' => true,
        ],
        'litecoin' => [
            'public_key' => env('GOURL_PAYMENTBOX_LITECOIN_PUBLIC_KEY', null),
            'private_key' => env('GOURL_PAYMENTBOX_LITECOIN_PRIVATE_KEY', null),
            'enabled' => true,
        ],
        'dash' => [
            'public_key' => env('GOURL_PAYMENTBOX_DASH_PUBLIC_KEY', null),
            'private_key' => env('GOURL_PAYMENTBOX_DASH_PRIVATE_KEY', null),
            'enabled' => true,
        ],
        'dogecoin' => [
            'public_key' => env('GOURL_PAYMENTBOX_DOGECOIN_PUBLIC_KEY', null),
            'private_key' => env('GOURL_PAYMENTBOX_DOGECOIN_PRIVATE_KEY', null),
            'enabled' => true,
        ],
        'speedcoin' => [
            'public_key' => env('GOURL_PAYMENTBOX_SPEEDCOIN_PUBLIC_KEY', null),
            'private_key' => env('GOURL_PAYMENTBOX_SPEEDCOIN_PRIVATE_KEY', null),
            'enabled' => true,
        ],
        'reddcoin' => [
            'public_key' => env('GOURL_PAYMENTBOX_REDDCOIN_PUBLIC_KEY', null),
            'private_key' => env('GOURL_PAYMENTBOX_REDDCOIN_PRIVATE_KEY', null),
            'enabled' => true,
        ],
        'potcoin' => [
            'public_key' => env('GOURL_PAYMENTBOX_POTCOIN_PUBLIC_KEY', null),
            'private_key' => env('GOURL_PAYMENTBOX_POTCOIN_PRIVATE_KEY', null),
            'enabled' => true,
        ],
        'feathercoin' => [
            'public_key' => env('GOURL_PAYMENTBOX_FEATHERCOIN_PUBLIC_KEY', null),
            'private_key' => env('GOURL_PAYMENTBOX_FEATHERCOIN_PRIVATE_KEY', null),
            'enabled' => true,
        ],
        'vertcoin' => [
            'public_key' => env('GOURL_PAYMENTBOX_VERTCOIN_PUBLIC_KEY', null),
            'private_key' => env('GOURL_PAYMENTBOX_VERTCOIN_PRIVATE_KEY', null),
            'enabled' => true,
        ],
        'peercoin' => [
            'public_key' => env('GOURL_PAYMENTBOX_PEERCOIN_PUBLIC_KEY', null),
            'private_key' => env('GOURL_PAYMENTBOX_PEERCOIN_PRIVATE_KEY', null),
            'enabled' => true,
        ],
        'monetaryunit' => [
            'public_key' => env('GOURL_PAYMENTBOX_MONETARYUNIT_PUBLIC_KEY', null),
            'private_key' => env('GOURL_PAYMENTBOX_MONETARYUNIT_PRIVATE_KEY', null),
            'enabled' => true,
        ],
        'universalcurrency' => [
            'public_key' => env('GOURL_PAYMENTBOX_UNIVERSALCURRENCY_PUBLIC_KEY', null),
            'private_key' => env('GOURL_PAYMENTBOX_UNIVERSALCURRENCY_PRIVATE_KEY', null),
            'enabled' => true,
        ],
    ],

    /**
     * This option is used only if form posted userID field is empty.
     * It will save random userID in cookies, sessions or use user IP address as userID.
     * Available values: COOKIE, SESSION, IPADDRESS
     * Default: COOKIE
     */
    'userFormat' => 'COOKIE',

    /**
     * Period after which the payment becomes obsolete and new cryptobox will be shown; 
     * Allowed values: NOEXPIRY, 1 MINUTE..90 MINUTE, 1 HOUR..90 HOURS, 1 DAY..90 DAYS, 1 WEEK..90 WEEKS, 1 MONTH..90 MONTHS
     * Default: NOEXPIRY
     */
    'period' => 'NOEXPIRY',

    /**
     * Relative logo path
     */
    'logo' => 'vendor/laravel-crypto-payment-gateway/images/logo.png',

    /**
     * Show logo on payment page
     */
    'show_logo' => true,

    /**
     * Show language box on payment page
     */
    'show_language_box' => true,


    /**
     * optional, gourl affiliate key
     */
    'webdev_key' => env('GOURL_WEBDEV_KEY', ''),
];