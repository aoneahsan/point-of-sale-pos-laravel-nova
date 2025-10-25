<?php

declare(strict_types=1);

namespace App\Events\Inventory;

use App\Models\Product;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Low Stock Detected Event
 *
 * Fired when a product's stock falls below its reorder point.
 * This event triggers notifications to inventory managers.
 *
 * @property Product $product The product with low stock
 * @property int $currentStock Current stock level
 * @property int $reorderPoint Reorder threshold
 */
final class LowStockDetectedEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  Product  $product The product with low stock
     * @param  int  $currentStock Current stock quantity
     * @param  int  $reorderPoint Reorder threshold
     */
    public function __construct(
        public readonly Product $product,
        public readonly int $currentStock,
        public readonly int $reorderPoint
    ) {
    }
}
