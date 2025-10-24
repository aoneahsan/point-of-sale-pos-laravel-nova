<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Models\Store;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $mainStore = Store::where('code', 'MAIN-001')->first();
        $northStore = Store::where('code', 'NORTH-001')->first();

        $retail = CustomerGroup::where('code', 'RETAIL')->first();
        $vip = CustomerGroup::where('code', 'VIP')->first();
        $wholesale = CustomerGroup::where('code', 'WHOLESALE')->first();
        $corporate = CustomerGroup::where('code', 'CORPORATE')->first();

        $customers = [
            [
                'store_id' => $mainStore->id,
                'customer_group_id' => $retail->id,
                'code' => 'CUST-001',
                'name' => 'John Smith',
                'email' => 'john.smith@example.com',
                'phone' => '+1-555-2001',
                'address' => '123 Customer St, City, State 12345',
                'tax_number' => null,
                'loyalty_points' => 250,
                'store_credit' => 0.00,
                'notes' => 'Regular customer, prefers email receipts',
                'active' => true,
            ],
            [
                'store_id' => $mainStore->id,
                'customer_group_id' => $vip->id,
                'code' => 'CUST-002',
                'name' => 'Jane Doe',
                'email' => 'jane.doe@example.com',
                'phone' => '+1-555-2002',
                'address' => '456 VIP Avenue, City, State 12346',
                'tax_number' => null,
                'loyalty_points' => 1500,
                'store_credit' => 50.00,
                'notes' => 'VIP customer, frequent buyer',
                'active' => true,
            ],
            [
                'store_id' => $mainStore->id,
                'customer_group_id' => $wholesale->id,
                'code' => 'CUST-003',
                'name' => 'ABC Retail Store',
                'email' => 'orders@abcretail.com',
                'phone' => '+1-555-2003',
                'address' => '789 Business Blvd, City, State 12347',
                'tax_number' => 'TAX-ABC-001',
                'loyalty_points' => 0,
                'store_credit' => 0.00,
                'notes' => 'Wholesale customer, monthly invoicing',
                'active' => true,
            ],
            [
                'store_id' => $mainStore->id,
                'customer_group_id' => $corporate->id,
                'code' => 'CUST-004',
                'name' => 'Tech Corp Inc',
                'email' => 'procurement@techcorp.com',
                'phone' => '+1-555-2004',
                'address' => '321 Corporate Way, City, State 12348',
                'tax_number' => 'TAX-TECH-001',
                'loyalty_points' => 500,
                'store_credit' => 100.00,
                'notes' => 'Corporate account, requires PO numbers',
                'active' => true,
            ],
            [
                'store_id' => $mainStore->id,
                'customer_group_id' => $retail->id,
                'code' => 'CUST-005',
                'name' => 'Michael Johnson',
                'email' => 'michael.j@example.com',
                'phone' => '+1-555-2005',
                'address' => '555 Main Road, City, State 12349',
                'tax_number' => null,
                'loyalty_points' => 100,
                'store_credit' => 25.00,
                'notes' => 'Prefers cash payments',
                'active' => true,
            ],
            [
                'store_id' => $northStore->id,
                'customer_group_id' => $retail->id,
                'code' => 'CUST-N001',
                'name' => 'Sarah Williams',
                'email' => 'sarah.w@example.com',
                'phone' => '+1-555-3001',
                'address' => '111 North St, City, State 12350',
                'tax_number' => null,
                'loyalty_points' => 75,
                'store_credit' => 0.00,
                'notes' => 'North branch regular',
                'active' => true,
            ],
            [
                'store_id' => $northStore->id,
                'customer_group_id' => $vip->id,
                'code' => 'CUST-N002',
                'name' => 'Robert Brown',
                'email' => 'robert.b@example.com',
                'phone' => '+1-555-3002',
                'address' => '222 North Ave, City, State 12351',
                'tax_number' => null,
                'loyalty_points' => 2000,
                'store_credit' => 150.00,
                'notes' => 'VIP member, high-value customer',
                'active' => true,
            ],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }
}
