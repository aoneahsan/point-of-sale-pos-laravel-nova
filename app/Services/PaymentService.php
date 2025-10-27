<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\SalePayment;
use App\Models\PaymentMethod;
use App\Exceptions\Payment\InvalidPaymentMethodException;
use App\Exceptions\Payment\PaymentFailedException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    /**
     * Process a single payment for a sale
     *
     * @param Sale $sale
     * @param int $paymentMethodId
     * @param float $amount
     * @param string|null $reference
     * @return SalePayment
     * @throws InvalidPaymentMethodException
     * @throws PaymentFailedException
     */
    public function processPayment(
        Sale $sale,
        int $paymentMethodId,
        float $amount,
        ?string $reference = null
    ): SalePayment {
        // Validate amount
        if ($amount <= 0) {
            throw new PaymentFailedException("Payment amount must be greater than zero");
        }

        // Validate payment method
        if (!$this->isPaymentMethodActive($paymentMethodId)) {
            throw new InvalidPaymentMethodException("Payment method is not active or does not exist");
        }

        return DB::transaction(function () use ($sale, $paymentMethodId, $amount, $reference) {
            $payment = SalePayment::create([
                'sale_id' => $sale->id,
                'payment_method_id' => $paymentMethodId,
                'amount' => $amount,
                'reference' => $reference,
            ]);

            Log::info("Payment processed", [
                'sale_id' => $sale->id,
                'payment_id' => $payment->id,
                'amount' => $amount,
                'payment_method_id' => $paymentMethodId,
            ]);

            return $payment;
        });
    }

    /**
     * Process split payment with multiple payment methods
     *
     * @param Sale $sale
     * @param array $payments Array of payment details [payment_method_id, amount, reference?]
     * @return Collection Collection of SalePayment models
     * @throws PaymentFailedException
     */
    public function processSplitPayment(Sale $sale, array $payments): Collection
    {
        // Calculate total payment amount
        $totalPayment = collect($payments)->sum('amount');

        // Validate total matches sale total
        if (abs($totalPayment - $sale->total) > 0.01) { // Allow for floating point precision
            throw new PaymentFailedException(
                "Total payment amount ($totalPayment) does not match sale total ({$sale->total})"
            );
        }

        return DB::transaction(function () use ($sale, $payments) {
            $processedPayments = collect();

            foreach ($payments as $paymentData) {
                $payment = $this->processPayment(
                    sale: $sale,
                    paymentMethodId: $paymentData['payment_method_id'],
                    amount: $paymentData['amount'],
                    reference: $paymentData['reference'] ?? null
                );

                $processedPayments->push($payment);
            }

            return $processedPayments;
        });
    }

    /**
     * Calculate change to return to customer
     *
     * @param float $amountDue
     * @param float $amountReceived
     * @return float
     * @throws PaymentFailedException
     */
    public function calculateChange(float $amountDue, float $amountReceived): float
    {
        if ($amountReceived < $amountDue) {
            throw new PaymentFailedException(
                "Received amount ($amountReceived) is less than amount due ($amountDue)"
            );
        }

        $change = $amountReceived - $amountDue;
        return round($change, 2);
    }

    /**
     * Validate if a payment method exists
     *
     * @param int $paymentMethodId
     * @return bool
     */
    public function validatePaymentMethod(int $paymentMethodId): bool
    {
        return PaymentMethod::where('id', $paymentMethodId)->exists();
    }

    /**
     * Check if a payment method is active
     *
     * @param int $paymentMethodId
     * @return bool
     */
    public function isPaymentMethodActive(int $paymentMethodId): bool
    {
        return PaymentMethod::where('id', $paymentMethodId)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Process a refund for a payment
     *
     * @param SalePayment $payment
     * @param float $amount
     * @param string|null $reason
     * @return SalePayment
     * @throws PaymentFailedException
     */
    public function refundPayment(
        SalePayment $payment,
        float $amount,
        ?string $reason = null
    ): SalePayment {
        // Validate refund amount
        if ($amount > $payment->amount) {
            throw new PaymentFailedException(
                "Refund amount ($amount) cannot exceed original payment amount ({$payment->amount})"
            );
        }

        if ($amount <= 0) {
            throw new PaymentFailedException("Refund amount must be greater than zero");
        }

        return DB::transaction(function () use ($payment, $amount, $reason) {
            // Create refund as a negative payment
            $refund = SalePayment::create([
                'sale_id' => $payment->sale_id,
                'payment_method_id' => $payment->payment_method_id,
                'amount' => -$amount, // Negative amount indicates refund
                'reference' => $reason ?? "Refund for payment #{$payment->id}",
            ]);

            Log::info("Payment refunded", [
                'original_payment_id' => $payment->id,
                'refund_payment_id' => $refund->id,
                'refund_amount' => $amount,
                'reason' => $reason,
            ]);

            return $refund;
        });
    }

    /**
     * Get all active payment methods
     *
     * @return Collection
     */
    public function getActivePaymentMethods(): Collection
    {
        return PaymentMethod::where('is_active', true)->get();
    }

    /**
     * Get payment method by code
     *
     * @param string $code
     * @return PaymentMethod|null
     */
    public function getPaymentMethodByCode(string $code): ?PaymentMethod
    {
        return PaymentMethod::where('code', $code)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Get total payments for a sale
     *
     * @param Sale $sale
     * @return float
     */
    public function getTotalPayments(Sale $sale): float
    {
        return $sale->payments()->sum('amount');
    }

    /**
     * Get remaining balance for a sale
     *
     * @param Sale $sale
     * @return float
     */
    public function getRemainingBalance(Sale $sale): float
    {
        $totalPaid = $this->getTotalPayments($sale);
        return round($sale->total - $totalPaid, 2);
    }

    /**
     * Check if sale is fully paid
     *
     * @param Sale $sale
     * @return bool
     */
    public function isFullyPaid(Sale $sale): bool
    {
        return $this->getRemainingBalance($sale) <= 0;
    }
}
