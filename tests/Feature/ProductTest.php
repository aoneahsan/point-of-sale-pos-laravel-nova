<?php

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;

test('can create a product', function () {
    $category = Category::factory()->create();
    $brand = Brand::factory()->create();

    $product = Product::create([
        'category_id' => $category->id,
        'brand_id' => $brand->id,
        'name' => 'Test Product',
        'slug' => 'test-product',
        'sku' => 'TEST-001',
        'active' => true,
    ]);

    expect($product)->toBeInstanceOf(Product::class)
        ->and($product->name)->toBe('Test Product');
});

test('product belongs to category', function () {
    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id]);

    expect($product->category)->toBeInstanceOf(Category::class)
        ->and($product->category->id)->toBe($category->id);
});

test('product search works', function () {
    Product::factory()->create(['name' => 'iPhone 15']);
    Product::factory()->create(['name' => 'Samsung Galaxy']);

    $results = Product::search('iPhone')->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()->name)->toContain('iPhone');
});