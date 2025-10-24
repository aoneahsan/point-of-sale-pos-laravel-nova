<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Electronics',
                'slug' => 'electronics',
                'description' => 'Electronic devices and accessories',
                'active' => true,
                'sort_order' => 1,
                'parent_id' => null,
            ],
            [
                'name' => 'Smartphones',
                'slug' => 'smartphones',
                'description' => 'Mobile phones and smartphones',
                'active' => true,
                'sort_order' => 1,
                'parent_id' => null, // Will be updated after Electronics is created
            ],
            [
                'name' => 'Laptops',
                'slug' => 'laptops',
                'description' => 'Laptop computers',
                'active' => true,
                'sort_order' => 2,
                'parent_id' => null,
            ],
            [
                'name' => 'Accessories',
                'slug' => 'accessories',
                'description' => 'Electronic accessories',
                'active' => true,
                'sort_order' => 3,
                'parent_id' => null,
            ],
            [
                'name' => 'Clothing',
                'slug' => 'clothing',
                'description' => 'Apparel and fashion items',
                'active' => true,
                'sort_order' => 2,
                'parent_id' => null,
            ],
            [
                'name' => 'Men\'s Clothing',
                'slug' => 'mens-clothing',
                'description' => 'Clothing for men',
                'active' => true,
                'sort_order' => 1,
                'parent_id' => null,
            ],
            [
                'name' => 'Women\'s Clothing',
                'slug' => 'womens-clothing',
                'description' => 'Clothing for women',
                'active' => true,
                'sort_order' => 2,
                'parent_id' => null,
            ],
            [
                'name' => 'Home & Kitchen',
                'slug' => 'home-kitchen',
                'description' => 'Home and kitchen products',
                'active' => true,
                'sort_order' => 3,
                'parent_id' => null,
            ],
            [
                'name' => 'Furniture',
                'slug' => 'furniture',
                'description' => 'Home and office furniture',
                'active' => true,
                'sort_order' => 1,
                'parent_id' => null,
            ],
            [
                'name' => 'Kitchenware',
                'slug' => 'kitchenware',
                'description' => 'Kitchen tools and appliances',
                'active' => true,
                'sort_order' => 2,
                'parent_id' => null,
            ],
            [
                'name' => 'Sports & Outdoors',
                'slug' => 'sports-outdoors',
                'description' => 'Sports equipment and outdoor gear',
                'active' => true,
                'sort_order' => 4,
                'parent_id' => null,
            ],
            [
                'name' => 'Books & Media',
                'slug' => 'books-media',
                'description' => 'Books, magazines, and media',
                'active' => true,
                'sort_order' => 5,
                'parent_id' => null,
            ],
        ];

        // Create main categories first
        $electronics = Category::create($categories[0]);
        $clothing = Category::create($categories[4]);
        $homeKitchen = Category::create($categories[7]);
        $sports = Category::create($categories[10]);
        $books = Category::create($categories[11]);

        // Create subcategories
        Category::create(array_merge($categories[1], ['parent_id' => $electronics->id]));
        Category::create(array_merge($categories[2], ['parent_id' => $electronics->id]));
        Category::create(array_merge($categories[3], ['parent_id' => $electronics->id]));

        Category::create(array_merge($categories[5], ['parent_id' => $clothing->id]));
        Category::create(array_merge($categories[6], ['parent_id' => $clothing->id]));

        Category::create(array_merge($categories[8], ['parent_id' => $homeKitchen->id]));
        Category::create(array_merge($categories[9], ['parent_id' => $homeKitchen->id]));
    }
}
