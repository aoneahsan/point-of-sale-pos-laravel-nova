<?php

use App\Models\Sale;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\Store;
use App\Services\SaleService;

beforeEach(function () {
    $this->store = Store::factory()->create();
    $this->user = User::factory()->create(['store_id' => $this->store->id]);
    $this->variant = ProductVariant::factory()->create(['store_id' => $this->store->id, 'stock' => 100]);
    $this->saleService = app(SaleService::class);
});

test('can create a sale', function () {
    $data = [
        'store_id' => $this->store->id,
        'user_id' => $this->user->id,
        'customer_id' => null,
        'items' => [
            [
                'product_variant_id' => $this->variant->id,
                'quantity' => 2,
                'discount' => 0,
            ],
        ],
    ];

    $sale = $this->saleService->createSale($data);

    expect($sale)->toBeInstanceOf(Sale::class)
        ->and($sale->items)->toHaveCount(1)
        ->and($sale->reference)->not()->toBeNull();
});

test('calculates sale totals correctly', function () {
    $data = [
        'store_id' => $this->store->id,
        'user_id' => $this->user->id,
        'customer_id' => null,
        'discount' => 10,
        'items' => [
            [
                'product_variant_id' => $this->variant->id,
                'quantity' => 2,
                'discount' => 0,
            ],
        ],
    ];

    $sale = $this->saleService->createSale($data);

    expect($sale->subtotal)->toBeGreaterThan(0)
        ->and($sale->total)->toBeLessThan($sale->subtotal);
});