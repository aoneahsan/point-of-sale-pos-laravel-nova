<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalePayment;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Customer;
use App\Exceptions\Inventory\InsufficientStockException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SaleService
{
    public function __construct(
        protected InventoryService $inventoryService,
        protected TaxService $taxService
    ) {}

    /**
     * Create a new sale with items and payments
     */
    public function createSale(array $data): Sale
    {
        return DB::transaction(function () use ($data) {
            // Validate stock availability first
            $this->validateStockAvailability($data['items'], $data['store_id']);

            // Create the sale
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

            // Add items to the sale
            foreach ($data['items'] as $itemData) {
                $product = Product::findOrFail($itemData['product_id']);

                $quantity = $itemData['quantity'];
                $price = $itemData['price'] ?? $product->price;
                $cost = $itemData['cost'] ?? $product->cost;
                $discount = $itemData['discount'] ?? 0;

                $itemSubtotal = ($price * $quantity) - $discount;

                // Calculate tax
                $taxRate = $product->taxRate->rate ?? 0;
                $tax = $this->taxService->calculateTax($itemSubtotal, $taxRate);

                $saleItem = SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $price,
                    'unit_cost' => $cost,
                    'discount' => $discount,
                    'tax' => $tax,
                    'total' => $itemSubtotal + $tax,
                ]);

                $subtotal += $itemSubtotal;
                $totalTax += $tax;
            }

            // Apply global discount if any
            $totalDiscount = $data['discount'] ?? 0;
            $total = $subtotal + $totalTax - $totalDiscount;

            $sale->update([
                'subtotal' => $subtotal,
                'tax' => $totalTax,
                'discount' => $totalDiscount,
                'total' => $total,
            ]);

            // Process payments if provided
            if (isset($data['payments']) && !empty($data['payments'])) {
                $this->completeSale($sale, $data['payments']);
            }

            return $sale->fresh(['items', 'payments']);
        });
    }

    /**
     * Complete a sale by processing payments and adjusting inventory
     */
    public function completeSale(Sale $sale, array $payments): Sale
    {
        return DB::transaction(function () use ($sale, $payments) {
            // Process payments
            foreach ($payments as $payment) {
                SalePayment::create([
                    'sale_id' => $sale->id,
                    'payment_method_id' => $payment['payment_method_id'],
                    'amount' => $payment['amount'],
                    'reference' => $payment['reference'] ?? null,
                ]);
            }

            // Deduct inventory
            foreach ($sale->items as $item) {
                if ($item->product && $item->product->track_stock) {
                    $this->inventoryService->deductStock(
                        product: $item->product,
                        quantity: $item->quantity,
                        reason: "Sale {$sale->reference}",
                        reference: 'sale',
                        referenceId: $sale->id
                    );
                }
            }

            // Award loyalty points if customer exists
            if ($sale->customer_id) {
                $this->awardLoyaltyPoints($sale);
            }

            // Update sale status
            $sale->update(['status' => Sale::STATUS_COMPLETED]);

            Log::info("Sale completed", [
                'sale_id' => $sale->id,
                'reference' => $sale->reference,
                'total' => $sale->total,
                'store_id' => $sale->store_id,
            ]);

            return $sale->fresh(['items', 'payments', 'customer']);
        });
    }

    /**
     * Process a refund for a sale
     */
    public function refundSale(Sale $sale, array $items = [], ?string $reason = null): Sale
    {
        return DB::transaction(function () use ($sale, $items, $reason) {
            // Create refund record (implementation depends on returns table structure)
            // For now, we'll just restore inventory and mark sale as refunded

            $refundAmount = 0;

            foreach ($items as $itemData) {
                $saleItem = $sale->items()->findOrFail($itemData['sale_item_id']);
                $refundQuantity = $itemData['quantity'];

                if ($refundQuantity > $saleItem->quantity) {
                    throw new \InvalidArgumentException("Refund quantity cannot exceed sold quantity");
                }

                // Restore inventory
                if ($saleItem->product->track_stock) {
                    $this->inventoryService->addStock(
                        $saleItem->product_id,
                        $refundQuantity,
                        "Refund for Sale {$sale->reference}",
                        $sale->store_id
                    );
                }

                $refundAmount += ($saleItem->unit_price * $refundQuantity);
            }

            // Update sale status
            $sale->update([
                'status' => Sale::STATUS_REFUNDED,
                'notes' => ($sale->notes ?? '') . "\nRefund Reason: " . ($reason ?? 'N/A'),
            ]);

            return $sale->fresh();
        });
    }

    /**
     * Validate stock availability for sale items
     */
    protected function validateStockAvailability(array $items, int $storeId): void
    {
        foreach ($items as $itemData) {
            $product = Product::findOrFail($itemData['product_id']);

            if (!$product->track_stock) {
                continue;
            }

            if ($product->stock_quantity < $itemData['quantity']) {
                throw new InsufficientStockException(
                    "Insufficient stock for product: {$product->name}. Available: {$product->stock_quantity}, Requested: {$itemData['quantity']}"
                );
            }
        }
    }

    /**
     * Award loyalty points to customer
     */
    protected function awardLoyaltyPoints(Sale $sale): void
    {
        if (!$sale->customer) {
            return;
        }

        $pointsRate = config('pos.loyalty_points_rate', 0.01); // 1 point per $100 by default
        $points = (int) floor($sale->total * $pointsRate);

        if ($points > 0) {
            $sale->customer->increment('loyalty_points', $points);
            Log::info("Loyalty points awarded", [
                'customer_id' => $sale->customer_id,
                'points' => $points,
                'sale_id' => $sale->id,
            ]);
        }
    }

    /**
     * Generate a unique sale reference number
     */
    protected function generateReference(): string
    {
        $prefix = config('pos.sale_reference_prefix', 'SALE');
        $date = date('Ymd');
        $count = Sale::whereDate('created_at', today())->count() + 1;
        $number = str_pad($count, 6, '0', STR_PAD_LEFT);

        return "{$prefix}-{$date}-{$number}";
    }
}