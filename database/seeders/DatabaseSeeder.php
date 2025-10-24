<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed in correct order due to foreign key constraints

        // 1. Independent tables (no foreign keys)
        $this->call([
            StoreSeeder::class,
            PaymentMethodSeeder::class,
            TaxRateSeeder::class,
            CategorySeeder::class,
            BrandSeeder::class,
            SupplierSeeder::class,
            CustomerGroupSeeder::class,
        ]);

        // 2. Roles and permissions (uses Spatie package)
        $this->call([
            RoleAndPermissionSeeder::class,
        ]);

        // 3. Users (depends on stores and roles)
        $this->call([
            UserSeeder::class,
        ]);

        // 4. Products (depends on categories, brands, tax rates, stores)
        $this->call([
            ProductSeeder::class,
        ]);

        // 5. Customers (depends on stores and customer groups)
        $this->call([
            CustomerSeeder::class,
        ]);

        // Note: Other seeders (sales, stock movements, etc.) can be added later
        // for more comprehensive demo data
    }
}
