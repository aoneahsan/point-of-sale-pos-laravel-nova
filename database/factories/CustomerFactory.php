<?php

namespace Database\Factories;

use App\Models\Store;
use App\Models\CustomerGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'store_id' => Store::factory(),
            'customer_group_id' => CustomerGroup::factory(),
            'code' => fake()->unique()->regexify('CUST-[0-9]{6}'),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'loyalty_points' => fake()->numberBetween(0, 1000),
            'store_credit' => fake()->randomFloat(2, 0, 200),
            'active' => true,
        ];
    }
}