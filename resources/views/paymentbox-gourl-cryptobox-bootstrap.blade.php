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
    @isset($supportJsPath)
      <script>
        {!! file_get_contents($supportJsPath) !!}
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
      $coins = $laravelCryptoPaymentGateway->enabledCoins;
      $def_coin = $laravelCryptoPaymentGateway->defaultCoin;
      $def_language = $laravelCryptoPaymentGateway->defaultLanguage;
      $custom_text = $box_template_options['custom_text'] ?? "";
      $coinImageSize = $box_template_options['coin_image_size'] ?? 70;
      $qrcodeSize = $box_template_options['qrcode_size'] ?? 200;
      $show_languages = $laravelCryptoPaymentGateway->showLanguageBox;
      $logoimg_path = $laravelCryptoPaymentGateway->showLogo ? asset($laravelCryptoPaymentGateway->logo) : '';
      $resultimg_path = $box_template_options['result_img_path'] ?? "default";
      $resultimgSize = $box_template_options['result_img_size'] ?? 250;
      $redirect = $laravelCryptoPaymentGateway->redirect;
      $method = $box_template_options['method'] ?? "curl";
      $debug = $box_template_options['debug'] ?? false;
      
      // Display payment box
      echo $box->display_cryptobox_bootstrap($coins, $def_coin, $def_language, $custom_text, $coinImageSize, $qrcodeSize, $show_languages, $logoimg_path, $resultimg_path, $resultimgSize, $redirect, $method, $debug);    
    @endphp

    @if($laravelCryptoPaymentGateway->showCancelButton && $laravelCryptoPaymentGateway->previous)
      <div class="text-center" style="margin-bottom: 2rem; margin-top: -3rem">
        <a
            href="?{{ $queryStringsFull ? $queryStringsFull.'&' : '' }}cancel-payment={{ 'yes' }}"
            class="px-4 py-2 btn-light"
            onclick="return confirm('{{ __('Cancel Payment?') }}')"
            >
            {{ __('Cancel') }}
          </a>
      </div>
    @endif
  
  </body>
</html>