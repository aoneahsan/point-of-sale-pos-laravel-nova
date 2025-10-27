<?php

namespace Database\Factories;

use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PaymentMethod>
 */
class PaymentMethodFactory extends Factory
{
    protected $model = PaymentMethod::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $methods = [
            ['name' => 'Cash', 'code' => 'CASH', 'type' => 'cash'],
            ['name' => 'Credit Card', 'code' => 'CARD', 'type' => 'card'],
            ['name' => 'Debit Card', 'code' => 'DEBIT', 'type' => 'card'],
            ['name' => 'Mobile Payment', 'code' => 'MOBILE', 'type' => 'digital_wallet'],
            ['name' => 'Bank Transfer', 'code' => 'BANK', 'type' => 'bank_transfer'],
        ];

        $method = fake()->randomElement($methods);

        return [
            'name' => $method['name'],
            'code' => $method['code'] . '-' . fake()->unique()->numberBetween(1000, 9999),
            'type' => $method['type'],
            'active' => true,
            'sort_order' => fake()->numberBetween(1, 100),
        ];
    }

    /**
     * Indicate that the payment method is cash.
     */
    public function cash(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Cash',
            'code' => 'CASH',
        ]);
    }

    /**
     * Indicate that the payment method is a card.
     */
    public function card(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Credit Card',
            'code' => 'CARD',
        ]);
    }

    /**
     * Indicate that the payment method is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'active' => false,
        ]);
    }
}
