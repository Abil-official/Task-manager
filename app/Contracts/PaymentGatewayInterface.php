<?php

namespace App\Contracts;

interface PaymentGatewayInterface
{
    public function charge(float $amount, array $details): string; // Returns Transaction ID

    public function refund(string $transactionId): bool;
}
