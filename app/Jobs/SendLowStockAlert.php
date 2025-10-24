<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\InventoryService;
use App\Models\Store;

class SendLowStockAlert implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $store;

    public function __construct(Store $store)
    {
        $this->store = $store;
    }

    public function handle(InventoryService $inventoryService): void
    {
        $lowStockProducts = $inventoryService->getLowStockProducts($this->store->id);

        if ($lowStockProducts->isNotEmpty()) {
            \Log::warning('Low Stock Alert', [
                'store' => $this->store->name,
                'products' => $lowStockProducts->count(),
            ]);
        }
    }
}