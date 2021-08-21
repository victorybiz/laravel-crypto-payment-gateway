<?php
/** 
 * MODIFIED CUSTOM CRYPTOBOX NEW PAYMENT FILE FROM /lib/cryptobox.newpayment.php
 */


function cryptobox_new_payment($paymentID = 0, $payment_details = array(), $box_status = "")
{

	// Debug - new payment email notification for webmaster
	// Uncomment lines below and make any test payment
	// You can use page https://gourl.io/info/ipn to send
	// dummy payment data to your website,
	// you will receive this email notification
	// --------------------------------------------
	// $email = "....your email address....";
	// mail($email, "Payment - " . $paymentID . " - " . $box_status, " \n Payment ID: " . $paymentID . " \n\n Status: " . $box_status . " \n\n Details: " . print_r($payment_details, true));

  /** .............
	.............

	PLACE YOUR CODE HERE

	Update database with new payment, send email to user, etc
	Please note, all received payments store in your table `crypto_payments` also
	See - https://gourl.io/api-php.html#payment_history
	.............
	.............
	For example, you have own table `user_orders`...
	You can use function run_sql() from cryptobox.class.php ( https://gourl.io/api-php.html#run_sql )

	.............
	// Save new Bitcoin payment in database table `user_orders`
	$recordExists = run_sql("select paymentID as nme FROM `user_orders` WHERE paymentID = ".intval($paymentID));
	if (!$recordExists) run_sql("INSERT INTO `user_orders` VALUES(".intval($paymentID).",'".addslashes($payment_details["user"])."','".addslashes($payment_details["order"])."',".floatval($payment_details["amount"]).",".floatval($payment_details["amountusd"]).",'".addslashes($payment_details["coinlabel"])."',".intval($payment_details["confirmed"]).",'".addslashes($payment_details["status"])."')");

	.............
	// Received second IPN notification (optional) - Bitcoin payment confirmed (6+ transaction confirmations)
	if ($recordExists && $box_status == "cryptobox_updated")  run_sql("UPDATE `user_orders` SET txconfirmed = ".intval($payment_details["confirmed"])." WHERE paymentID = ".intval($paymentID));
	.............
	.............

	// Onetime action when payment confirmed (6+ transaction confirmations)
	$processed = run_sql("select processed as nme FROM `crypto_payments` WHERE paymentID = ".intval($paymentID)." LIMIT 1");
	if (!$processed && $payment_details["confirmed"])
	{
		// ... Your code ...

		// ... and update status in default table where all payments are stored - https://github.com/cryptoapi/Payment-Gateway#mysql-table
		$sql = "UPDATE crypto_payments SET processed = 1, processedDate = '".gmdate("Y-m-d H:i:s")."' WHERE paymentID = ".intval($paymentID)." LIMIT 1";
		run_sql($sql);
	}

	.............
  */


    /**
     * Hook to add IPN custom code
     */
    $hook_ipn = config('laravel-crypto-payment-gateway.hook_ipn');
    if ($hook_ipn && is_array($hook_ipn)) {
        
        $cryptoPaymentModel = \Victorybiz\LaravelCryptoPaymentGateway\Models\CryptoPaymentModel::find($paymentID);

        return call_user_func($hook_ipn, $cryptoPaymentModel, $payment_details, $box_status);
    }

    return true;         
}
