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
    public function addStock(int $productId, int $quantity, string $reason, int $storeId): Product
    {
        return DB::transaction(function () use ($productId, $quantity, $reason, $storeId) {
            $product = Product::lockForUpdate()->findOrFail($productId);

            if ($quantity <= 0) {
                throw new InvalidStockAdjustmentException("Quantity must be positive");
            }

            $quantityBefore = $product->stock_quantity;
            $quantityAfter = $quantityBefore + $quantity;

            $product->update(['stock_quantity' => $quantityAfter]);

            $this->createStockMovement([
                'product_id' => $productId,
                'store_id' => $storeId,
                'type' => 'in',
                'quantity' => $quantity,
                'quantity_before' => $quantityBefore,
                'quantity_after' => $quantityAfter,
                'reference' => $reason,
            ]);

            return $product->fresh();
        });
    }

    /**
     * Deduct stock from a product
     */
    public function deductStock(int $productId, int $quantity, string $reason, int $storeId): Product
    {
        return DB::transaction(function () use ($productId, $quantity, $reason, $storeId) {
            $product = Product::lockForUpdate()->findOrFail($productId);

            if ($quantity <= 0) {
                throw new InvalidStockAdjustmentException("Quantity must be positive");
            }

            if (!$this->hasAvailableStock($productId, $quantity)) {
                throw new InsufficientStockException(
                    "Insufficient stock for product ID {$productId}. Available: {$product->stock_quantity}, Requested: {$quantity}"
                );
            }

            $quantityBefore = $product->stock_quantity;
            $quantityAfter = $quantityBefore - $quantity;

            $product->update(['stock_quantity' => $quantityAfter]);

            $this->createStockMovement([
                'product_id' => $productId,
                'store_id' => $storeId,
                'type' => 'out',
                'quantity' => $quantity,
                'quantity_before' => $quantityBefore,
                'quantity_after' => $quantityAfter,
                'reference' => $reason,
            ]);

            return $product->fresh();
        });
    }

    /**
     * Adjust product stock to a specific quantity
     */
    public function adjustStock(int $productId, int $newQuantity, string $reason, int $storeId): Product
    {
        return DB::transaction(function () use ($productId, $newQuantity, $reason, $storeId) {
            $product = Product::lockForUpdate()->findOrFail($productId);

            if ($newQuantity < 0) {
                throw new InvalidStockAdjustmentException("Stock quantity cannot be negative");
            }

            $quantityBefore = $product->stock_quantity;
            $difference = $newQuantity - $quantityBefore;
            $type = $difference > 0 ? 'in' : 'out';

            $product->update(['stock_quantity' => $newQuantity]);

            $this->createStockMovement([
                'product_id' => $productId,
                'store_id' => $storeId,
                'type' => 'adjustment',
                'quantity' => abs($difference),
                'quantity_before' => $quantityBefore,
                'quantity_after' => $newQuantity,
                'reference' => $reason,
            ]);

            return $product->fresh();
        });
    }

    /**
     * Check if product has available stock
     */
    public function hasAvailableStock(int $productId, int $requiredQuantity): bool
    {
        $product = Product::findOrFail($productId);

        if (!$product->track_stock) {
            return true;
        }

        return $product->stock_quantity >= $requiredQuantity;
    }

    /**
     * Check if product is low on stock
     */
    public function isLowStock(int $productId): bool
    {
        $product = Product::findOrFail($productId);
        return $product->isLowStock();
    }

    /**
     * Get all low stock products for a store
     */
    public function getLowStockProducts(int $storeId): Collection
    {
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
            'quantity_before' => $data['quantity_before'],
            'quantity_after' => $data['quantity_after'],
            'reference' => $data['reference'] ?? null,
            'relatable_type' => $data['relatable_type'] ?? null,
            'relatable_id' => $data['relatable_id'] ?? null,
        ]);
    }
}