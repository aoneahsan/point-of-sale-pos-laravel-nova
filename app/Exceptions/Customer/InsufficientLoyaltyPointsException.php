<?php

declare(strict_types=1);

namespace App\Exceptions\Customer;

use App\Exceptions\POSException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Exception thrown when customer doesn't have enough loyalty points for redemption.
 */
class InsufficientLoyaltyPointsException extends POSException
{
    protected int $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY;

    protected string $errorCode = 'INSUFFICIENT_LOYALTY_POINTS';

    /**
     * Create exception for insufficient points.
     */
    public static function forRedemption(int $customerId, int $requiredPoints, int $availablePoints): self
    {
        return new self(
            "Customer does not have enough loyalty points. Required: {$requiredPoints}, Available: {$availablePoints}",
            [
                'customer_id' => $customerId,
                'required_points' => $requiredPoints,
                'available_points' => $availablePoints,
                'shortage' => $requiredPoints - $availablePoints,
            ]
        );
    }
}
