<?php

namespace App\Factory;

use App\EsewaGatewayClass;
use App\StripeGatewayClass;

class PaymentGatewayBindingFactory
{
    public static function make($provider)
    {
        return match ($provider) {
            'esewa' => new EsewaGatewayClass,
            'stripe' => new StripeGatewayClass,
        };
    }
}
