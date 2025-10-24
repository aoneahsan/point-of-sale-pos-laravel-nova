<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            [
                'name' => 'Apple',
                'slug' => 'apple',
                'description' => 'Premium electronics and technology products',
                'active' => true,
            ],
            [
                'name' => 'Samsung',
                'slug' => 'samsung',
                'description' => 'Consumer electronics and appliances',
                'active' => true,
            ],
            [
                'name' => 'Dell',
                'slug' => 'dell',
                'description' => 'Computer hardware and technology solutions',
                'active' => true,
            ],
            [
                'name' => 'HP',
                'slug' => 'hp',
                'description' => 'Computing and printing solutions',
                'active' => true,
            ],
            [
                'name' => 'Sony',
                'slug' => 'sony',
                'description' => 'Electronics and entertainment products',
                'active' => true,
            ],
            [
                'name' => 'Nike',
                'slug' => 'nike',
                'description' => 'Athletic footwear and apparel',
                'active' => true,
            ],
            [
                'name' => 'Adidas',
                'slug' => 'adidas',
                'description' => 'Sports clothing and accessories',
                'active' => true,
            ],
            [
                'name' => 'IKEA',
                'slug' => 'ikea',
                'description' => 'Furniture and home accessories',
                'active' => true,
            ],
            [
                'name' => 'KitchenAid',
                'slug' => 'kitchenaid',
                'description' => 'Kitchen appliances and accessories',
                'active' => true,
            ],
            [
                'name' => 'Generic',
                'slug' => 'generic',
                'description' => 'Generic or unbranded products',
                'active' => true,
            ],
        ];

        foreach ($brands as $brand) {
            Brand::create($brand);
        }
    }
}
