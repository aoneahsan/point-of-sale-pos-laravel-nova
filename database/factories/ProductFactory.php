<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Brand;
use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->words(3, true);
        $cost = fake()->randomFloat(2, 10, 500);
        $price = $cost * fake()->randomFloat(2, 1.5, 3); // 50% to 200% markup

        return [
            'store_id' => Store::factory(),
            'category_id' => Category::factory(),
            'brand_id' => Brand::factory(),
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'sku' => fake()->unique()->regexify('[A-Z]{3}-[0-9]{5}'),
            'barcode' => fake()->unique()->ean13(),
            'description' => fake()->paragraph(),
            'unit' => 'piece',
            'price' => round($price, 2),
            'cost' => round($cost, 2),
            'stock_quantity' => fake()->numberBetween(0, 200),
            'reorder_point' => fake()->numberBetween(5, 20),
            'track_stock' => true,
            'active' => true,
            'featured' => fake()->boolean(20),
            'track_inventory' => true,
        ];
    }
}