<?php

declare(strict_types=1);

namespace App\Events\Payment;

use App\Models\SalePayment;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Payment Processed Event
 *
 * Fired when a payment is successfully processed.
 * This event triggers audit logging and payment gateway notifications.
 *
 * @property SalePayment $payment The processed payment
 */
final class PaymentProcessedEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  SalePayment  $payment The payment that was processed
     */
    public function __construct(
        public readonly SalePayment $payment
    ) {
    }
}
