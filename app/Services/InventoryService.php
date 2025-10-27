<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockMovement;
use App\Exceptions\Inventory\InsufficientStockException;
use App\Exceptions\Inventory\InvalidStockAdjustmentException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class InventoryService
{
    /**
     * Add stock to a product
     */
    public function addStock(
        Product $product,
        int $quantity,
        string $reason,
        ?string $reference = null,
        ?int $referenceId = null
    ): Product {
        return DB::transaction(function () use ($product, $quantity, $reason, $reference, $referenceId) {
            $product = Product::lockForUpdate()->findOrFail($product->id);

            if ($quantity <= 0) {
                throw new InvalidStockAdjustmentException("Quantity must be positive");
            }

            $quantityBefore = $product->stock_quantity;
            $quantityAfter = $quantityBefore + $quantity;

            $product->update(['stock_quantity' => $quantityAfter]);

            $this->createStockMovement([
                'product_id' => $product->id,
                'store_id' => $product->store_id,
                'type' => 'in',
                'quantity' => $quantity,
                'quantity_before' => $quantityBefore,
                'quantity_after' => $quantityAfter,
                'reference' => $reference ?? $reason,
                'reference_id' => $referenceId,
                'reason' => $reason,
            ]);

            return $product->fresh();
        });
    }

    /**
     * Deduct stock from a product
     */
    public function deductStock(
        Product $product,
        int $quantity,
        string $reason,
        ?string $reference = null,
        ?int $referenceId = null
    ): Product {
        return DB::transaction(function () use ($product, $quantity, $reason, $reference, $referenceId) {
            $product = Product::lockForUpdate()->findOrFail($product->id);

            if ($quantity <= 0) {
                throw new InvalidStockAdjustmentException("Quantity must be positive");
            }

            if ($product->stock_quantity < $quantity) {
                throw InsufficientStockException::forProduct(
                    $product->id,
                    $quantity,
                    $product->stock_quantity
                );
            }

            $quantityBefore = $product->stock_quantity;
            $quantityAfter = $quantityBefore - $quantity;

            $product->update(['stock_quantity' => $quantityAfter]);

            $this->createStockMovement([
                'product_id' => $product->id,
                'store_id' => $product->store_id,
                'type' => 'out',
                'quantity' => -$quantity, // Negative for deductions
                'quantity_before' => $quantityBefore,
                'quantity_after' => $quantityAfter,
                'reference' => $reference ?? $reason,
                'reference_id' => $referenceId,
                'reason' => $reason,
            ]);

            return $product->fresh();
        });
    }

    /**
     * Adjust product stock to a specific quantity
     */
    public function adjustStock(Product $product, int $newQuantity, string $reason): Product
    {
        return DB::transaction(function () use ($product, $newQuantity, $reason) {
            $product = Product::lockForUpdate()->findOrFail($product->id);

            if ($newQuantity < 0) {
                throw new InvalidStockAdjustmentException("Stock quantity cannot be negative");
            }

            $quantityBefore = $product->stock_quantity;
            $difference = $newQuantity - $quantityBefore;

            $product->update(['stock_quantity' => $newQuantity]);

            $this->createStockMovement([
                'product_id' => $product->id,
                'store_id' => $product->store_id,
                'type' => 'adjustment',
                'quantity' => $difference, // Can be positive or negative
                'quantity_before' => $quantityBefore,
                'quantity_after' => $newQuantity,
                'reference' => $reason,
                'reason' => $reason,
            ]);

            return $product->fresh();
        });
    }

    /**
     * Check if product has available stock
     */
    public function checkStockAvailability(Product $product, int $quantity): bool
    {
        if (!$product->track_stock) {
            return true;
        }

        return $product->stock_quantity >= $quantity;
    }

    /**
     * Check if product is low on stock
     */
    public function isLowStock(Product $product): bool
    {
        return $product->isLowStock();
    }

    /**
     * Get all low stock products for a store
     */
    public function getLowStockProducts($store): Collection
    {
        $storeId = is_object($store) ? $store->id : $store;

        return Product::where('store_id', $storeId)
            ->lowStock()
            ->with(['category', 'brand'])
            ->get();
    }

    /**
     * Get stock movements for a product
     */
    public function getStockMovements(int $productId, ?int $storeId = null): Collection
    {
        $query = StockMovement::where('product_id', $productId)
            ->with('product')
            ->orderBy('created_at', 'desc');

        if ($storeId) {
            $query->where('store_id', $storeId);
        }

        return $query->get();
    }

    /**
     * Create a stock movement record
     */
    protected function createStockMovement(array $data): StockMovement
    {
        return StockMovement::create([
            'product_id' => $data['product_id'],
            'store_id' => $data['store_id'],
            'type' => $data['type'],
            'quantity' => $data['quantity'],
            'quantity_before' => $data['quantity_before'] ?? null,
            'quantity_after' => $data['quantity_after'] ?? null,
            'reference' => $data['reference'] ?? null,
            'relatable_type' => $data['relatable_type'] ?? null,
            'relatable_id' => $data['relatable_id'] ?? $data['reference_id'] ?? null,
            'reason' => $data['reason'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);
    }
}