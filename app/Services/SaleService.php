<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalePayment;
use App\Models\ProductVariant;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class SaleService
{
    public function createSale(array $data): Sale
    {
        return DB::transaction(function () use ($data) {
            $sale = Sale::create([
                'store_id' => $data['store_id'],
                'user_id' => $data['user_id'],
                'customer_id' => $data['customer_id'] ?? null,
                'reference' => $this->generateReference(),
                'subtotal' => 0,
                'tax' => 0,
                'discount' => 0,
                'total' => 0,
                'status' => Sale::STATUS_PENDING,
                'notes' => $data['notes'] ?? null,
            ]);

            $subtotal = 0;
            $totalTax = 0;

            foreach ($data['items'] as $item) {
                $variant = ProductVariant::findOrFail($item['product_variant_id']);
                
                $quantity = $item['quantity'];
                $price = $variant->price;
                $discount = $item['discount'] ?? 0;
                $itemSubtotal = ($price * $quantity) - $discount;
                
                $taxRate = $variant->product->taxRate->rate ?? 0;
                $tax = ($itemSubtotal * $taxRate) / 100;
                
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_variant_id' => $variant->id,
                    'quantity' => $quantity,
                    'unit_price' => $price,
                    'discount' => $discount,
                    'tax' => $tax,
                    'total' => $itemSubtotal + $tax,
                ]);

                $subtotal += $itemSubtotal;
                $totalTax += $tax;
            }

            $totalDiscount = $data['discount'] ?? 0;
            $total = $subtotal + $totalTax - $totalDiscount;

            $sale->update([
                'subtotal' => $subtotal,
                'tax' => $totalTax,
                'discount' => $totalDiscount,
                'total' => $total,
            ]);

            return $sale->fresh();
        });
    }

    public function completeSale(Sale $sale, array $payments): Sale
    {
        return DB::transaction(function () use ($sale, $payments) {
            foreach ($payments as $payment) {
                SalePayment::create([
                    'sale_id' => $sale->id,
                    'payment_method_id' => $payment['payment_method_id'],
                    'amount' => $payment['amount'],
                    'reference' => $payment['reference'] ?? null,
                ]);
            }

            foreach ($sale->items as $item) {
                $item->variant->decrement('stock', $item->quantity);
                
                app(InventoryService::class)->createStockMovement([
                    'product_variant_id' => $item->product_variant_id,
                    'store_id' => $sale->store_id,
                    'type' => 'out',
                    'quantity' => $item->quantity,
                    'reference' => $sale->reference,
                    'relatable_type' => Sale::class,
                    'relatable_id' => $sale->id,
                ]);
            }

            if ($sale->customer_id && config('pos.enable_loyalty_points', true)) {
                $points = floor($sale->total * config('pos.loyalty_points_rate', 0.1));
                $sale->customer->addLoyaltyPoints($points);
            }

            $sale->update(['status' => Sale::STATUS_COMPLETED]);

            return $sale->fresh();
        });
    }

    protected function generateReference(): string
    {
        $prefix = config('pos.sale_reference_prefix', 'SALE');
        $number = str_pad(Sale::count() + 1, 6, '0', STR_PAD_LEFT);
        return "{$prefix}-{$number}-" . date('Ymd');
    }
}