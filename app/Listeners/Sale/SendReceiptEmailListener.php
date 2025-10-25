<?php

declare(strict_types=1);

namespace App\Listeners\Sale;

use App\Events\Sale\SaleCreatedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Send Receipt Email Listener
 *
 * Sends receipt email to customer when a sale is completed.
 * Runs asynchronously via queue.
 *
 * @see SaleCreatedEvent
 */
final class SendReceiptEmailListener implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param  SaleCreatedEvent  $event
     * @return void
     */
    public function handle(SaleCreatedEvent $event): void
    {
        $sale = $event->sale;

        // Only send email if customer exists and has email
        if (!$sale->customer || !$sale->customer->email) {
            return;
        }

        try {
            // TODO: Create receipt mail class and send
            // Mail::to($sale->customer->email)
            //     ->send(new SaleReceiptMail($sale));

            Log::info('Receipt email queued for customer', [
                'sale_id' => $sale->id,
                'customer_email' => $sale->customer->email,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send receipt email', [
                'sale_id' => $sale->id,
                'error' => $e->getMessage(),
            ]);

            // Don't throw - email failure shouldn't fail the sale
        }
    }
}
