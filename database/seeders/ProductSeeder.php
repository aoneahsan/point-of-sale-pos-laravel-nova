<?php

namespace Database\Seeders;

use App\Models\Product;
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

        $products = [
            // Smartphones
            [
                'store_id' => $mainStore->id,
                'category_id' => $smartphones->id,
                'brand_id' => $apple->id,
                'tax_rate_id' => $standardTax->id,
                'name' => 'iPhone 15 Pro',
                'slug' => 'iphone-15-pro',
                'description' => 'Latest iPhone with advanced features and ProMotion display',
                'sku' => 'APPL-IPH15PRO',
                'barcode' => '1234567890123',
                'unit' => 'piece',
                'price' => 999.00,
                'cost' => 750.00,
                'stock_quantity' => 50,
                'reorder_point' => 10,
                'track_stock' => true,
                'active' => true,
                'featured' => true,
                'track_inventory' => true,
            ],
            [
                'store_id' => $mainStore->id,
                'category_id' => $smartphones->id,
                'brand_id' => $samsung->id,
                'tax_rate_id' => $standardTax->id,
                'name' => 'Samsung Galaxy S24',
                'slug' => 'samsung-galaxy-s24',
                'description' => 'Flagship Samsung smartphone with AI features',
                'sku' => 'SAMS-GALS24',
                'barcode' => '2234567890124',
                'unit' => 'piece',
                'price' => 849.00,
                'cost' => 650.00,
                'stock_quantity' => 40,
                'reorder_point' => 10,
                'track_stock' => true,
                'active' => true,
                'featured' => true,
                'track_inventory' => true,
            ],
            [
                'store_id' => $mainStore->id,
                'category_id' => $smartphones->id,
                'brand_id' => $apple->id,
                'tax_rate_id' => $standardTax->id,
                'name' => 'iPhone 14',
                'slug' => 'iphone-14',
                'description' => 'Previous generation iPhone with excellent performance',
                'sku' => 'APPL-IPH14',
                'barcode' => '3234567890125',
                'unit' => 'piece',
                'price' => 699.00,
                'cost' => 550.00,
                'stock_quantity' => 60,
                'reorder_point' => 15,
                'track_stock' => true,
                'active' => true,
                'featured' => false,
                'track_inventory' => true,
            ],

            // Laptops
            [
                'store_id' => $mainStore->id,
                'category_id' => $laptops->id,
                'brand_id' => $apple->id,
                'tax_rate_id' => $standardTax->id,
                'name' => 'MacBook Pro 16"',
                'slug' => 'macbook-pro-16',
                'description' => 'Powerful laptop for professionals with M3 Pro chip',
                'sku' => 'APPL-MBP16',
                'barcode' => '4234567890126',
                'unit' => 'piece',
                'price' => 2499.00,
                'cost' => 2000.00,
                'stock_quantity' => 25,
                'reorder_point' => 5,
                'track_stock' => true,
                'active' => true,
                'featured' => true,
                'track_inventory' => true,
            ],
            [
                'store_id' => $mainStore->id,
                'category_id' => $laptops->id,
                'brand_id' => $dell->id,
                'tax_rate_id' => $standardTax->id,
                'name' => 'Dell XPS 15',
                'slug' => 'dell-xps-15',
                'description' => 'Premium Windows laptop with stunning display',
                'sku' => 'DELL-XPS15',
                'barcode' => '5234567890127',
                'unit' => 'piece',
                'price' => 1799.00,
                'cost' => 1400.00,
                'stock_quantity' => 30,
                'reorder_point' => 8,
                'track_stock' => true,
                'active' => true,
                'featured' => false,
                'track_inventory' => true,
            ],

            // Accessories
            [
                'store_id' => $mainStore->id,
                'category_id' => $accessories->id,
                'brand_id' => $apple->id,
                'tax_rate_id' => $reducedTax->id,
                'name' => 'AirPods Pro',
                'slug' => 'airpods-pro',
                'description' => 'Premium wireless earbuds with active noise cancellation',
                'sku' => 'APPL-AIRPODSPRO',
                'barcode' => '6234567890128',
                'unit' => 'piece',
                'price' => 249.00,
                'cost' => 150.00,
                'stock_quantity' => 100,
                'reorder_point' => 20,
                'track_stock' => true,
                'active' => true,
                'featured' => true,
                'track_inventory' => true,
            ],
            [
                'store_id' => $mainStore->id,
                'category_id' => $accessories->id,
                'brand_id' => $apple->id,
                'tax_rate_id' => $reducedTax->id,
                'name' => 'Magic Mouse',
                'slug' => 'magic-mouse',
                'description' => 'Wireless rechargeable mouse with multi-touch surface',
                'sku' => 'APPL-MAGICMOUSE',
                'barcode' => '7234567890129',
                'unit' => 'piece',
                'price' => 79.00,
                'cost' => 45.00,
                'stock_quantity' => 75,
                'reorder_point' => 15,
                'track_stock' => true,
                'active' => true,
                'featured' => false,
                'track_inventory' => true,
            ],

            // Clothing
            [
                'store_id' => $mainStore->id,
                'category_id' => $mensClothing->id,
                'brand_id' => $nike->id,
                'tax_rate_id' => $reducedTax->id,
                'name' => 'Nike Air Jordan T-Shirt',
                'slug' => 'nike-air-jordan-tshirt',
                'description' => 'Comfortable cotton t-shirt with Air Jordan logo',
                'sku' => 'NIKE-AJ-TSHIRT',
                'barcode' => '8234567890130',
                'unit' => 'piece',
                'price' => 39.99,
                'cost' => 20.00,
                'stock_quantity' => 150,
                'reorder_point' => 30,
                'track_stock' => true,
                'active' => true,
                'featured' => false,
                'track_inventory' => true,
            ],

            // Furniture
            [
                'store_id' => $mainStore->id,
                'category_id' => $furniture->id,
                'brand_id' => $ikea->id,
                'tax_rate_id' => $standardTax->id,
                'name' => 'IKEA Office Chair',
                'slug' => 'ikea-office-chair',
                'description' => 'Ergonomic office chair with lumbar support',
                'sku' => 'IKEA-OFFICECHAIR',
                'barcode' => '9234567890131',
                'unit' => 'piece',
                'price' => 299.00,
                'cost' => 180.00,
                'stock_quantity' => 45,
                'reorder_point' => 10,
                'track_stock' => true,
                'active' => true,
                'featured' => false,
                'track_inventory' => true,
            ],
            [
                'store_id' => $mainStore->id,
                'category_id' => $furniture->id,
                'brand_id' => $ikea->id,
                'tax_rate_id' => $standardTax->id,
                'name' => 'IKEA Standing Desk',
                'slug' => 'ikea-standing-desk',
                'description' => 'Adjustable height desk for healthier working',
                'sku' => 'IKEA-STANDDESK',
                'barcode' => '9334567890132',
                'unit' => 'piece',
                'price' => 599.00,
                'cost' => 380.00,
                'stock_quantity' => 20,
                'reorder_point' => 5,
                'track_stock' => true,
                'active' => true,
                'featured' => true,
                'track_inventory' => true,
            ],
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }

        // Create some products for other stores using factory
        Product::factory()->count(20)->create(['store_id' => $mainStore->id]);
        Product::factory()->count(15)->create(['store_id' => $northStore->id]);
        Product::factory()->count(15)->create(['store_id' => $southStore->id]);

        $this->command->info('Products created successfully!');
    }
}
