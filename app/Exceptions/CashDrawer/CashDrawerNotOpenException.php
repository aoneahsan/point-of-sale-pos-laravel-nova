<?php

declare(strict_types=1);

namespace App\Exceptions\CashDrawer;

use App\Exceptions\POSException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Exception thrown when attempting operations that require an open cash drawer.
 */
class CashDrawerNotOpenException extends POSException
{
    protected int $statusCode = Response::HTTP_PRECONDITION_FAILED;

    protected string $errorCode = 'CASH_DRAWER_NOT_OPEN';

    /**
     * Create exception for operation requiring open drawer.
     */
    public static function forOperation(string $operation): self
    {
        return new self(
            "Cannot perform '{$operation}': No cash drawer is currently open for this user",
            ['operation' => $operation]
        );
    }

    /**
     * Create exception for cash sale.
     */
    public static function forCashSale(): self
    {
        return self::forOperation('cash sale');
    }

    /**
     * Create exception for cash transaction.
     */
    public static function forCashTransaction(): self
    {
        return self::forOperation('cash transaction');
    }
}
