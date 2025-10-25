<?php

declare(strict_types=1);

namespace App\Exceptions\CashDrawer;

use App\Exceptions\POSException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Exception thrown when attempting to open a cash drawer that's already open.
 */
class CashDrawerAlreadyOpenException extends POSException
{
    protected int $statusCode = Response::HTTP_CONFLICT;

    protected string $errorCode = 'CASH_DRAWER_ALREADY_OPEN';

    /**
     * Create exception for already open drawer.
     */
    public static function forUser(int $userId, int $cashDrawerId): self
    {
        return new self(
            "User already has an open cash drawer (ID: {$cashDrawerId}). Please close it before opening a new one.",
            [
                'user_id' => $userId,
                'open_drawer_id' => $cashDrawerId,
            ]
        );
    }
}
