<?php

declare(strict_types=1);

namespace App\Exceptions\Product;

use App\Exceptions\POSException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Exception thrown when a product cannot be found.
 */
class ProductNotFoundException extends POSException
{
    protected int $statusCode = Response::HTTP_NOT_FOUND;

    protected string $errorCode = 'PRODUCT_NOT_FOUND';

    /**
     * Create exception for product not found by ID.
     */
    public static function byId(int $productId): self
    {
        return new self(
            "Product with ID {$productId} not found",
            ['product_id' => $productId]
        );
    }

    /**
     * Create exception for product not found by SKU.
     */
    public static function bySku(string $sku): self
    {
        return new self(
            "Product with SKU '{$sku}' not found",
            ['sku' => $sku]
        );
    }

    /**
     * Create exception for product not found by barcode.
     */
    public static function byBarcode(string $barcode): self
    {
        return new self(
            "Product with barcode '{$barcode}' not found",
            ['barcode' => $barcode]
        );
    }
}
