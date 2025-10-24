<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $paymentMethods = [
            [
                'name' => 'Cash',
                'code' => 'CASH',
                'type' => 'cash',
                'active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Credit Card',
                'code' => 'CREDIT_CARD',
                'type' => 'card',
                'active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Debit Card',
                'code' => 'DEBIT_CARD',
                'type' => 'card',
                'active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Mobile Payment',
                'code' => 'MOBILE',
                'type' => 'digital',
                'active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Store Credit',
                'code' => 'STORE_CREDIT',
                'type' => 'store_credit',
                'active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Gift Card',
                'code' => 'GIFT_CARD',
                'type' => 'gift_card',
                'active' => true,
                'sort_order' => 6,
            ],
            [
                'name' => 'Bank Transfer',
                'code' => 'BANK_TRANSFER',
                'type' => 'bank_transfer',
                'active' => true,
                'sort_order' => 7,
            ],
            [
                'name' => 'Check',
                'code' => 'CHECK',
                'type' => 'check',
                'active' => false,
                'sort_order' => 8,
            ],
        ];

        foreach ($paymentMethods as $method) {
            PaymentMethod::create($method);
        }
    }
}
