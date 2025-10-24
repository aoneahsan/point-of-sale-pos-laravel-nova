<?php

namespace Database\Seeders;

use App\Models\TaxRate;
use Illuminate\Database\Seeder;

class TaxRateSeeder extends Seeder
{
    public function run(): void
    {
        $taxRates = [
            [
                'name' => 'Standard Tax',
                'code' => 'STANDARD',
                'rate' => 10.00,
                'active' => true,
                'is_default' => true,
                'description' => 'Standard tax rate applied to most products',
            ],
            [
                'name' => 'Reduced Tax',
                'code' => 'REDUCED',
                'rate' => 5.00,
                'active' => true,
                'is_default' => false,
                'description' => 'Reduced tax rate for essential goods',
            ],
            [
                'name' => 'Zero Tax',
                'code' => 'ZERO',
                'rate' => 0.00,
                'active' => true,
                'is_default' => false,
                'description' => 'Zero tax rate for exempt products',
            ],
            [
                'name' => 'Luxury Tax',
                'code' => 'LUXURY',
                'rate' => 20.00,
                'active' => true,
                'is_default' => false,
                'description' => 'Higher tax rate for luxury items',
            ],
        ];

        foreach ($taxRates as $taxRate) {
            TaxRate::create($taxRate);
        }
    }
}
