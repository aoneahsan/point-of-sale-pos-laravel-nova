<?php

declare(strict_types=1);

use App\Models\Customer;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Store;
use App\Models\User;
use App\Services\SaleService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->service = app(SaleService::class);

    $this->store = Store::factory()->create();
    $this->user = User::factory()->create(['store_id' => $this->store->id]);
    $this->customer = Customer::factory()->create(['store_id' => $this->store->id]);
    $this->product = Product::factory()->create([
        'store_id' => $this->store->id,
        'price' => 100.00,
        'cost' => 50.00,
        'stock_quantity' => 100,
        'track_stock' => true,
    ]);
    $this->paymentMethod = PaymentMethod::factory()->create([
        'name' => 'Cash',
        'active' => true,
    ]);
});

describe('Sale Creation', function () {
    test('can create a basic sale with single item', function () {
        $saleData = [
            'store_id' => $this->store->id,
            'customer_id' => $this->customer->id,
            'user_id' => $this->user->id,
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 2,
                    'price' => 100.00,
                ],
            ],
            'payments' => [
                [
                    'payment_method_id' => $this->paymentMethod->id,
                    'amount' => 200.00,
                ],
            ],
        ];

        $sale = $this->service->createSale($saleData);

        expect($sale)->toBeInstanceOf(Sale::class)
            ->and($sale->total)->toEqual(200.00)
            ->and($sale->items)->toHaveCount(1)
            ->and($sale->payments)->toHaveCount(1)
            ->and($sale->status)->toBe('completed');
    });

    test('can create sale with multiple items', function () {
        $product2 = Product::factory()->create([
            'store_id' => $this->store->id,
            'price' => 50.00,
            'stock_quantity' => 50,
        ]);

        $saleData = [
            'store_id' => $this->store->id,
            'user_id' => $this->user->id,
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 1,
                    'price' => 100.00,
                ],
                [
                    'product_id' => $product2->id,
                    'quantity' => 3,
                    'price' => 50.00,
                ],
            ],
            'payments' => [
                [
                    'payment_method_id' => $this->paymentMethod->id,
                    'amount' => 250.00,
                ],
            ],
        ];

        $sale = $this->service->createSale($saleData);

        expect($sale->items)->toHaveCount(2)
            ->and($sale->total)->toEqual(250.00);
    });

    test('can create sale without customer', function () {
        $saleData = [
            'store_id' => $this->store->id,
            'user_id' => $this->user->id,
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 1,
                    'price' => 100.00,
                ],
            ],
            'payments' => [
                [
                    'payment_method_id' => $this->paymentMethod->id,
                    'amount' => 100.00,
                ],
            ],
        ];

        $sale = $this->service->createSale($saleData);

        expect($sale->customer_id)->toBeNull()
            ->and($sale->total)->toEqual(100.00);
    });

    test('calculates subtotal and total correctly', function () {
        $saleData = [
            'store_id' => $this->store->id,
            'user_id' => $this->user->id,
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 3,
                    'price' => 100.00,
                ],
            ],
            'payments' => [
                [
                    'payment_method_id' => $this->paymentMethod->id,
                    'amount' => 300.00,
                ],
            ],
        ];

        $sale = $this->service->createSale($saleData);

        expect($sale->subtotal)->toEqual(300.00)
            ->and($sale->total)->toEqual(300.00);
    });
});

describe('Sale with Split Payments', function () {
    test('can create sale with multiple payment methods', function () {
        $cardPayment = PaymentMethod::factory()->create(['name' => 'Credit Card']);

        $saleData = [
            'store_id' => $this->store->id,
            'user_id' => $this->user->id,
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 2,
                    'price' => 100.00,
                ],
            ],
            'payments' => [
                [
                    'payment_method_id' => $this->paymentMethod->id,
                    'amount' => 100.00,
                ],
                [
                    'payment_method_id' => $cardPayment->id,
                    'amount' => 100.00,
                ],
            ],
        ];

        $sale = $this->service->createSale($saleData);

        expect($sale->payments)->toHaveCount(2)
            ->and($sale->payments->sum('amount'))->toEqual(200.00);
    });
});

describe('Sale Calculations', function () {
    test('calculates profit margin correctly', function () {
        $saleData = [
            'store_id' => $this->store->id,
            'user_id' => $this->user->id,
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 2,
                    'price' => 100.00,
                    'cost' => 50.00,
                ],
            ],
            'payments' => [
                [
                    'payment_method_id' => $this->paymentMethod->id,
                    'amount' => 200.00,
                ],
            ],
        ];

        $sale = $this->service->createSale($saleData);

        // Profit should be (100 - 50) * 2 = 100
        $expectedProfit = 100.00;

        expect($sale->items->first()->profit)->toBe($expectedProfit);
    });
});

describe('Sale Validation', function () {
    test('throws exception for insufficient stock', function () {
        $this->product->update(['stock_quantity' => 1]);

        $saleData = [
            'store_id' => $this->store->id,
            'user_id' => $this->user->id,
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 10, // More than available
                    'price' => 100.00,
                ],
            ],
            'payments' => [
                [
                    'payment_method_id' => $this->paymentMethod->id,
                    'amount' => 1000.00,
                ],
            ],
        ];

        expect(fn() => $this->service->createSale($saleData))
            ->toThrow(\App\Exceptions\Inventory\InsufficientStockException::class);
    });
});
