<?php

namespace Database\Factories;

use App\Models\Discount;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Discount>
 */
class DiscountFactory extends Factory
{
    protected $model = Discount::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(['percentage', 'fixed']);
        $value = $type === 'percentage'
            ? fake()->randomFloat(2, 5, 50)  // 5% to 50%
            : fake()->randomFloat(2, 5, 100); // $5 to $100

        return [
            'name' => fake()->words(3, true) . ' Discount',
            'type' => $type,
            'value' => $value,
            'min_amount' => fake()->optional(0.3)->randomFloat(2, 10, 50),
            'max_uses' => fake()->optional(0.5)->numberBetween(50, 500),
            'uses' => 0,
            'start_date' => fake()->optional(0.3)->dateTimeBetween('-30 days', '+7 days'),
            'end_date' => fake()->optional(0.5)->dateTimeBetween('+7 days', '+90 days'),
            'conditions' => null,
            'active' => true,
        ];
    }

    /**
     * Percentage discount.
     */
    public function percentage(float $value = 10.0): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'percentage',
            'value' => $value,
        ]);
    }

    /**
     * Fixed amount discount.
     */
    public function fixed(float $value = 10.0): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'fixed',
            'value' => $value,
        ]);
    }

    /**
     * Inactive discount.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'active' => false,
        ]);
    }

    /**
     * Discount that hasn't started yet.
     */
    public function notStarted(): static
    {
        return $this->state(fn (array $attributes) => [
            'start_date' => now()->addDay(),
            'end_date' => now()->addDays(30),
        ]);
    }

    /**
     * Expired discount.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'start_date' => now()->subDays(30),
            'end_date' => now()->subDay(),
        ]);
    }
}
