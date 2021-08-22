<?php

namespace Victorybiz\LaravelCryptoPaymentGateway;

class LaravelCryptoPaymentGateway
{
    public $cryptobox;

    private $options = [
        "coinname"    => 'bitcoin',
        "amount"   	  => 0,				// post price in coins OR in USD below
        "amountUSD"   => 0,	            // we use post price in USD
        "orderID"     => '', 		    // order id: if you manual setup userID, you need to update orderID for users who already paid before: post1, post2, post3  
        "userID"      => '', 		    // unique identifier for every user, if userID is empty, system will autogenerate userID and save in cookies
        "userFormat"  => 'COOKIE', 	    // save userID in COOKIE, IPADDRESS or SESSION
        "period"      => 'NOEXPIRY', 	// payment valid period: NOEXPIRY = one time payment for each new user post, not expiry
        "language"	  => 'en',          // text on EN - english, FR - french, etc
        "public_key"  => '', 	        // your public key from gourl.io
        "private_key" => '', 	        // your private key from gourl.io
        "webdev_key"  => '',            // optional, gourl affiliate key
    ];

    public $coins = [];
    public $enabledCoins = [];
    public $enabledCoinImages = [];
    public $defaultCoin = 'bitcoin';
    public $localisation = [];
    public $defaultLanguage = 'en';
    public $boxTemplate = 'compact';
    public $boxTemplateOptions = [];
    public $logo = '';
    public $showLogo = '';
    public $showLanguageBox = '';
    public $redirect = '';

    /**
     * The constructor.
     */
    public function __construct()
    {
        // firstly call to check and customize if none cryptobox lib files
        $this->customizeCryptoboxLibFiles();

        // include cryptobox class
        require_once(__DIR__ . '/cryptoapi_php/lib/cryptobox.class.php');


        if (!defined('CRYPTOBOX_IMG_FILES_PATH')) {
            define('CRYPTOBOX_IMG_FILES_PATH', asset('vendor/laravel-crypto-payment-gateway/images/'));
        }

        // Enabled coins
        $cryptobox = config('laravel-crypto-payment-gateway.paymentbox');
        if ($cryptobox && is_array($cryptobox)) {
            foreach ($cryptobox as $coin => $box) {
                if (isset($box['enabled']) && isset($box['private_key']) && $box['enabled'] == true && $box['private_key'] != '') {
                    $this->enabledCoins[] = $coin;
                    $this->enabledCoinImages[$coin] = base64_encode(file_get_contents(__DIR__ . '/cryptoapi_php/images/' . $coin . '.png'));
                }
            }
        }
        // Default coin
        $this->defaultCoin = config('laravel-crypto-payment-gateway.default_coin');

        // GoUrl supported crypto currencies
        $this->coins = json_decode(CRYPTOBOX_COINS) or die("CRYPTOBOX_COINS not defined");
        // localisation
        $this->localisation = json_decode(CRYPTOBOX_LOCALISATION, true);        
        // Default language
        $this->defaultLanguage = app()->getLocale();
        // Box Template
        $this->boxTemplate = config('laravel-crypto-payment-gateway.box_template');
        // Box Template Options
        $this->boxTemplateOptions = config('laravel-crypto-payment-gateway.box_template_options');

        // Logo
        $this->logo = config('laravel-crypto-payment-gateway.logo');
        // Show logo
        $this->showLogo = config('laravel-crypto-payment-gateway.show_logo');
        // Show language box
        $this->showLanguageBox = config('laravel-crypto-payment-gateway.show_language_box');
    }

    /**
     * Customize cryptobox lib files
     */
    public function customizeCryptoboxLibFiles()
    {
        // read and customize the cryptobox config file
        $lines = file(__DIR__ . '/cryptoapi_php/lib/cryptobox.config.php');
        if (is_array($lines) && count($lines) > 0) {
            $last_line = $lines[count($lines) - 1];
            if ($last_line != "require_once(__DIR__ . '/../../custom-cryptobox.config.php');") {
                $newContent = "";
                foreach ($lines as $line) {
                    if (trim($line) != "?>") {
                        $newContent .= (trim($line) == "<?php") ? $line : '// ' . $line;
                    }
                }
                $newContent .= "\r\n\r\n\r\n";
                $newContent .= "// custom cryptobox.config.php\r\n";
                $newContent .= "require_once(__DIR__ . '/../../custom-cryptobox.config.php');";
                // write the new content
                file_put_contents(__DIR__ . '/cryptoapi_php/lib/cryptobox.config.php', $newContent);
            }
        }
        // read and customize the cryptobox newpayment file
        $lines = file(__DIR__ . '/cryptoapi_php/lib/cryptobox.newpayment.php');
        if (is_array($lines) && count($lines) > 0) {
            $last_line = $lines[count($lines) - 1];
            if ($last_line != "require_once(__DIR__ . '/../../custom-cryptobox.newpayment.php');") {
                $newContent = "";
                foreach ($lines as $line) {
                    if (trim($line) != "?>") {
                        $newContent .= (trim($line) == "<?php") ? $line : '// ' . $line;
                    }
                }
                $newContent .= "\r\n\r\n\r\n";
                $newContent .= "// custom cryptobox.newpayment.php\r\n";
                $newContent .= "require_once(__DIR__ . '/../../custom-cryptobox.newpayment.php');";
                // write the new content
                file_put_contents(__DIR__ . '/cryptoapi_php/lib/cryptobox.newpayment.php', $newContent);
            }
        }
    }

    /**
     * Get Cryptobox
     * 
     * @param array $options
     * @return Cryptobox
     */
    public function getCryptobox($options = [])
    {
        $options = array_merge($this->options, $options);

        if (!isset($options['coinname'])) {
            $options['coinname'] =  'bitcoin';
        }

        $cryptoboxConfig = config('laravel-crypto-payment-gateway.paymentbox');

        $options['public_key'] =  $options['public_key'] ?: $cryptoboxConfig[$options['coinname']]['public_key'];
        $options['private_key'] =  $options['private_key'] ?: $cryptoboxConfig[$options['coinname']]['private_key'];

        $options['webdev_key'] =  $options['webdev_key'] ?: config('laravel-crypto-payment-gateway.webdev_key');

        $this->redirect =  $options['redirect'] ?? '';
        unset($options['redirect']);

        $this->cryptobox = new \CryptoBox($options);
        return $this->cryptobox;
    }

    /** 
     * Callback function for cryptobox
     */
    public static function callback()
    {
        // include cryptobox callback file
        require_once(__DIR__ . '/cryptoapi_php/lib/cryptobox.callback.php');
    }

    /**
     * Start payment session
     */
    public static function startPaymentSession($options)
    {
        $payment_session_id = \Illuminate\Support\Str::uuid()->toString();
        // save to session
        session(["paymentbox_{$payment_session_id}" => $options]);

        $paymentbox_url = action([\Victorybiz\LaravelCryptoPaymentGateway\Http\Controllers\CryptoPaymentController::class], ['cryptopsid' => $payment_session_id]);
        return $paymentbox_url;
    }

    /**
     * Get payment session
     */
    public static function getPaymentSession($request)
    {
        $payment_session_id = $request->query('cryptopsid');
        $options = session("paymentbox_{$payment_session_id}");
        return $options;
    }

    
}
