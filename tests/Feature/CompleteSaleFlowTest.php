<?php

declare(strict_types=1);

use App\Models\Customer;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->store = Store::factory()->create();
    $this->user = User::factory()->create([
        'store_id' => $this->store->id,
    ]);
    $this->customer = Customer::factory()->create([
        'store_id' => $this->store->id,
        'email' => 'customer@example.com',
    ]);
    $this->product1 = Product::factory()->create([
        'store_id' => $this->store->id,
        'name' => 'Product 1',
        'price' => 50.00,
        'cost' => 25.00,
        'stock_quantity' => 100,
        'track_stock' => true,
    ]);
    $this->product2 = Product::factory()->create([
        'store_id' => $this->store->id,
        'name' => 'Product 2',
        'price' => 30.00,
        'cost' => 15.00,
        'stock_quantity' => 50,
        'track_stock' => true,
    ]);
    $this->paymentMethod = PaymentMethod::factory()->create([
        'name' => 'Cash',
        'code' => 'cash',
        'is_active' => true,
    ]);

    // Give user required permissions
    $this->user->givePermissionTo('process-sales');

    // Authenticate user for API tests
    Sanctum::actingAs($this->user, ['*']);
});

describe('Complete Sale Flow - API', function () {
    test('can create sale with single item via API', function () {
        $response = $this->postJson('/api/sales', [
            'store_id' => $this->store->id,
            'customer_id' => $this->customer->id,
            'items' => [
                [
                    'product_id' => $this->product1->id,
                    'quantity' => 2,
                    'price' => 50.00,
                ],
            ],
            'payments' => [
                [
                    'payment_method_id' => $this->paymentMethod->id,
                    'amount' => 100.00,
                ],
            ],
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'total',
                    'status',
                    'items',
                    'payments',
                ],
            ]);

        // Verify sale was created
        $this->assertDatabaseHas('sales', [
            'store_id' => $this->store->id,
            'customer_id' => $this->customer->id,
            'total' => 100.00,
            'status' => 'completed',
        ]);

        // Verify sale items were created
        $this->assertDatabaseHas('sale_items', [
            'product_id' => $this->product1->id,
            'quantity' => 2,
            'price' => 50.00,
        ]);

        // Verify payment was recorded
        $this->assertDatabaseHas('sale_payments', [
            'payment_method_id' => $this->paymentMethod->id,
            'amount' => 100.00,
        ]);
    });

    test('can create sale with multiple items', function () {
        $response = $this->postJson('/api/sales', [
            'store_id' => $this->store->id,
            'items' => [
                [
                    'product_id' => $this->product1->id,
                    'quantity' => 2,
                    'price' => 50.00,
                ],
                [
                    'product_id' => $this->product2->id,
                    'quantity' => 3,
                    'price' => 30.00,
                ],
            ],
            'payments' => [
                [
                    'payment_method_id' => $this->paymentMethod->id,
                    'amount' => 190.00, // (2 * 50) + (3 * 30) = 190
                ],
            ],
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('sales', [
            'total' => 190.00,
        ]);
    });

    test('deducts inventory after sale', function () {
        $initialStock = $this->product1->stock_quantity;

        $this->postJson('/api/sales', [
            'store_id' => $this->store->id,
            'items' => [
                [
                    'product_id' => $this->product1->id,
                    'quantity' => 5,
                    'price' => 50.00,
                ],
            ],
            'payments' => [
                [
                    'payment_method_id' => $this->paymentMethod->id,
                    'amount' => 250.00,
                ],
            ],
        ]);

        // Refresh product to get updated stock
        $this->product1->refresh();

        expect($this->product1->stock_quantity)->toBe($initialStock - 5);
    });

    test('prevents sale when insufficient stock', function () {
        $this->product1->update(['stock_quantity' => 2]);

        $response = $this->postJson('/api/sales', [
            'store_id' => $this->store->id,
            'items' => [
                [
                    'product_id' => $this->product1->id,
                    'quantity' => 10, // More than available
                    'price' => 50.00,
                ],
            ],
            'payments' => [
                [
                    'payment_method_id' => $this->paymentMethod->id,
                    'amount' => 500.00,
                ],
            ],
        ]);

        $response->assertStatus(400);
    });

    test('can create sale with split payment', function () {
        $cardPayment = PaymentMethod::factory()->create([
            'name' => 'Credit Card',
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/sales', [
            'store_id' => $this->store->id,
            'items' => [
                [
                    'product_id' => $this->product1->id,
                    'quantity' => 2,
                    'price' => 50.00,
                ],
            ],
            'payments' => [
                [
                    'payment_method_id' => $this->paymentMethod->id,
                    'amount' => 60.00,
                ],
                [
                    'payment_method_id' => $cardPayment->id,
                    'amount' => 40.00,
                ],
            ],
        ]);

        $response->assertStatus(201);

        // Verify both payments were recorded
        $sale = \App\Models\Sale::latest()->first();
        expect($sale->payments)->toHaveCount(2)
            ->and($sale->payments->sum('amount'))->toBe(100.00);
    });
});

describe('Sale Validation', function () {
    test('requires items for sale creation', function () {
        $response = $this->postJson('/api/sales', [
            'store_id' => $this->store->id,
            'items' => [], // Empty items
            'payments' => [
                [
                    'payment_method_id' => $this->paymentMethod->id,
                    'amount' => 100.00,
                ],
            ],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['items']);
    });

    test('requires payments for sale creation', function () {
        $response = $this->postJson('/api/sales', [
            'store_id' => $this->store->id,
            'items' => [
                [
                    'product_id' => $this->product1->id,
                    'quantity' => 1,
                    'price' => 50.00,
                ],
            ],
            'payments' => [], // Empty payments
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['payments']);
    });

    test('validates payment total matches sale total', function () {
        $response = $this->postJson('/api/sales', [
            'store_id' => $this->store->id,
            'items' => [
                [
                    'product_id' => $this->product1->id,
                    'quantity' => 2,
                    'price' => 50.00,
                ],
            ],
            'payments' => [
                [
                    'payment_method_id' => $this->paymentMethod->id,
                    'amount' => 50.00, // Only 50, but total is 100
                ],
            ],
        ]);

        $response->assertStatus(422);
    });
});

describe('Sale Retrieval', function () {
    test('can retrieve sale details', function () {
        $sale = \App\Models\Sale::factory()->create([
            'store_id' => $this->store->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->getJson("/api/sales/{$sale->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'total',
                    'status',
                ],
            ]);
    });

    test('can list all sales', function () {
        \App\Models\Sale::factory()->count(5)->create([
            'store_id' => $this->store->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->getJson('/api/sales');

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data');
    });
});
