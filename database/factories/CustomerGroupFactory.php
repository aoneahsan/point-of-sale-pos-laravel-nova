<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerGroupFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'code' => fake()->unique()->regexify('[A-Z]{3,5}'),
            'discount_percentage' => fake()->randomFloat(2, 0, 20),
            'loyalty_points_multiplier' => fake()->randomFloat(1, 0.5, 2.0),
            'description' => fake()->sentence(),
            'active' => true,
        ];
    }
}