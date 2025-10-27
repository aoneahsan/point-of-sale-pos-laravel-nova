<?php

use App\Models\User;
use App\Models\Product;
use App\Models\Store;
use App\Models\Category;
use Spatie\Permission\Models\Permission;

test('can list products via api', function () {
    $user = User::factory()->create();

    // Give user the view-products permission
    $permission = Permission::firstOrCreate(['name' => 'view-products']);
    $user->givePermissionTo($permission);

    Product::factory()->count(5)->create();

    $response = $this->actingAs($user, 'sanctum')
        ->getJson('/api/v1/products');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'sku']
            ]
        ]);
});

test('can create product via api', function () {
    $user = User::factory()->create();
    $store = Store::factory()->create();
    $category = Category::factory()->create();

    // Give user the manage-products permission
    $permission = Permission::firstOrCreate(['name' => 'manage-products']);
    $user->givePermissionTo($permission);

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/products', [
            'name' => 'New Product',
            'sku' => 'NEW-001',
            'category_id' => $category->id,
            'store_id' => $store->id,
            'price' => 99.99,
        ]);

    $response->assertStatus(201)
        ->assertJsonFragment(['name' => 'New Product']);
});