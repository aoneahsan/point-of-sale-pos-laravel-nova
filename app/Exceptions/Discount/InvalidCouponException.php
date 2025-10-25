<?php

declare(strict_types=1);

namespace App\Exceptions\Discount;

use App\Exceptions\POSException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Exception thrown when a coupon is invalid or cannot be applied.
 */
class InvalidCouponException extends POSException
{
    protected int $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY;

    protected string $errorCode = 'INVALID_COUPON';

    /**
     * Create exception for non-existent coupon.
     */
    public static function notFound(string $code): self
    {
        return new self(
            "Coupon code '{$code}' not found",
            ['coupon_code' => $code]
        );
    }

    /**
     * Create exception for expired coupon.
     */
    public static function expired(string $code): self
    {
        return new self(
            "Coupon code '{$code}' has expired",
            ['coupon_code' => $code]
        );
    }

    /**
     * Create exception for usage limit reached.
     */
    public static function limitReached(string $code): self
    {
        return new self(
            "Coupon code '{$code}' has reached its usage limit",
            ['coupon_code' => $code]
        );
    }

    /**
     * Create exception for minimum order not met.
     */
    public static function minimumNotMet(string $code, float $minimumAmount, float $currentAmount): self
    {
        return new self(
            "Coupon '{$code}' requires minimum order of {$minimumAmount}. Current: {$currentAmount}",
            [
                'coupon_code' => $code,
                'minimum_amount' => $minimumAmount,
                'current_amount' => $currentAmount,
            ]
        );
    }
}
