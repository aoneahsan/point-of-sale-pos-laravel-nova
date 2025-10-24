<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'Tech Wholesale Inc.',
                'code' => 'TWI-001',
                'email' => 'orders@techwholesale.com',
                'phone' => '+1-555-1001',
                'address' => '100 Tech Park Drive, Silicon Valley, CA 94025',
                'contact_person' => 'James Wilson',
                'tax_number' => 'TAX-TWI-001',
                'payment_terms' => 'Net 30',
                'active' => true,
            ],
            [
                'name' => 'Global Electronics Supply',
                'code' => 'GES-001',
                'email' => 'sales@globalelectronics.com',
                'phone' => '+1-555-1002',
                'address' => '200 Electronics Boulevard, Austin, TX 78701',
                'contact_person' => 'Sarah Chen',
                'tax_number' => 'TAX-GES-001',
                'payment_terms' => 'Net 45',
                'active' => true,
            ],
            [
                'name' => 'Fashion Distributors Ltd.',
                'code' => 'FDL-001',
                'email' => 'info@fashiondist.com',
                'phone' => '+1-555-1003',
                'address' => '300 Fashion Avenue, New York, NY 10001',
                'contact_person' => 'Michael Brown',
                'tax_number' => 'TAX-FDL-001',
                'payment_terms' => 'Net 30',
                'active' => true,
            ],
            [
                'name' => 'Home Goods Wholesalers',
                'code' => 'HGW-001',
                'email' => 'orders@homegoods.com',
                'phone' => '+1-555-1004',
                'address' => '400 Home Street, Chicago, IL 60601',
                'contact_person' => 'Emily Davis',
                'tax_number' => 'TAX-HGW-001',
                'payment_terms' => 'Net 60',
                'active' => true,
            ],
            [
                'name' => 'Sports Equipment Co.',
                'code' => 'SEC-001',
                'email' => 'sales@sportsequip.com',
                'phone' => '+1-555-1005',
                'address' => '500 Sports Way, Portland, OR 97201',
                'contact_person' => 'Robert Martinez',
                'tax_number' => 'TAX-SEC-001',
                'payment_terms' => 'Net 30',
                'active' => true,
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}
