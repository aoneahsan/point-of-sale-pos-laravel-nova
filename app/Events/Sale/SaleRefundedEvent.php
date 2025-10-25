<?php

declare(strict_types=1);

namespace App\Events\Sale;

use App\Models\Sale;
use App\Models\SaleReturn;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Sale Refunded Event
 *
 * Fired when a sale is refunded/returned.
 * This event triggers inventory restoration and refund processing.
 *
 * @property Sale $sale The original sale being refunded
 * @property SaleReturn $return The return record
 */
final class SaleRefundedEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  Sale  $sale The original sale
     * @param  SaleReturn  $return The return record
     */
    public function __construct(
        public readonly Sale $sale,
        public readonly SaleReturn $return
    ) {
    }
}
