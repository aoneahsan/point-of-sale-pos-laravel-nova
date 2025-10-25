<?php

declare(strict_types=1);

namespace App\Listeners\Sale;

use App\Events\Sale\SaleRefundedEvent;
use App\Services\InventoryService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

/**
 * Update Inventory On Refund Listener
 *
 * Handles inventory restoration when a sale is refunded.
 * Runs asynchronously via queue.
 *
 * @see SaleRefundedEvent
 */
final class UpdateInventoryOnRefundListener implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @param  InventoryService  $inventoryService
     */
    public function __construct(
        private readonly InventoryService $inventoryService
    ) {
    }

    /**
     * Handle the event.
     *
     * @param  SaleRefundedEvent  $event
     * @return void
     */
    public function handle(SaleRefundedEvent $event): void
    {
        try {
            foreach ($event->return->items as $returnItem) {
                $this->inventoryService->addStock(
                    product: $returnItem->saleItem->product,
                    quantity: $returnItem->quantity,
                    reason: "Refund for Sale #{$event->sale->id}",
                    reference: "refund",
                    referenceId: $event->return->id
                );
            }

            Log::info('Inventory restored for refund', [
                'sale_id' => $event->sale->id,
                'return_id' => $event->return->id,
                'items_count' => $event->return->items->count(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to restore inventory for refund', [
                'sale_id' => $event->sale->id,
                'return_id' => $event->return->id,
                'error' => $e->getMessage(),
            ]);

            throw $e; // Re-throw to trigger queue retry
        }
    }
}
