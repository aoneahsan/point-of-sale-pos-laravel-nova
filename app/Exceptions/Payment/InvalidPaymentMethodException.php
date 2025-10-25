<?php

declare(strict_types=1);

namespace App\Exceptions\Payment;

use App\Exceptions\POSException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Exception thrown when an invalid payment method is used.
 */
class InvalidPaymentMethodException extends POSException
{
    protected int $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY;

    protected string $errorCode = 'INVALID_PAYMENT_METHOD';

    /**
     * Create exception for unsupported payment method.
     */
    public static function unsupported(string $paymentMethod): self
    {
        return new self(
            "Payment method '{$paymentMethod}' is not supported",
            ['payment_method' => $paymentMethod]
        );
    }

    /**
     * Create exception for inactive payment method.
     */
    public static function inactive(string $paymentMethod): self
    {
        return new self(
            "Payment method '{$paymentMethod}' is currently inactive",
            ['payment_method' => $paymentMethod]
        );
    }
}
