<?php

namespace App;

use App\Contracts\PaymentGatewayInterface;

class StripeGatewayClass implements PaymentGatewayInterface
{
    public function charge(float $amount, array $details): string {} // Returns Transaction ID

    public function refund(string $transactionId): bool {}
}
