<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductVariantFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'store_id' => Store::factory(),
            'name' => fake()->word(),
            'sku' => fake()->unique()->regexify('[A-Z]{3}-[0-9]{5}-VAR'),
            'barcode' => fake()->unique()->ean13(),
            'price' => fake()->randomFloat(2, 10, 1000),
            'cost' => fake()->randomFloat(2, 5, 500),
            'stock' => fake()->numberBetween(0, 100),
            'low_stock_threshold' => 10,
            'attributes' => [],
        ];
    }
}