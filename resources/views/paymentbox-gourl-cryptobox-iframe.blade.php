<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="">
    <title>{{ $boxJsonValues['texts']['title'] }}</title>
    
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
    @isset($cryptoboxJsPath)
      <script>
        {!! file_get_contents($cryptoboxJsPath) !!}
      </script>
    @endisset

  </head>

  <body class="pt-4">

    @if($laravelCryptoPaymentGateway->showLanguageBox)
      <div class='mb-4 text-center'>
          {!! display_language_box($laravelCryptoPaymentGateway->defaultLanguage) !!}
      </div>
    @endif

    @if(!$boxIsPaid)
      <div class='mb-2 text-center'>
          @php
            $coins = $laravelCryptoPaymentGateway->enabledCoins;
            $def_coin = $laravelCryptoPaymentGateway->defaultCoin;
            $def_language = $laravelCryptoPaymentGateway->defaultLanguage;
            $iconWidth = 50;
            $style = ""; //margin: 80px 0 0 
            $directory = CRYPTOBOX_IMG_FILES_PATH;

            echo display_currency_box($coins, $def_coin, $def_language, $iconWidth, $style, $directory);
          @endphp
      </div>
    @endif

    @php
      $submit_btn = $box_template_options['submit_btn'] ?? true;
      $width = $box_template_options['width'] ?? '540';
      $height = $box_template_options['height'] ?? '230';
      $box_style = $box_template_options['box_style'] ?? '';
      $message_style = $box_template_options['message_style'] ?? '';
      $anchor = $box_template_options['anchor'] ?? '';

      // Display payment box
      echo $box->display_cryptobox($submit_btn, $width, $height, $box_style, $message_style, $anchor);
    @endphp

    @if($laravelCryptoPaymentGateway->showCancelButton && $laravelCryptoPaymentGateway->previous)
      <div class="-mt-5 text-center">
        <a
            href="?{{ $queryStringsFull ? $queryStringsFull.'&' : '' }}cancel-payment={{ 'yes' }}"
            class="justify-center px-4 py-1.5 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md shadow-sm hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300"
            onclick="return confirm('{{ __('Cancel Payment?') }}')"
            >
            {{ __('Cancel') }}
          </a>
      </div>
    @endif
  
  </body>
</html>