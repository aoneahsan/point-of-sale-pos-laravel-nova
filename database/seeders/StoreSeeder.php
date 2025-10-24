<?php

namespace Database\Seeders;

use App\Models\Store;
use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
{
    public function run(): void
    {
        $stores = [
            [
                'name' => 'Main Store',
                'code' => 'MAIN-001',
                'address' => '123 Main Street, Downtown, City, 12345',
                'phone' => '+1-555-0100',
                'email' => 'main@posstore.com',
                'tax_number' => 'TAX-MAIN-001',
                'active' => true,
                'settings' => [
                    'currency' => 'USD',
                    'timezone' => 'America/New_York',
                    'receipt_header' => 'Main Store',
                    'receipt_footer' => 'Thank you for your business!',
                    'low_stock_alert' => true,
                    'allow_negative_stock' => false,
                ],
            ],
            [
                'name' => 'North Branch',
                'code' => 'NORTH-001',
                'address' => '456 North Avenue, Uptown, City, 12346',
                'phone' => '+1-555-0101',
                'email' => 'north@posstore.com',
                'tax_number' => 'TAX-NORTH-001',
                'active' => true,
                'settings' => [
                    'currency' => 'USD',
                    'timezone' => 'America/New_York',
                    'receipt_header' => 'North Branch',
                    'receipt_footer' => 'Thank you for your business!',
                    'low_stock_alert' => true,
                    'allow_negative_stock' => false,
                ],
            ],
            [
                'name' => 'South Branch',
                'code' => 'SOUTH-001',
                'address' => '789 South Street, Southside, City, 12347',
                'phone' => '+1-555-0102',
                'email' => 'south@posstore.com',
                'tax_number' => 'TAX-SOUTH-001',
                'active' => true,
                'settings' => [
                    'currency' => 'USD',
                    'timezone' => 'America/New_York',
                    'receipt_header' => 'South Branch',
                    'receipt_footer' => 'Thank you for your business!',
                    'low_stock_alert' => true,
                    'allow_negative_stock' => false,
                ],
            ],
        ];

        foreach ($stores as $store) {
            Store::create($store);
        }
    }
}
