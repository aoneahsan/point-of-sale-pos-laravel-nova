<?php

declare(strict_types=1);

namespace App\Events\Customer;

use App\Models\Customer;
use App\Models\Sale;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Loyalty Points Earned Event
 *
 * Fired when a customer earns loyalty points from a purchase.
 * This event can trigger notifications to the customer.
 *
 * @property Customer $customer The customer who earned points
 * @property int $points Number of points earned
 * @property Sale $sale The sale that generated the points
 */
final class LoyaltyPointsEarnedEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  Customer  $customer The customer
     * @param  int  $points Points earned
     * @param  Sale  $sale The sale
     */
    public function __construct(
        public readonly Customer $customer,
        public readonly int $points,
        public readonly Sale $sale
    ) {
    }
}
