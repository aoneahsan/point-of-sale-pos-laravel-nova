<?php

declare(strict_types=1);

use App\Models\Product;
use App\Models\Store;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->service = app(InventoryService::class);
    $this->store = Store::factory()->create();
    $this->product = Product::factory()->create([
        'store_id' => $this->store->id,
        'stock_quantity' => 100,
        'track_stock' => true,
        'reorder_point' => 10,
    ]);
});

describe('Stock Addition', function () {
    test('can add stock to product', function () {
        $initialStock = $this->product->stock_quantity;

        $this->service->addStock(
            product: $this->product,
            quantity: 50,
            reason: 'Purchase order received',
            reference: 'PO',
            referenceId: 1
        );

        $this->product->refresh();

        expect($this->product->stock_quantity)->toBe($initialStock + 50);
    });

    test('creates stock movement record when adding stock', function () {
        $this->service->addStock(
            product: $this->product,
            quantity: 50,
            reason: 'Restock',
            reference: 'manual',
            referenceId: null
        );

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $this->product->id,
            'quantity' => 50,
            'type' => 'in',
            'reason' => 'Restock',
        ]);
    });
});

describe('Stock Deduction', function () {
    test('can deduct stock from product', function () {
        $initialStock = $this->product->stock_quantity;

        $this->service->deductStock(
            product: $this->product,
            quantity: 30,
            reason: 'Sale',
            reference: 'sale',
            referenceId: 1
        );

        $this->product->refresh();

        expect($this->product->stock_quantity)->toBe($initialStock - 30);
    });

    test('throws exception when deducting more stock than available', function () {
        expect(fn() => $this->service->deductStock(
            product: $this->product,
            quantity: 200, // More than available (100)
            reason: 'Sale',
            reference: 'sale',
            referenceId: 1
        ))->toThrow(\App\Exceptions\Inventory\InsufficientStockException::class);
    });

    test('creates stock movement record when deducting stock', function () {
        $this->service->deductStock(
            product: $this->product,
            quantity: 20,
            reason: 'Sale',
            reference: 'sale',
            referenceId: 5
        );

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $this->product->id,
            'quantity' => -20,
            'type' => 'out',
            'reason' => 'Sale',
        ]);
    });
});

describe('Low Stock Detection', function () {
    test('detects when stock is below reorder point', function () {
        $this->product->update(['stock_quantity' => 5]); // Below reorder point of 10

        $isLowStock = $this->service->isLowStock($this->product);

        expect($isLowStock)->toBeTrue();
    });

    test('returns false when stock is above reorder point', function () {
        $this->product->update(['stock_quantity' => 50]); // Above reorder point of 10

        $isLowStock = $this->service->isLowStock($this->product);

        expect($isLowStock)->toBeFalse();
    });

    test('returns low stock products for a store', function () {
        // Create another product with low stock
        $lowStockProduct = Product::factory()->create([
            'store_id' => $this->store->id,
            'stock_quantity' => 3,
            'reorder_point' => 10,
            'track_stock' => true,
        ]);

        // Update existing product to have normal stock
        $this->product->update(['stock_quantity' => 100]);

        $lowStockProducts = $this->service->getLowStockProducts($this->store);

        expect($lowStockProducts)->toHaveCount(1)
            ->and($lowStockProducts->first()->id)->toBe($lowStockProduct->id);
    });
});

describe('Stock Validation', function () {
    test('validates stock availability for single product', function () {
        $isAvailable = $this->service->checkStockAvailability(
            product: $this->product,
            quantity: 50
        );

        expect($isAvailable)->toBeTrue();
    });

    test('returns false when stock is insufficient', function () {
        $isAvailable = $this->service->checkStockAvailability(
            product: $this->product,
            quantity: 200
        );

        expect($isAvailable)->toBeFalse();
    });

    test('skips stock check for products that dont track stock', function () {
        $this->product->update(['track_stock' => false]);

        $isAvailable = $this->service->checkStockAvailability(
            product: $this->product,
            quantity: 1000
        );

        expect($isAvailable)->toBeTrue();
    });
});

describe('Stock Adjustments', function () {
    test('can adjust stock quantity', function () {
        $this->service->adjustStock(
            product: $this->product,
            newQuantity: 150,
            reason: 'Physical count adjustment'
        );

        $this->product->refresh();

        expect($this->product->stock_quantity)->toBe(150);
    });

    test('creates adjustment record with correct difference', function () {
        $initialStock = $this->product->stock_quantity; // 100

        $this->service->adjustStock(
            product: $this->product,
            newQuantity: 85,
            reason: 'Inventory reconciliation'
        );

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $this->product->id,
            'quantity' => -15, // 85 - 100
            'type' => 'adjustment',
            'reason' => 'Inventory reconciliation',
        ]);
    });
});
