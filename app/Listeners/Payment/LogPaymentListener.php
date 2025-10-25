<?php

declare(strict_types=1);

namespace App\Listeners\Payment;

use App\Events\Payment\PaymentProcessedEvent;
use Illuminate\Support\Facades\Log;

/**
 * Log Payment Listener
 *
 * Logs payment processing for audit trail.
 * Runs synchronously to ensure immediate logging.
 *
 * @see PaymentProcessedEvent
 */
final class LogPaymentListener
{
    /**
     * Handle the event.
     *
     * @param  PaymentProcessedEvent  $event
     * @return void
     */
    public function handle(PaymentProcessedEvent $event): void
    {
        $payment = $event->payment;

        Log::info('Payment processed', [
            'payment_id' => $payment->id,
            'sale_id' => $payment->sale_id,
            'payment_method_id' => $payment->payment_method_id,
            'amount' => $payment->amount,
            'reference' => $payment->reference,
            'user_id' => $payment->sale->user_id ?? null,
            'store_id' => $payment->sale->store_id ?? null,
        ]);

        // Additional audit logging can be added here
        // e.g., writing to a separate audit log table
    }
}
