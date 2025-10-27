<?php

declare(strict_types=1);

namespace App\Exceptions\Inventory;

use App\Exceptions\POSException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Exception thrown when trying to sell or adjust stock with insufficient quantity.
 *
 * This exception is used when there's not enough stock available for a transaction.
 * Context should include: product_id, requested_quantity, available_quantity
 */
class InsufficientStockException extends POSException
{
    protected int $statusCode = Response::HTTP_BAD_REQUEST;

    protected string $errorCode = 'INSUFFICIENT_STOCK';

    /**
     * Create exception for insufficient stock.
     *
     * @param  int  $productId
     * @param  int  $requestedQuantity
     * @param  int  $availableQuantity
     */
    public static function forProduct(int $productId, int $requestedQuantity, int $availableQuantity): self
    {
        return new self(
            "Insufficient stock for product ID {$productId}. Requested: {$requestedQuantity}, Available: {$availableQuantity}",
            [
                'product_id' => $productId,
                'requested_quantity' => $requestedQuantity,
                'available_quantity' => $availableQuantity,
            ]
        );
    }

    /**
     * Create exception for insufficient stock with product name.
     *
     * @param  string  $productName
     * @param  int  $requestedQuantity
     * @param  int  $availableQuantity
     */
    public static function forProductName(string $productName, int $requestedQuantity, int $availableQuantity): self
    {
        return new self(
            "Insufficient stock for '{$productName}'. Requested: {$requestedQuantity}, Available: {$availableQuantity}",
            [
                'product_name' => $productName,
                'requested_quantity' => $requestedQuantity,
                'available_quantity' => $availableQuantity,
            ]
        );
    }
}
