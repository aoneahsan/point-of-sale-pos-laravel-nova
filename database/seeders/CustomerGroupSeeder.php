<?php

namespace Database\Seeders;

use App\Models\CustomerGroup;
use Illuminate\Database\Seeder;

class CustomerGroupSeeder extends Seeder
{
    public function run(): void
    {
        $customerGroups = [
            [
                'name' => 'Retail',
                'code' => 'RETAIL',
                'discount_percentage' => 0.00,
                'loyalty_points_multiplier' => 1.0,
                'description' => 'Regular retail customers',
                'active' => true,
            ],
            [
                'name' => 'VIP',
                'code' => 'VIP',
                'discount_percentage' => 10.00,
                'loyalty_points_multiplier' => 2.0,
                'description' => 'VIP customers with special benefits',
                'active' => true,
            ],
            [
                'name' => 'Wholesale',
                'code' => 'WHOLESALE',
                'discount_percentage' => 15.00,
                'loyalty_points_multiplier' => 0.5,
                'description' => 'Wholesale customers buying in bulk',
                'active' => true,
            ],
            [
                'name' => 'Corporate',
                'code' => 'CORPORATE',
                'discount_percentage' => 12.00,
                'loyalty_points_multiplier' => 1.5,
                'description' => 'Corporate and business customers',
                'active' => true,
            ],
            [
                'name' => 'Student',
                'code' => 'STUDENT',
                'discount_percentage' => 5.00,
                'loyalty_points_multiplier' => 1.0,
                'description' => 'Students with valid ID',
                'active' => true,
            ],
            [
                'name' => 'Senior',
                'code' => 'SENIOR',
                'discount_percentage' => 8.00,
                'loyalty_points_multiplier' => 1.0,
                'description' => 'Senior citizens',
                'active' => true,
            ],
        ];

        foreach ($customerGroups as $group) {
            CustomerGroup::create($group);
        }
    }
}
