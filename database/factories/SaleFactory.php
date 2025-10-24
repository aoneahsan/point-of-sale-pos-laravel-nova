<?php

namespace Database\Factories;

use App\Models\Store;
use App\Models\User;
use App\Models\Customer;
use App\Models\Sale;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleFactory extends Factory
{
    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 10, 1000);
        $tax = $subtotal * 0.1;
        $discount = fake()->randomFloat(2, 0, 50);
        
        return [
            'store_id' => Store::factory(),
            'user_id' => User::factory(),
            'customer_id' => fake()->boolean(70) ? Customer::factory() : null,
            'reference' => 'SALE-' . fake()->unique()->numberBetween(100000, 999999),
            'subtotal' => $subtotal,
            'tax' => $tax,
            'discount' => $discount,
            'total' => $subtotal + $tax - $discount,
            'status' => fake()->randomElement([
                Sale::STATUS_COMPLETED,
                Sale::STATUS_PENDING,
                Sale::STATUS_ON_HOLD,
            ]),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}