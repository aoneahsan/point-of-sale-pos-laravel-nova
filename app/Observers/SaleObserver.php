<?php

namespace App\Observers;

use App\Models\Sale;

class SaleObserver
{
    public function creating(Sale $sale): void
    {
        if (!$sale->reference) {
            $prefix = config('pos.sale_reference_prefix', 'SALE');
            $number = str_pad(Sale::count() + 1, 6, '0', STR_PAD_LEFT);
            $sale->reference = "{$prefix}-{$number}-" . date('Ymd');
        }
    }

    public function created(Sale $sale): void
    {
        // Log sale creation
    }

    public function updated(Sale $sale): void
    {
        if ($sale->wasChanged('status') && $sale->status === Sale::STATUS_COMPLETED) {
            // Handle sale completion events
        }
    }
}