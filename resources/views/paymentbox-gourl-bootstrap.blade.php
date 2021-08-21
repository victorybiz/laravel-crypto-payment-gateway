<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="">
    <title>{{ $boxJsonValues['texts']['title'] }}</title>

    <!-- Bootstrap4 CSS - -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" crossorigin="anonymous">   
      
    <!-- Note - If your website not use Bootstrap4 CSS as main style, please use custom css style below and delete css line above. 
    It isolate Bootstrap CSS to a particular class 'bootstrapiso' to avoid css conflicts with your site main css style -->
    <!-- <link rel="stylesheet" href="css/bootstrapcustom.min.css" crossorigin="anonymous"> -->


    <!-- JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script defer src="https://use.fontawesome.com/releases/v5.12.0/js/all.js" crossorigin="anonymous"></script>
    @isset($jsPath)
      <script>
        {!! file_get_contents($jsPath) !!}
      </script>
    @endisset

    <!-- CSS for Payment Box -->
    <style>
            html { font-size: 14px; }
            @media (min-width: 768px) { html { font-size: 16px; } .tooltip-inner { max-width: 350px; } }
            .mncrpt .container { max-width: 980px; }
            .mncrpt .box-shadow { box-shadow: 0 .25rem .75rem rgba(0, 0, 0, .05); }
            img.radioimage-select { padding: 7px; border: solid 2px #ffffff; margin: 7px 1px; cursor: pointer; box-shadow: none; }
            img.radioimage-select:hover { border: solid 2px #a5c1e5; }
            img.radioimage-select.radioimage-checked { border: solid 2px #7db8d9; background-color: #f4f8fb; }
    </style>
  </head>

  <body>

    @php    
      // Text above payment box
      // $custom_text  = "<p class='lead'>";
      // $custom_text .= __('Please contact support for resolution.');
      // $custom_text .= "</p>";
      $custom_text = "";

      $coins = $laravelCryptoPaymentGateway->enabledCoins;
      $def_coin = $laravelCryptoPaymentGateway->defaultCoin;
      $def_language = $laravelCryptoPaymentGateway->defaultLanguage;
      $custom_text = $custom_text;
      $coinImageSize = 70;
      $qrcodeSize = 200;
      $show_languages = $laravelCryptoPaymentGateway->showLanguageBox;
      $logoimg_path = $laravelCryptoPaymentGateway->showLogo ? asset($laravelCryptoPaymentGateway->logo) : '';
      $resultimg_path = "default";
      $resultimgSize = 250;
      $redirect = $laravelCryptoPaymentGateway->redirect;
      $method = "curl";
      $debug = false;
      
      // Display payment box
      echo $box->display_cryptobox_bootstrap($coins, $def_coin, $def_language, $custom_text, $coinImageSize, $qrcodeSize, $show_languages, $logoimg_path, $resultimg_path, $resultimgSize, $redirect, $method, $debug);    

      // You can setup method='curl' in function above and use code below on this webpage -
      // if successful bitcoin payment received .... allow user to access your premium data/files/products, etc.
      // if ($box->is_paid()) { ... your code here ... }

    @endphp
  
  </body>
</html>