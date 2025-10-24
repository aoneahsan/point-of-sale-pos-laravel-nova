<?php

namespace App\Services;

use App\Models\StockMovement;
use App\Models\ProductVariant;

class InventoryService
{
    public function createStockMovement(array $data): StockMovement
    {
        $variant = ProductVariant::findOrFail($data['product_variant_id']);
        $quantityBefore = $variant->stock;
        
        $movement = StockMovement::create([
            'product_variant_id' => $data['product_variant_id'],
            'store_id' => $data['store_id'],
            'type' => $data['type'],
            'quantity' => $data['quantity'],
            'quantity_before' => $quantityBefore,
            'quantity_after' => $quantityBefore + ($data['type'] === 'in' ? $data['quantity'] : -$data['quantity']),
            'reference' => $data['reference'] ?? null,
            'relatable_type' => $data['relatable_type'] ?? null,
            'relatable_id' => $data['relatable_id'] ?? null,
        ]);

        return $movement;
    }

    public function adjustStock(ProductVariant $variant, int $newQuantity, string $reason): StockMovement
    {
        $diff = $newQuantity - $variant->stock;
        $type = $diff > 0 ? 'in' : 'out';

        $movement = $this->createStockMovement([
            'product_variant_id' => $variant->id,
            'store_id' => $variant->store_id,
            'type' => 'adjustment',
            'quantity' => abs($diff),
            'reference' => $reason,
        ]);

        $variant->update(['stock' => $newQuantity]);

        return $movement;
    }

    public function getLowStockProducts(int $storeId)
    {
        return ProductVariant::where('store_id', $storeId)
            ->lowStock()
            ->with('product')
            ->get();
    }
}