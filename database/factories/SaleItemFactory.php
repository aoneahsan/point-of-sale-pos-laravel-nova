<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SaleItem>
 */
class SaleItemFactory extends Factory
{
    protected $model = SaleItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 10);
        $unitPrice = fake()->randomFloat(2, 10, 500);
        $unitCost = $unitPrice * 0.6; // Assume 40% margin
        $discount = fake()->optional(0.2)->randomFloat(2, 0, $unitPrice * $quantity * 0.1); // 20% chance of discount
        $subtotal = ($unitPrice * $quantity) - ($discount ?? 0);
        $tax = $subtotal * 0.08; // 8% tax
        $total = $subtotal + $tax;

        return [
            'sale_id' => Sale::factory(),
            'product_id' => Product::factory(),
            'product_variant_id' => null,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'unit_cost' => $unitCost,
            'discount' => $discount ?? 0,
            'tax' => $tax,
            'total' => $total,
        ];
    }

    /**
     * Indicate that the sale item has no discount.
     */
    public function noDiscount(): static
    {
        return $this->state(function (array $attributes) {
            $subtotal = $attributes['unit_price'] * $attributes['quantity'];
            $tax = $subtotal * 0.08;

            return [
                'discount' => 0,
                'total' => $subtotal + $tax,
            ];
        });
    }

    /**
     * Indicate that the sale item has no tax.
     */
    public function noTax(): static
    {
        return $this->state(function (array $attributes) {
            $subtotal = ($attributes['unit_price'] * $attributes['quantity']) - $attributes['discount'];

            return [
                'tax' => 0,
                'total' => $subtotal,
            ];
        });
    }

    /**
     * Set specific quantity and price.
     */
    public function withPrice(float $unitPrice, int $quantity = 1): static
    {
        return $this->state(function (array $attributes) use ($unitPrice, $quantity) {
            $unitCost = $unitPrice * 0.6;
            $subtotal = $unitPrice * $quantity;
            $tax = $subtotal * 0.08;
            $total = $subtotal + $tax;

            return [
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'unit_cost' => $unitCost,
                'discount' => 0,
                'tax' => $tax,
                'total' => $total,
            ];
        });
    }
}
