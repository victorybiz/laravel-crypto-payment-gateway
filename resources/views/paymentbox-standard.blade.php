<!doctype html>
<html>

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>{{ $boxJsonValues['texts']['title'] }}</title>
  <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>
  <link rel="stylesheet" href="https://unpkg.com/tippy.js@6/dist/tippy.css" />
</head>

<body
  x-data="{
    walletAddress: '{{ $boxJsonValues['addr'] }}',
    amount: '{{ $boxJsonValues['amount'] }}',
    txUrl: '{{ $boxJsonValues['tx_url'] }}',
  }"
>
  <input type="hidden" x-model="walletAddress">
  <input type="hidden" x-model="amount">
  <input type="hidden" x-model="txUrl">

  <div class="flex flex-col justify-center min-h-screen py-12 bg-gray-50 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-2xl">
      {{-- Logo --}}
      @if($laravelCryptoPaymentGateway->showLogo)
        <img class="w-auto h-12 mx-auto" src="{{ asset($laravelCryptoPaymentGateway->logo) }}" alt="{{ asset($laravelCryptoPaymentGateway->logo) }}">
      @endif

      <div class="mt-4 text-center">
        {{-- Custim currency box --}}
        <span class="block mt-1 mb-2 font-bold">{!! $localisation["payment"] !!}</span>
        @if(is_array($laravelCryptoPaymentGateway->enabledCoinImages) && count($laravelCryptoPaymentGateway->enabledCoinImages) > 1)
          @foreach($laravelCryptoPaymentGateway->enabledCoinImages as $coin => $imageData)
            <a
              href="?{{ $queryStrings ? $queryStrings.'&' : '' }}coin={{ $coin }}"
              x-data="{ tooltip: '{{ addslashes(str_replace("%coinName%", ucfirst($coin), $localisation["pay_in"])) }}' }"
              x-tooltip="tooltip"
              class="inline-block p-1 border hover:border-gray-400 {{ request()->query('coin') == $coin ? 'border rounded border-gray-800' : '' }}"
            >
              <img class="h-14" src="data:image/png;base64,{{ $imageData }}" alt="{{ addslashes(str_replace("%coinName%", ucfirst($coin), $localisation["pay_in"])) }}">
            </a>
          @endforeach
        @endif
      </div>
      
      <h2 class="mt-6 text-3xl font-medium text-center text-gray-900">
          {{ $boxJsonValues['texts']['title'] }}
      </h2>
    </div>
    
    {{-- Paid --}}
    @if($boxIsPaid)

      {{-- payment_received --}}
      @if($boxJsonValues['status'] == 'payment_received')

        <div class="mt-4 sm:mx-auto sm:w-full sm:max-w-2xl">
          <div class="px-4 py-8 mx-2 bg-white rounded-lg shadow sm:px-10">

            {{-- USD amount badge --}}
            <div class="float-right px-2 py-1 -mt-8 -mr-4 text-sm font-medium text-white bg-gray-600 rounded-tr-lg whitespace-nowrap sm:-mt-8 sm:-mr-10 abslute">
              {{ $boxJsonValues['amountusd'] }} <span class="font-extralight">USD</span>
            </div>
            
            <div class="space-y-6">

              <div class="text-3xl font-extrabold text-center">
                <span
                  x-data="{ tooltip: '{{ addslashes($boxJsonValues['texts']['copy_amount']) }}' }"
                  x-tooltip="tooltip"
                  x-on:click="$clipboard(amount); tooltip = '{{ addslashes($boxJsonValues['texts']['copied']) }}'"
                  class="cursor-pointer"
                >
                  {{ $boxJsonValues['amount'] }}
                </span>
                <span class="font-extralight">{{ $boxJsonValues['coinlabel'] }}</span>
              </div>

              <div class="text-center text-9xl font-extralight">
                <i
                  class="text-green-500 fas fa-check-circle"
                  x-data="{ tooltip: '{{ addslashes('Copy Transaction ID') }}' }"
                  x-tooltip="tooltip"
                  x-on:click="$clipboard(txUrl); tooltip = '{{ addslashes($boxJsonValues['texts']['copied']) }}'"
                ></i>
              </div>

              <div class="text-5xl text-center font-extralight">
                {{ $boxJsonValues['texts']['payment_successful'] }}
              </div>

              <div class="text-xl text-center font-extralight">
                {{ $boxJsonValues['texts']['received_on'] }} {{ $boxJsonValues['date'] }}
              </div>

              <div class="space-y-2 sm:flex sm:space-x-4 sm:space-y-0">
                <a 
                  href="{{ $boxJsonValues['tx_url'] }}"
                  target="_blank"
                  class="flex justify-center w-full px-4 py-2 text-sm font-medium text-blue-600 bg-white border border-blue-600 rounded-md shadow-sm sm:flex-1 hover:bg-blue-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                  >
                  {{ $boxJsonValues['texts']['btn_res'] }}
                </a>
                
                {{-- Redirect / Close button --}}
                @if ($laravelCryptoPaymentGateway->redirect)
                  <a 
                    href="{{ $laravelCryptoPaymentGateway->redirect }}"
                    class="flex justify-center w-full px-4 py-2 text-sm font-medium text-black bg-gray-300 border border-gray-400 rounded-md shadow-sm sm:flex-1 hover:bg-gray-400 hover:text-black focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                    >
                    {{ __('Close') }}
                  </a>
                @endif
              </div>
            </div>
          </div>
        </div>

      {{-- payment_received_unrecognised --}}
      @elseif($boxJsonValues['status'] == 'payment_received_unrecognised')

        <div class="mt-4 sm:mx-auto sm:w-full sm:max-w-2xl">
          <div class="px-4 py-8 mx-2 bg-white rounded-lg shadow sm:px-10">

            {{-- USD amount badge --}}
            <div class="float-right px-2 py-1 -mt-8 -mr-4 text-sm font-medium text-white bg-gray-600 rounded-tr-lg whitespace-nowrap sm:-mt-8 sm:-mr-10 abslute">
              {{ $boxJsonValues['amountusd'] }} <span class="font-extralight">USD</span>
            </div>
            
            <div class="space-y-6">

              <div class="text-3xl font-extrabold text-center">
                <span
                  x-data="{ tooltip: '{{ addslashes($boxJsonValues['texts']['copy_amount']) }}' }"
                  x-tooltip="tooltip"
                  x-on:click="$clipboard(amount); tooltip = '{{ addslashes($boxJsonValues['texts']['copied']) }}'"
                  class="cursor-pointer"
                >
                  {{ $boxJsonValues['amount'] }}
                </span>
                <span class="font-extralight">{{ $boxJsonValues['coinlabel'] }}</span>
              </div>

              <div class="text-center text-9xl font-extralight">
                <i
                  class="text-yellow-500 fas fa-exclamation-triangle"
                  x-data="{ tooltip: '{{ addslashes('Copy Transaction ID') }}' }"
                  x-tooltip="tooltip"
                  x-on:click="$clipboard(txUrl); tooltip = '{{ addslashes($boxJsonValues['texts']['copied']) }}'"
                ></i>
              </div>

              <div class="text-5xl text-center font-extralight">
                {{ $boxJsonValues['err'] ?? __('An incorrect bitcoin amount has been received') }}
              </div>

              <div class="text-xl text-center font-extralight">
                {{ $boxJsonValues['texts']['received_on'] }} {{ $boxJsonValues['date'] }}
              </div>

              <div class="text-xl text-center font-extralight">
                {{ __('Please contact support for resolution.') }}
              </div>

              <div class="space-y-2 sm:flex sm:space-x-4 sm:space-y-0">
                <a 
                  href="{{ $boxJsonValues['tx_url'] }}"
                  target="_blank"
                  class="flex justify-center w-full px-4 py-2 text-sm font-medium text-blue-600 bg-white border border-blue-600 rounded-md shadow-sm sm:flex-1 hover:bg-blue-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                  >
                  {{ $boxJsonValues['texts']['btn_res'] }}
                </a>

                {{-- Redirect / Close button --}}
                @if ($laravelCryptoPaymentGateway->redirect)
                  <a 
                    href="{{ $laravelCryptoPaymentGateway->redirect }}"
                    class="flex justify-center w-full px-4 py-2 text-sm font-medium text-black bg-gray-300 border border-gray-400 rounded-md shadow-sm sm:flex-1 hover:bg-gray-400 hover:text-black focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                    >
                    {{ __('Close') }}
                  </a>
                @endif
              </div>
              
            </div>
          </div>
        </div>

      @else

        {{-- Unknown error --}}
        <div class="mt-4 sm:mx-auto sm:w-full sm:max-w-2xl">
          <div class="px-4 py-8 bg-white shadow sm:rounded-lg sm:px-10">
            <div class="space-y-6">
              <div class="font-medium text-center text-red-600 text-md">
                {{ __("Sorry, we couldn't complete your request. Please try again in a moment.") }}
              </div>

              {{-- Redirect / Close button --}}
              @if ($laravelCryptoPaymentGateway->redirect)
                <a 
                  href="{{ $laravelCryptoPaymentGateway->redirect }}"
                  class="flex justify-center w-full px-4 py-2 text-sm font-medium text-black bg-gray-300 border border-gray-400 rounded-md shadow-sm hover:bg-gray-400 hover:text-black focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                  >
                  {{ __('Close') }}
                </a>
              @endif
            </div>
          </div>
        </div>

      @endif


    @else

      {{-- payment_not_received | Awaiting payment --}}
      <div class="mt-4 sm:mx-auto sm:w-full sm:max-w-2xl">
        <div class="px-4 py-8 mx-2 bg-white rounded-lg shadow sm:px-10">

          {{-- USD amount badge --}}
          <div class="float-right px-2 py-1 -mt-8 -mr-4 text-sm font-medium text-white bg-gray-600 rounded-tr-lg whitespace-nowrap sm:-mt-8 sm:-mr-10 abslute">
            {{ $boxJsonValues['amountusd'] }} <span class="font-extralight">USD</span>
          </div>
          
          <div class="space-y-6">
            
            <div
              class="text-center"
              x-data="{ tooltip: '{{ addslashes($boxJsonValues['texts']['btn_copy']) }}' }"
              x-tooltip="tooltip"
              x-on:click="tooltip = ''"
            >
              <span
                class="inline-block cursor-pointer"
                x-data="{ tooltip: '{{ addslashes($boxJsonValues['texts']['qrcode']) }}' }"
                x-tooltip.delay.1500="tooltip"
                x-on:click="$clipboard(walletAddress); tooltip = '{{ addslashes($boxJsonValues['texts']['copied']) }}'"
              >
                {!! $walletQRCode !!}
              </span>
            </div>
            
            <div class="text-3xl font-extrabold text-center">
              <span
                x-data="{ tooltip: '{{ addslashes($boxJsonValues['texts']['copy_amount']) }}' }"
                x-tooltip="tooltip"
                x-on:click="$clipboard(amount); tooltip = '{{ addslashes($boxJsonValues['texts']['copied']) }}'"
                class="cursor-pointer"
              >
                {{ $boxJsonValues['amount'] }}
              </span>
              <span class="font-extralight">{{ $boxJsonValues['coinlabel'] }}</span>
            </div>
            
            <div class="text-center text-md">
              {{ $boxJsonValues['texts']['intro2'] }}
            </div>

            <div class="font-extrabold text-center border border-gray-400 rounded text-md">            
              <a
                href="{{ $boxJsonValues['wallet_url'] }}"
                x-data="{ tooltip: '{{ addslashes($boxJsonValues['texts']['btn_wallet']) }}' }"
                x-tooltip="tooltip"
                class="text-blue-700 hover:text-blue-500"
              >
                {{ $boxJsonValues['addr'] }} 
              </a>
                          
              <i
                class="ml-3 cursor-pointer far fa-copy"
                x-data="{ tooltip: '{{ addslashes($boxJsonValues['texts']['btn_copy']) }}' }"
                x-tooltip="tooltip"
                x-on:click="$clipboard(walletAddress); tooltip = '{{ addslashes($boxJsonValues['texts']['copied']) }}'"
              ></i>

              <a href="{{ $boxJsonValues['wallet_url'] }}">
                <i
                  class="ml-3 cursor-pointer fas fa-external-link-alt"
                  x-data="{ tooltip: '{{ addslashes($boxJsonValues['texts']['btn_wallet_hint']) }}' }"
                  x-tooltip="tooltip"                
                ></i>
              </a>
            </div>

            <div class="mt-2">
              <button 
                type="submit"
                class="flex justify-center w-full px-4 py-2 text-sm font-medium text-blue-600 bg-white border border-blue-600 rounded-md shadow-sm hover:bg-blue-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                <i class="mr-2 fas fa-spinner fa-pulse"></i>
                {{ $boxJsonValues['texts']['payment_wait'] }}
              </button>
            </div>         
            <div class="text-center text-md">
              {{ $boxJsonValues['texts']['intro3'] }}
            </div>          
          </div>
        </div>
        {{-- <div class="m-4">
          <a 
            href=""
            x-data="{ tooltip: '{{ addslashes($boxJsonValues['texts']['btn_wait_hint']) }}' }"
            x-tooltip="tooltip"
            class="flex justify-center w-full px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
            <i class="mt-1 mr-2 fas fa-angle-double-right"></i>
            {{ __('Click Here if you have already sent :coinname', ['coinname' => $boxJsonValues['coinname']]) }}
            <i class="mt-1 ml-2 fas fa-angle-double-right"></i>
          </a>
        </div> --}}
        
      </div>
    @endif
    
    {{-- language box  --}}
    @if($laravelCryptoPaymentGateway->showLanguageBox)
      <div class="mt-4 text-center">
        {!! display_language_box('en', 'cryptolang', true) !!}
      </div>
    @endif
    
  </div>

  <script src="https://cdn.jsdelivr.net/npm/@ryangjchandler/alpine-clipboard@2.x.x/dist/alpine-clipboard.js" defer></script>
  <script src="https://cdn.jsdelivr.net/npm/@ryangjchandler/alpine-tooltip@0.x.x/dist/cdn.min.js" defer></script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  @if(!$boxIsPaid)
    <script>
      window.setInterval('refresh()', 15000); // 15000 milliseconds (15 seconds)
      function refresh() {
        window.location.reload();
      }
    </script>
  @endif

  @if($boxIsPaid && $boxJsonValues['status'] == 'payment_received' && $laravelCryptoPaymentGateway->redirect)
    <script>
      window.setTimeout(function() { 
        window.location = '{{ $laravelCryptoPaymentGateway->redirect }}'; 
      }, 3000);
    </script>
  @endif
</body>

</html>