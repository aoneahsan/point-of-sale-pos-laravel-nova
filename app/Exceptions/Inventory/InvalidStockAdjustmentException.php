<?php

declare(strict_types=1);

namespace App\Exceptions\Inventory;

use App\Exceptions\POSException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Exception thrown when a stock adjustment is invalid.
 *
 * This includes negative stock, invalid adjustment types, or other constraint violations.
 */
class InvalidStockAdjustmentException extends POSException
{
    protected int $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY;

    protected string $errorCode = 'INVALID_STOCK_ADJUSTMENT';

    /**
     * Create exception for negative stock result.
     */
    public static function negativeStock(int $productId, int $currentStock, int $adjustmentQuantity): self
    {
        return new self(
            "Stock adjustment would result in negative stock. Current: {$currentStock}, Adjustment: {$adjustmentQuantity}",
            [
                'product_id' => $productId,
                'current_stock' => $currentStock,
                'adjustment_quantity' => $adjustmentQuantity,
            ]
        );
    }

    /**
     * Create exception for unapproved adjustment.
     */
    public static function requiresApproval(int $adjustmentId): self
    {
        return new self(
            "Stock adjustment ID {$adjustmentId} requires approval before processing",
            ['adjustment_id' => $adjustmentId]
        );
    }
}
