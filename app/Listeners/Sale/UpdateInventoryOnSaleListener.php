<?php

declare(strict_types=1);

namespace App\Listeners\Sale;

use App\Events\Sale\SaleCreatedEvent;
use App\Services\InventoryService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

/**
 * Update Inventory On Sale Listener
 *
 * Handles inventory deduction when a sale is created.
 * Runs asynchronously via queue for better performance.
 *
 * @see SaleCreatedEvent
 */
final class UpdateInventoryOnSaleListener implements ShouldQueue
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
     * @param  SaleCreatedEvent  $event
     * @return void
     */
    public function handle(SaleCreatedEvent $event): void
    {
        try {
            foreach ($event->sale->items as $item) {
                $this->inventoryService->deductStock(
                    product: $item->product,
                    quantity: $item->quantity,
                    reason: "Sale #{$event->sale->id}",
                    reference: "sale",
                    referenceId: $event->sale->id
                );
            }

            Log::info('Inventory updated for sale', [
                'sale_id' => $event->sale->id,
                'items_count' => $event->sale->items->count(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update inventory for sale', [
                'sale_id' => $event->sale->id,
                'error' => $e->getMessage(),
            ]);

            throw $e; // Re-throw to trigger queue retry
        }
    }
}
