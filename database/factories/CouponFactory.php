<?php

namespace Database\Factories;

use App\Models\Coupon;
use App\Models\Discount;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Coupon>
 */
class CouponFactory extends Factory
{
    protected $model = Coupon::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'discount_id' => Discount::factory(),
            'code' => strtoupper(fake()->unique()->bothify('???###')),
            'max_uses' => fake()->numberBetween(10, 100),
            'uses' => 0,
            'max_uses_per_customer' => fake()->numberBetween(1, 5),
            'expires_at' => now()->addDays(fake()->numberBetween(7, 90)),
            'active' => true,
        ];
    }

    /**
     * Indicate that the coupon is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'active' => false,
        ]);
    }

    /**
     * Indicate that the coupon is expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => now()->subDay(),
        ]);
    }

    /**
     * Indicate that the coupon has reached its usage limit.
     */
    public function maxedOut(): static
    {
        return $this->state(fn (array $attributes) => [
            'max_uses' => 5,
            'uses' => 5,
        ]);
    }
}
