<?php

declare(strict_types=1);

use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->store = Store::factory()->create();
    $this->user = User::factory()->create(['store_id' => $this->store->id]);
    $this->product = Product::factory()->create([
        'store_id' => $this->store->id,
        'price' => 100.00,
        'stock_quantity' => 50,
        'track_stock' => true,
    ]);

    // Create a completed sale
    $this->sale = Sale::factory()->create([
        'store_id' => $this->store->id,
        'user_id' => $this->user->id,
        'total' => 200.00,
        'status' => 'completed',
    ]);

    $this->saleItem = SaleItem::factory()->create([
        'sale_id' => $this->sale->id,
        'product_id' => $this->product->id,
        'quantity' => 2,
        'price' => 100.00,
    ]);

    $paymentMethod = PaymentMethod::factory()->create();
    $this->sale->payments()->create([
        'payment_method_id' => $paymentMethod->id,
        'amount' => 200.00,
    ]);

    // Give user permissions
    $this->user->givePermissionTo('process-refunds');
    Sanctum::actingAs($this->user, ['*']);
});

describe('Refund Processing', function () {
    test('can process full refund', function () {
        $response = $this->postJson("/api/sales/{$this->sale->id}/refund", [
            'items' => [
                [
                    'sale_item_id' => $this->saleItem->id,
                    'quantity' => 2,
                    'reason' => 'customer_request',
                ],
            ],
            'reason' => 'Customer requested full refund',
        ]);

        $response->assertStatus(200);

        // Verify return was created
        $this->assertDatabaseHas('returns', [
            'sale_id' => $this->sale->id,
            'reason' => 'Customer requested full refund',
        ]);

        // Verify return items were created
        $this->assertDatabaseHas('return_items', [
            'sale_item_id' => $this->saleItem->id,
            'quantity' => 2,
        ]);
    });

    test('can process partial refund', function () {
        $response = $this->postJson("/api/sales/{$this->sale->id}/refund", [
            'items' => [
                [
                    'sale_item_id' => $this->saleItem->id,
                    'quantity' => 1, // Only 1 of 2 items
                    'reason' => 'defective',
                ],
            ],
            'reason' => 'One item was defective',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('return_items', [
            'sale_item_id' => $this->saleItem->id,
            'quantity' => 1,
        ]);
    });

    test('restores inventory after refund', function () {
        $initialStock = $this->product->stock_quantity;

        $this->postJson("/api/sales/{$this->sale->id}/refund", [
            'items' => [
                [
                    'sale_item_id' => $this->saleItem->id,
                    'quantity' => 2,
                    'reason' => 'customer_request',
                ],
            ],
            'reason' => 'Full refund',
        ]);

        $this->product->refresh();

        expect($this->product->stock_quantity)->toBe($initialStock + 2);
    });

    test('prevents refunding more than purchased', function () {
        $response = $this->postJson("/api/sales/{$this->sale->id}/refund", [
            'items' => [
                [
                    'sale_item_id' => $this->saleItem->id,
                    'quantity' => 5, // Purchased only 2
                    'reason' => 'customer_request',
                ],
            ],
            'reason' => 'Invalid refund attempt',
        ]);

        $response->assertStatus(400);
    });
});

describe('Refund Validation', function () {
    test('requires refund reason', function () {
        $response = $this->postJson("/api/sales/{$this->sale->id}/refund", [
            'items' => [
                [
                    'sale_item_id' => $this->saleItem->id,
                    'quantity' => 1,
                    'reason' => 'customer_request',
                ],
            ],
            // Missing overall reason
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['reason']);
    });

    test('requires valid refund item reason', function () {
        $response = $this->postJson("/api/sales/{$this->sale->id}/refund", [
            'items' => [
                [
                    'sale_item_id' => $this->saleItem->id,
                    'quantity' => 1,
                    'reason' => 'invalid_reason',
                ],
            ],
            'reason' => 'Test refund',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['items.0.reason']);
    });

    test('requires at least one item to refund', function () {
        $response = $this->postJson("/api/sales/{$this->sale->id}/refund", [
            'items' => [],
            'reason' => 'Empty refund',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['items']);
    });
});

describe('Refund Authorization', function () {
    test('requires refund permission', function () {
        // Create user without refund permission
        $unauthorizedUser = User::factory()->create(['store_id' => $this->store->id]);
        Sanctum::actingAs($unauthorizedUser, ['*']);

        $response = $this->postJson("/api/sales/{$this->sale->id}/refund", [
            'items' => [
                [
                    'sale_item_id' => $this->saleItem->id,
                    'quantity' => 1,
                    'reason' => 'customer_request',
                ],
            ],
            'reason' => 'Unauthorized refund attempt',
        ]);

        $response->assertStatus(403);
    });
});
