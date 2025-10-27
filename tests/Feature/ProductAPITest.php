<?php

declare(strict_types=1);

use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->store = Store::factory()->create();
    $this->user = User::factory()->create(['store_id' => $this->store->id]);
    $this->category = Category::factory()->create();

    $permission = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'manage-products']);
    $this->user->givePermissionTo($permission);
    Sanctum::actingAs($this->user, ['*']);
});

describe('Product API - Create', function () {
    test('can create product via API', function () {
        $response = $this->postJson('/api/products', [
            'name' => 'Test Product',
            'sku' => 'TEST-001',
            'barcode' => '123456789',
            'description' => 'Test product description',
            'category_id' => $this->category->id,
            'price' => 99.99,
            'cost' => 50.00,
            'store_id' => $this->store->id,
            'active' => true,
            'track_stock' => true,
            'stock_quantity' => 100,
            'reorder_point' => 10,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'sku',
                    'price',
                ],
            ]);

        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'sku' => 'TEST-001',
            'price' => 99.99,
        ]);
    });

    test('validates required fields', function () {
        $response = $this->postJson('/api/products', [
            // Missing required fields
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'sku', 'category_id', 'price', 'store_id']);
    });

    test('validates unique SKU', function () {
        Product::factory()->create([
            'sku' => 'DUPLICATE-SKU',
            'store_id' => $this->store->id,
        ]);

        $response = $this->postJson('/api/products', [
            'name' => 'Another Product',
            'sku' => 'DUPLICATE-SKU',
            'category_id' => $this->category->id,
            'price' => 50.00,
            'store_id' => $this->store->id,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['sku']);
    });

    test('validates unique barcode', function () {
        Product::factory()->create([
            'barcode' => '999888777',
            'store_id' => $this->store->id,
        ]);

        $response = $this->postJson('/api/products', [
            'name' => 'Product with duplicate barcode',
            'sku' => 'UNIQUE-SKU',
            'barcode' => '999888777',
            'category_id' => $this->category->id,
            'price' => 50.00,
            'store_id' => $this->store->id,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['barcode']);
    });
});

describe('Product API - Read', function () {
    test('can retrieve single product', function () {
        $product = Product::factory()->create([
            'store_id' => $this->store->id,
            'name' => 'Retrievable Product',
        ]);

        $response = $this->getJson("/api/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $product->id,
                    'name' => 'Retrievable Product',
                ],
            ]);
    });

    test('can list all products', function () {
        Product::factory()->count(10)->create([
            'store_id' => $this->store->id,
        ]);

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJsonCount(10, 'data');
    });

    test('returns 404 for non-existent product', function () {
        $response = $this->getJson('/api/products/99999');

        $response->assertStatus(404);
    });
});

describe('Product API - Update', function () {
    test('can update product', function () {
        $product = Product::factory()->create([
            'store_id' => $this->store->id,
            'price' => 100.00,
        ]);

        $response = $this->putJson("/api/products/{$product->id}", [
            'price' => 120.00,
            'name' => 'Updated Product Name',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'price' => 120.00,
            'name' => 'Updated Product Name',
        ]);
    });

    test('validates unique SKU on update', function () {
        $product1 = Product::factory()->create([
            'sku' => 'SKU-001',
            'store_id' => $this->store->id,
        ]);

        $product2 = Product::factory()->create([
            'sku' => 'SKU-002',
            'store_id' => $this->store->id,
        ]);

        $response = $this->putJson("/api/products/{$product2->id}", [
            'sku' => 'SKU-001', // Trying to use product1's SKU
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['sku']);
    });
});

describe('Product API - Delete', function () {
    test('can delete product', function () {
        $product = Product::factory()->create([
            'store_id' => $this->store->id,
        ]);

        $response = $this->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(200);

        $this->assertSoftDeleted('products', [
            'id' => $product->id,
        ]);
    });
});

describe('Product API - Search', function () {
    test('can search products by name', function () {
        Product::factory()->create([
            'name' => 'Red Widget',
            'store_id' => $this->store->id,
        ]);

        Product::factory()->create([
            'name' => 'Blue Widget',
            'store_id' => $this->store->id,
        ]);

        Product::factory()->create([
            'name' => 'Green Gadget',
            'store_id' => $this->store->id,
        ]);

        $response = $this->getJson('/api/products?search=Widget');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    });

    test('can filter products by category', function () {
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();

        Product::factory()->count(3)->create([
            'category_id' => $category1->id,
            'store_id' => $this->store->id,
        ]);

        Product::factory()->count(2)->create([
            'category_id' => $category2->id,
            'store_id' => $this->store->id,
        ]);

        $response = $this->getJson("/api/products?category_id={$category1->id}");

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    });
});

describe('Product API - Authorization', function () {
    test('requires authentication', function () {
        // Clear authentication by creating a fresh test request without actingAs
        $this->app['auth']->forgetGuards();

        $response = $this->getJson('/api/products');

        $response->assertStatus(401);
    });

    test('requires permission to create product', function () {
        $unauthorizedUser = User::factory()->create(['store_id' => $this->store->id]);
        Sanctum::actingAs($unauthorizedUser, ['*']);

        $response = $this->postJson('/api/products', [
            'name' => 'Unauthorized Product',
            'sku' => 'UNAUTH-001',
            'category_id' => $this->category->id,
            'price' => 50.00,
            'store_id' => $this->store->id,
        ]);

        $response->assertStatus(403);
    });
});
