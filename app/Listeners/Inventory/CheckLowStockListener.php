<?php

declare(strict_types=1);

namespace App\Listeners\Inventory;

use App\Events\Inventory\LowStockDetectedEvent;
use App\Events\Inventory\StockAdjustedEvent;
use Illuminate\Support\Facades\Log;

/**
 * Check Low Stock Listener
 *
 * Checks if stock adjustment resulted in low stock condition.
 * Fires LowStockDetectedEvent if threshold is breached.
 *
 * @see StockAdjustedEvent
 */
final class CheckLowStockListener
{
    /**
     * Handle the event.
     *
     * @param  StockAdjustedEvent  $event
     * @return void
     */
    public function handle(StockAdjustedEvent $event): void
    {
        foreach ($event->adjustment->items as $item) {
            $product = $item->product;

            // Skip if product doesn't track stock or has no reorder point
            if (!$product->track_stock || !$product->reorder_point) {
                continue;
            }

            $currentStock = $product->stock_quantity ?? 0;

            // Check if stock is below reorder point
            if ($currentStock <= $product->reorder_point) {
                LowStockDetectedEvent::dispatch(
                    product: $product,
                    currentStock: $currentStock,
                    reorderPoint: $product->reorder_point
                );

                Log::info('Low stock detected during adjustment', [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'current_stock' => $currentStock,
                    'reorder_point' => $product->reorder_point,
                    'adjustment_id' => $event->adjustment->id,
                ]);
            }
        }
    }
}
