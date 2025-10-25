<?php

declare(strict_types=1);

namespace App\Exceptions\Payment;

use App\Exceptions\POSException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Exception thrown when a payment processing fails.
 */
class PaymentFailedException extends POSException
{
    protected int $statusCode = Response::HTTP_PAYMENT_REQUIRED;

    protected string $errorCode = 'PAYMENT_FAILED';

    /**
     * Create exception for failed payment.
     */
    public static function withReason(string $reason, array $context = []): self
    {
        return new self(
            "Payment failed: {$reason}",
            array_merge(['reason' => $reason], $context)
        );
    }

    /**
     * Create exception for Stripe payment failure.
     */
    public static function stripeError(string $stripeError): self
    {
        return new self(
            "Stripe payment failed: {$stripeError}",
            ['stripe_error' => $stripeError]
        );
    }

    /**
     * Create exception for insufficient payment.
     */
    public static function insufficientAmount(float $totalDue, float $amountPaid): self
    {
        return new self(
            "Insufficient payment. Total due: {$totalDue}, Amount paid: {$amountPaid}",
            [
                'total_due' => $totalDue,
                'amount_paid' => $amountPaid,
                'remaining' => $totalDue - $amountPaid,
            ]
        );
    }
}
