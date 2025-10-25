<?php

declare(strict_types=1);

namespace App\Exceptions\Sales;

use App\Exceptions\POSException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Exception thrown when a sale is invalid or cannot be processed.
 */
class InvalidSaleException extends POSException
{
    protected int $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY;

    protected string $errorCode = 'INVALID_SALE';

    /**
     * Create exception for empty cart.
     */
    public static function emptyCart(): self
    {
        return new self('Cannot process sale with empty cart');
    }

    /**
     * Create exception for invalid sale status.
     */
    public static function invalidStatus(string $currentStatus, string $requiredStatus): self
    {
        return new self(
            "Cannot perform this action. Sale status is '{$currentStatus}', required: '{$requiredStatus}'",
            [
                'current_status' => $currentStatus,
                'required_status' => $requiredStatus,
            ]
        );
    }

    /**
     * Create exception for completed sale modification.
     */
    public static function cannotModifyCompleted(int $saleId): self
    {
        return new self(
            "Cannot modify completed sale ID {$saleId}",
            ['sale_id' => $saleId]
        );
    }

    /**
     * Create exception for invalid sale total.
     */
    public static function invalidTotal(float $calculatedTotal, float $providedTotal): self
    {
        return new self(
            "Sale total mismatch. Calculated: {$calculatedTotal}, Provided: {$providedTotal}",
            [
                'calculated_total' => $calculatedTotal,
                'provided_total' => $providedTotal,
            ]
        );
    }
}
