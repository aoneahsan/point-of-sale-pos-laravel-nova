<?php

declare(strict_types=1);

namespace App\Events\Sale;

use App\Models\Sale;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Sale Created Event
 *
 * Fired when a new sale transaction is successfully created.
 * This event triggers inventory updates, receipt generation, and audit logging.
 *
 * @property Sale $sale The created sale instance
 */
final class SaleCreatedEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  Sale  $sale The sale that was created
     */
    public function __construct(
        public readonly Sale $sale
    ) {
    }
}
