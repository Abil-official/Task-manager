<?php

namespace App\Services;

use App\Contracts\PaymentGatewayInterface;
use App\Factory\PaymentGatewayBindingFactory;

class PaymentService
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected PaymentGatewayInterface $paymentGatewayInterface)
    {
        //
    }

    public function chargePayment()
    {
        $charge = 1000;
        $details = [
            'amount' => '100',
            'failure_url' => 'https=>//developer.esewa.com.np/failure',
            'product_delivery_charge' => '0',
            'product_service_charge' => '0',
            'product_code' => 'EPAYTEST',
            'signature' => 'i94zsd3oXF6ZsSr/kGqT4sSzYQzjj1W/waxjWyRwaME=',
            'signed_field_names' => 'total_amount,transaction_uuid,product_code',
            'success_url' => 'https=>//developer.esewa.com.np/success',
            'tax_amount' => '10',
            'total_amount' => '110',
            'transaction_uuid' => '241028',
        ];
        $provider = 'esewa';

        PaymentGatewayBindingFactory::make($provider);
        $this->paymentGatewayInterface->charge($charge, $details);

    }
}
