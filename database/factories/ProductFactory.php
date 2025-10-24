<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->words(3, true);
        
        return [
            'category_id' => Category::factory(),
            'brand_id' => Brand::factory(),
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'sku' => fake()->unique()->regexify('[A-Z]{3}-[0-9]{5}'),
            'barcode' => fake()->unique()->ean13(),
            'description' => fake()->paragraph(),
            'unit' => 'piece',
            'active' => true,
            'featured' => fake()->boolean(20),
            'track_inventory' => true,
        ];
    }
}