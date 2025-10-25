<?php

declare(strict_types=1);

namespace App\Exceptions\Customer;

use App\Exceptions\POSException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Exception thrown when customer doesn't have enough store credit for payment.
 */
class InsufficientStoreCreditException extends POSException
{
    protected int $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY;

    protected string $errorCode = 'INSUFFICIENT_STORE_CREDIT';

    /**
     * Create exception for insufficient credit.
     */
    public static function forPayment(int $customerId, float $requiredAmount, float $availableCredit): self
    {
        return new self(
            "Customer does not have enough store credit. Required: {$requiredAmount}, Available: {$availableCredit}",
            [
                'customer_id' => $customerId,
                'required_amount' => $requiredAmount,
                'available_credit' => $availableCredit,
                'shortage' => $requiredAmount - $availableCredit,
            ]
        );
    }
}
