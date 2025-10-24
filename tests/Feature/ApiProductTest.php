<?php

use App\Models\User;
use App\Models\Product;

test('can list products via api', function () {
    $user = User::factory()->create();
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

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/products', [
            'name' => 'New Product',
            'sku' => 'NEW-001',
        ]);

    $response->assertStatus(201)
        ->assertJsonFragment(['name' => 'New Product']);
});