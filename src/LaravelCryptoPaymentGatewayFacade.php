<?php

namespace Victorybiz\LaravelCryptoPaymentGateway;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Victorybiz\LaravelCryptoPaymentGateway\Skeleton\SkeletonClass
 */
class LaravelCryptoPaymentGatewayFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-crypto-payment-gateway';
    }
}
