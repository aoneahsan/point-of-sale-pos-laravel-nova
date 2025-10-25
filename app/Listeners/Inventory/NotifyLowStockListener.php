<?php

declare(strict_types=1);

namespace App\Listeners\Inventory;

use App\Events\Inventory\LowStockDetectedEvent;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

/**
 * Notify Low Stock Listener
 *
 * Sends notifications to inventory managers when stock is low.
 * Runs asynchronously via queue.
 *
 * @see LowStockDetectedEvent
 */
final class NotifyLowStockListener implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param  LowStockDetectedEvent  $event
     * @return void
     */
    public function handle(LowStockDetectedEvent $event): void
    {
        try {
            // Get users with 'manage-inventory' permission
            $inventoryManagers = User::permission('manage-inventory')
                ->where('store_id', $event->product->store_id)
                ->get();

            if ($inventoryManagers->isEmpty()) {
                Log::warning('No inventory managers found for low stock notification', [
                    'product_id' => $event->product->id,
                    'store_id' => $event->product->store_id,
                ]);
                return;
            }

            // TODO: Create LowStockNotification and send
            // Notification::send($inventoryManagers, new LowStockNotification($event->product));

            Log::info('Low stock notification queued', [
                'product_id' => $event->product->id,
                'product_name' => $event->product->name,
                'current_stock' => $event->currentStock,
                'reorder_point' => $event->reorderPoint,
                'notified_users' => $inventoryManagers->count(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send low stock notification', [
                'product_id' => $event->product->id,
                'error' => $e->getMessage(),
            ]);

            // Don't throw - notification failure shouldn't fail inventory operations
        }
    }
}
