<?php

namespace App\Services;

use App\Models\SalePayment;
use App\Models\Sale;

class PaymentService
{
    public function processPayment(Sale $sale, array $paymentData): SalePayment
    {
        $payment = SalePayment::create([
            'sale_id' => $sale->id,
            'payment_method_id' => $paymentData['payment_method_id'],
            'amount' => $paymentData['amount'],
            'reference' => $paymentData['reference'] ?? null,
        ]);

        return $payment;
    }

    public function getTotalPaid(Sale $sale): float
    {
        return $sale->payments()->sum('amount');
    }

    public function getRemainingBalance(Sale $sale): float
    {
        return $sale->total - $this->getTotalPaid($sale);
    }

    public function isFullyPaid(Sale $sale): bool
    {
        return $this->getRemainingBalance($sale) <= 0;
    }
}