<?php

declare(strict_types=1);

namespace App\Events\Inventory;

use App\Models\StockAdjustment;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Stock Adjusted Event
 *
 * Fired when inventory levels are manually adjusted.
 * This event triggers low stock checks and audit logging.
 *
 * @property StockAdjustment $adjustment The stock adjustment record
 */
final class StockAdjustedEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  StockAdjustment  $adjustment The stock adjustment
     */
    public function __construct(
        public readonly StockAdjustment $adjustment
    ) {
    }
}
