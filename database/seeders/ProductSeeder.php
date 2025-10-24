<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Store;
use App\Models\TaxRate;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $mainStore = Store::where('code', 'MAIN-001')->first();
        $northStore = Store::where('code', 'NORTH-001')->first();
        $southStore = Store::where('code', 'SOUTH-001')->first();

        $smartphones = Category::where('slug', 'smartphones')->first();
        $laptops = Category::where('slug', 'laptops')->first();
        $accessories = Category::where('slug', 'accessories')->first();
        $mensClothing = Category::where('slug', 'mens-clothing')->first();
        $furniture = Category::where('slug', 'furniture')->first();

        $apple = Brand::where('slug', 'apple')->first();
        $samsung = Brand::where('slug', 'samsung')->first();
        $dell = Brand::where('slug', 'dell')->first();
        $nike = Brand::where('slug', 'nike')->first();
        $ikea = Brand::where('slug', 'ikea')->first();

        $standardTax = TaxRate::where('code', 'STANDARD')->first();
        $reducedTax = TaxRate::where('code', 'REDUCED')->first();

        // Product 1: iPhone 15 Pro
        $iphone = Product::create([
            'category_id' => $smartphones->id,
            'brand_id' => $apple->id,
            'tax_rate_id' => $standardTax->id,
            'name' => 'iPhone 15 Pro',
            'slug' => 'iphone-15-pro',
            'description' => 'Latest iPhone with advanced features and ProMotion display',
            'sku' => 'APPL-IPH15PRO',
            'barcode' => '1234567890123',
            'unit' => 'piece',
            'active' => true,
            'featured' => true,
            'track_inventory' => true,
        ]);

        // Variants for iPhone 15 Pro
        foreach (['128GB', '256GB', '512GB'] as $storage) {
            foreach (['Black', 'White', 'Blue'] as $color) {
                ProductVariant::create([
                    'product_id' => $iphone->id,
                    'store_id' => $mainStore->id,
                    'name' => "{$storage} {$color}",
                    'sku' => "APPL-IPH15PRO-{$storage}-{$color}",
                    'barcode' => '1234567' . rand(100000, 999999),
                    'price' => $storage === '128GB' ? 999.00 : ($storage === '256GB' ? 1099.00 : 1299.00),
                    'cost' => $storage === '128GB' ? 750.00 : ($storage === '256GB' ? 850.00 : 1000.00),
                    'stock' => rand(10, 50),
                    'low_stock_threshold' => 5,
                    'attributes' => ['storage' => $storage, 'color' => $color],
                ]);
            }
        }

        // Product 2: Samsung Galaxy S24
        $galaxy = Product::create([
            'category_id' => $smartphones->id,
            'brand_id' => $samsung->id,
            'tax_rate_id' => $standardTax->id,
            'name' => 'Samsung Galaxy S24',
            'slug' => 'samsung-galaxy-s24',
            'description' => 'Flagship Samsung smartphone with AI features',
            'sku' => 'SAMS-GALS24',
            'barcode' => '2234567890124',
            'unit' => 'piece',
            'active' => true,
            'featured' => true,
            'track_inventory' => true,
        ]);

        foreach (['128GB', '256GB'] as $storage) {
            ProductVariant::create([
                'product_id' => $galaxy->id,
                'store_id' => $mainStore->id,
                'name' => $storage,
                'sku' => "SAMS-GALS24-{$storage}",
                'barcode' => '2234567' . rand(100000, 999999),
                'price' => $storage === '128GB' ? 849.00 : 949.00,
                'cost' => $storage === '128GB' ? 650.00 : 750.00,
                'stock' => rand(15, 40),
                'low_stock_threshold' => 5,
                'attributes' => ['storage' => $storage],
            ]);
        }

        // Product 3: Dell XPS 15
        $dellLaptop = Product::create([
            'category_id' => $laptops->id,
            'brand_id' => $dell->id,
            'tax_rate_id' => $standardTax->id,
            'name' => 'Dell XPS 15',
            'slug' => 'dell-xps-15',
            'description' => 'Premium laptop with stunning display',
            'sku' => 'DELL-XPS15',
            'barcode' => '3234567890125',
            'unit' => 'piece',
            'active' => true,
            'featured' => true,
            'track_inventory' => true,
        ]);

        ProductVariant::create([
            'product_id' => $dellLaptop->id,
            'store_id' => $mainStore->id,
            'name' => 'i7 16GB 512GB',
            'sku' => 'DELL-XPS15-I7-16-512',
            'barcode' => '3234567890125',
            'price' => 1499.00,
            'cost' => 1200.00,
            'stock' => rand(5, 20),
            'low_stock_threshold' => 3,
            'attributes' => ['processor' => 'i7', 'ram' => '16GB', 'storage' => '512GB'],
        ]);

        // Product 4: Nike Air Max
        $shoes = Product::create([
            'category_id' => $mensClothing->id,
            'brand_id' => $nike->id,
            'tax_rate_id' => $reducedTax->id,
            'name' => 'Nike Air Max 2024',
            'slug' => 'nike-air-max-2024',
            'description' => 'Comfortable running shoes with air cushioning',
            'sku' => 'NIKE-AIRMAX24',
            'barcode' => '4234567890126',
            'unit' => 'pair',
            'active' => true,
            'featured' => false,
            'track_inventory' => true,
        ]);

        foreach ([8, 9, 10, 11, 12] as $size) {
            ProductVariant::create([
                'product_id' => $shoes->id,
                'store_id' => $mainStore->id,
                'name' => "Size {$size}",
                'sku' => "NIKE-AIRMAX24-{$size}",
                'barcode' => '42345678' . str_pad($size, 5, '0', STR_PAD_LEFT),
                'price' => 129.99,
                'cost' => 80.00,
                'stock' => rand(20, 50),
                'low_stock_threshold' => 10,
                'attributes' => ['size' => $size],
            ]);
        }

        // Product 5: IKEA Office Desk
        $desk = Product::create([
            'category_id' => $furniture->id,
            'brand_id' => $ikea->id,
            'tax_rate_id' => $standardTax->id,
            'name' => 'IKEA BEKANT Desk',
            'slug' => 'ikea-bekant-desk',
            'description' => 'Adjustable office desk with cable management',
            'sku' => 'IKEA-BEKANT',
            'barcode' => '5234567890127',
            'unit' => 'piece',
            'active' => true,
            'featured' => false,
            'track_inventory' => true,
        ]);

        ProductVariant::create([
            'product_id' => $desk->id,
            'store_id' => $mainStore->id,
            'name' => 'White 160x80cm',
            'sku' => 'IKEA-BEKANT-WHT-160',
            'barcode' => '5234567890127',
            'price' => 299.00,
            'cost' => 200.00,
            'stock' => rand(10, 25),
            'low_stock_threshold' => 5,
            'attributes' => ['color' => 'White', 'dimensions' => '160x80cm'],
        ]);

        // Create some variants for other stores (North and South)
        ProductVariant::create([
            'product_id' => $iphone->id,
            'store_id' => $northStore->id,
            'name' => '128GB Black',
            'sku' => 'APPL-IPH15PRO-128GB-Black-NORTH',
            'barcode' => '1234567' . rand(100000, 999999),
            'price' => 999.00,
            'cost' => 750.00,
            'stock' => rand(5, 20),
            'low_stock_threshold' => 5,
            'attributes' => ['storage' => '128GB', 'color' => 'Black'],
        ]);

        ProductVariant::create([
            'product_id' => $galaxy->id,
            'store_id' => $southStore->id,
            'name' => '128GB',
            'sku' => 'SAMS-GALS24-128GB-SOUTH',
            'barcode' => '2234567' . rand(100000, 999999),
            'price' => 849.00,
            'cost' => 650.00,
            'stock' => rand(5, 20),
            'low_stock_threshold' => 5,
            'attributes' => ['storage' => '128GB'],
        ]);
    }
}
