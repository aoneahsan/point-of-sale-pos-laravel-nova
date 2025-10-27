<?php

namespace Database\Factories;

use App\Models\TaxRate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TaxRate>
 */
class TaxRateFactory extends Factory
{
    protected $model = TaxRate::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $rates = [
            ['name' => 'Standard VAT', 'rate' => 20.00, 'code' => 'STANDARD'],
            ['name' => 'Reduced VAT', 'rate' => 5.00, 'code' => 'REDUCED'],
            ['name' => 'Zero VAT', 'rate' => 0.00, 'code' => 'ZERO'],
            ['name' => 'Sales Tax', 'rate' => 8.50, 'code' => 'SALES'],
        ];

        $tax = fake()->randomElement($rates);

        return [
            'name' => $tax['name'],
            'rate' => $tax['rate'],
            'code' => $tax['code'] . '-' . fake()->unique()->numberBetween(100, 999),
            'description' => fake()->sentence(),
            'active' => true,
            'is_default' => false,
        ];
    }

    /**
     * Indicate that this is the default tax rate.
     */
    public function default(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_default' => true,
        ]);
    }

    /**
     * Indicate that the tax rate is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'active' => false,
        ]);
    }
}
