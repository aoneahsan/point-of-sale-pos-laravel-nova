<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\Customer\LoyaltyPointsEarnedEvent;
use App\Events\Inventory\LowStockDetectedEvent;
use App\Events\Inventory\StockAdjustedEvent;
use App\Events\Payment\PaymentProcessedEvent;
use App\Events\Sale\SaleCreatedEvent;
use App\Events\Sale\SaleRefundedEvent;
use App\Listeners\Customer\NotifyLoyaltyPointsEarnedListener;
use App\Listeners\Inventory\CheckLowStockListener;
use App\Listeners\Inventory\NotifyLowStockListener;
use App\Listeners\Payment\LogPaymentListener;
use App\Listeners\Sale\SendReceiptEmailListener;
use App\Listeners\Sale\UpdateInventoryOnRefundListener;
use App\Listeners\Sale\UpdateInventoryOnSaleListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

/**
 * Event Service Provider
 *
 * Registers all domain events and their listeners.
 * Provides centralized event-driven architecture for the POS system.
 */
final class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        // Sale Events
        SaleCreatedEvent::class => [
            UpdateInventoryOnSaleListener::class,
            SendReceiptEmailListener::class,
        ],

        SaleRefundedEvent::class => [
            UpdateInventoryOnRefundListener::class,
        ],

        // Payment Events
        PaymentProcessedEvent::class => [
            LogPaymentListener::class,
        ],

        // Inventory Events
        StockAdjustedEvent::class => [
            CheckLowStockListener::class,
        ],

        LowStockDetectedEvent::class => [
            NotifyLowStockListener::class,
        ],

        // Customer Events
        LoyaltyPointsEarnedEvent::class => [
            NotifyLoyaltyPointsEarnedListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false; // Explicitly define events for better control
    }
}
