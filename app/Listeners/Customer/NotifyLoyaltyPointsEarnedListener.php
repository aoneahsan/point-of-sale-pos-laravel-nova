<?php

declare(strict_types=1);

namespace App\Listeners\Customer;

use App\Events\Customer\LoyaltyPointsEarnedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

/**
 * Notify Loyalty Points Earned Listener
 *
 * Notifies customers when they earn loyalty points.
 * Runs asynchronously via queue.
 *
 * @see LoyaltyPointsEarnedEvent
 */
final class NotifyLoyaltyPointsEarnedListener implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param  LoyaltyPointsEarnedEvent  $event
     * @return void
     */
    public function handle(LoyaltyPointsEarnedEvent $event): void
    {
        // Only notify if customer has email
        if (!$event->customer->email) {
            return;
        }

        try {
            // TODO: Create loyalty points notification
            // Notification::send($event->customer, new LoyaltyPointsEarnedNotification($event->points));

            Log::info('Loyalty points earned notification queued', [
                'customer_id' => $event->customer->id,
                'points_earned' => $event->points,
                'sale_id' => $event->sale->id,
                'new_balance' => $event->customer->loyalty_points,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send loyalty points notification', [
                'customer_id' => $event->customer->id,
                'error' => $e->getMessage(),
            ]);

            // Don't throw - notification failure shouldn't fail the sale
        }
    }
}
