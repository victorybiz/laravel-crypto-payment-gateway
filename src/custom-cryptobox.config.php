<?php
/** 
 * MODIFIED CUSTOM CRYPTOBOX CONFIGURATION FILE FROM /lib/cryptobox.config.php
 */


/**
 *  MYSQL DATABASE DETAILS
 */
define("DB_HOST",    env('DB_HOST', '127.0.0.1'));		
define("DB_USER", 	  env('DB_USERNAME', 'forge'));
define("DB_PASSWORD", env('DB_PASSWORD', ''));
define("DB_NAME", 	  env('DB_DATABASE', 'forge'));



/**
 *  ARRAY OF ALL YOUR CRYPTOBOX PRIVATE KEYS
 */
$cryptobox_private_keys = [];
$cryptobox = config('laravel-crypto-payment-gateway.paymentbox');

if ($cryptobox && is_array($cryptobox)) {
  foreach ($cryptobox as $box) {
    if (isset($box['enabled']) && isset($box['private_key']) && $box['enabled'] == true && $box['private_key'] != '') {
      $cryptobox_private_keys[] = $box['private_key'];
    }
  }
}
if (count($cryptobox_private_keys) > 0) {
  $cryptobox_private_keys = implode('^', $cryptobox_private_keys);
} else {
  $cryptobox_private_keys = null;
}
// defined the private keys
define("CRYPTOBOX_PRIVATE_KEYS", $cryptobox_private_keys);
unset($cryptobox_private_keys);
