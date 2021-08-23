<?php

namespace Victorybiz\LaravelCryptoPaymentGateway\Http\Controllers;
use Victorybiz\LaravelCryptoPaymentGateway\LaravelCryptoPaymentGateway;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CryptoPaymentController extends Controller
{    
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        return $this->paymentbox($request);
    }


    /**
     * Payment box
     */
    public function paymentbox(Request $request)
    {   
        // if request to this controller action is a POST request, retrieve the data from the request
        // and store it in the session then redirect to the payment box with a GET request and psid (payment session id)
        if ($request->isMethod('post')) {

            $validated = $request->validate([
                'amount' => [
                    'nullable', 
                    new \Victorybiz\LaravelCryptoPaymentGateway\Rules\Money
                ],
                'amountUSD' => [
                    'nullable',
                    \Illuminate\Validation\Rule::requiredIf(function () use ($request) {
                        return $request->input('amount') == null || $request->input('amount') <= 0;
                    }),
                    new \Victorybiz\LaravelCryptoPaymentGateway\Rules\Money
                ],
                'orderID' => 'nullable',
                'userID' => 'nullable',
                'redirect' => 'nullable|url',
            ]);
            // save to session
            $paymentbox_url = LaravelCryptoPaymentGateway::startPaymentSession($validated);

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => true, 
                    'message' => __('Request successful.'),
                    'data' => $paymentbox_url,
                ], 200);
            }
            return redirect()->to($paymentbox_url);
        }
        // End of POST request


        // --------------------------------
        // if request to this controller action is a GET request, retrieve the data from the session
        $options = LaravelCryptoPaymentGateway::getPaymentSession($request);
        if (!$options || !is_array($options)) {
            die(__("Sorry, we couldn't complete your request due to data mismatch. Please try making payment again."));
        }

        $laravelCryptoPaymentGateway = new LaravelCryptoPaymentGateway;
        $options['coinname'] =  $request->query('coin', $request->query('gourlcryptocoin')) ?: $laravelCryptoPaymentGateway->defaultCoin;

        $box = $laravelCryptoPaymentGateway->getCryptobox(($options));

        // $boxJsonUrl = $box->cryptobox_json_url();
        $boxJsonValues = $box->get_json_values();
        $boxIsPaid = $box->is_paid();

        if (!isset($boxJsonValues['box'])) {
            // if no data was received from payment box api
            die(__("Sorry, we couldn't complete your request. Please try again in a moment."));
        }
        
        // Cancel payment
        if ($request->query('cancel-payment') == 'yes' && $laravelCryptoPaymentGateway->previous) {
            $box->cryptobox_reset();
            return redirect()->to($laravelCryptoPaymentGateway->previous);
        }
        
        // Current Language
		$lan = cryptobox_sellanguage($laravelCryptoPaymentGateway->defaultLanguage);
		$localisation = $laravelCryptoPaymentGateway->localisation[$lan];

        // Querystrings formatting
        $queryStringsArr = $_GET;
        $queryStringsFull = http_build_query($queryStringsArr);
        if (isset($queryStringsArr['coin'])) {
            unset($queryStringsArr['coin']);
        }
        $queryStrings = http_build_query($queryStringsArr);

        // instantiate the barcode class
        $barcode = new \Com\Tecnick\Barcode\Barcode();
        $bar_width = $laravelCryptoPaymentGateway->boxTemplate == 'compact' || $laravelCryptoPaymentGateway->boxTemplate == '' ? -2 : -4;
        $bar_height = $laravelCryptoPaymentGateway->boxTemplate == 'compact' || $laravelCryptoPaymentGateway->boxTemplate == '' ? -2 : -4;
        $barcodeObj = $barcode->getBarcodeObj(
            'QRCODE,H',                     // barcode type and additional comma-separated parameters
            $boxJsonValues['wallet_url'],   // data string to encode
            $bar_width,                     // bar width (use absolute or negative value as multiplication factor)
            $bar_height,                    // bar height (use absolute or negative value as multiplication factor)
            'black',                        // foreground color
            array(-2, -2, -2, -2)           // padding (use absolute or negative values as multiplication factors)
            )->setBackgroundColor('white'); // background color
        // output the barcode... 
        $walletQRCode = $barcodeObj->getHtmlDiv(); // SOURCE OUTPUT:: getHtmlDiv(), getSvgCode() | IMAGE OUTPUT:: getPng(), getSvg(), getPngData


        // Switch to the view based on the box template
        if($laravelCryptoPaymentGateway->boxTemplate == 'gourl-cryptobox-iframe') {
            $view = 'paymentbox-gourl-cryptobox-iframe';
            $box_template_options = $laravelCryptoPaymentGateway->boxTemplateOptions['gourl_cryptobox_iframe'];

        } elseif($laravelCryptoPaymentGateway->boxTemplate == 'gourl-cryptobox-bootstrap') {
            $view = 'paymentbox-gourl-cryptobox-bootstrap';
            $box_template_options = $laravelCryptoPaymentGateway->boxTemplateOptions['gourl_cryptobox_bootstrap'];

        } elseif($laravelCryptoPaymentGateway->boxTemplate == 'standard') {
            $view = 'paymentbox-standard';
            $box_template_options = $laravelCryptoPaymentGateway->boxTemplateOptions['standard'];

        } else {
            $view = 'paymentbox-compact';
            $box_template_options = $laravelCryptoPaymentGateway->boxTemplateOptions['compact'];
        }

        return view("laravel-crypto-payment-gateway::{$view}")
                ->with( 
                    compact(
                        'laravelCryptoPaymentGateway', 'box', 'boxJsonValues', 'boxIsPaid', 
                        'walletQRCode', 'queryStrings', 'queryStringsFull', 'localisation', 'box_template_options'
                    ) 
                );
    }

    /**
     * Cryptobox callback Example.
     */
    public function callback(Request $request)
    {   
        return LaravelCryptoPaymentGateway::callback();
    }


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