<?php

namespace App\Services;

use App\Models\Discount;
use App\Models\Coupon;
use App\Exceptions\Discount\InvalidCouponException;
use Carbon\Carbon;

class DiscountService
{
    /**
     * Calculate percentage discount on an amount
     *
     * @param float $amount The original amount
     * @param float $discountPercent The discount percentage (e.g., 10 for 10%)
     * @return float The discount amount
     */
    public function calculatePercentageDiscount(float $amount, float $discountPercent): float
    {
        if ($amount < 0 || $discountPercent < 0) {
            return 0.00;
        }

        if ($discountPercent > 100) {
            $discountPercent = 100;
        }

        $discount = ($amount * $discountPercent) / 100;
        return round($discount, 2);
    }

    /**
     * Apply fixed discount to an amount (returns final amount)
     *
     * @param float $amount The original amount
     * @param float $discount The fixed discount amount
     * @return float The final amount after discount
     */
    public function applyFixedDiscount(float $amount, float $discount): float
    {
        $finalAmount = $amount - $discount;

        // Don't allow negative amounts
        if ($finalAmount < 0) {
            return 0.00;
        }

        return round($finalAmount, 2);
    }

    /**
     * Validate a coupon
     *
     * @param Coupon $coupon
     * @return bool
     * @throws InvalidCouponException
     */
    public function validateCoupon(Coupon $coupon): bool
    {
        // Check if active
        if (!$coupon->is_active) {
            throw new InvalidCouponException("Coupon is not active");
        }

        $now = Carbon::now();

        // Check if started
        if ($coupon->start_date && $now->isBefore($coupon->start_date)) {
            throw new InvalidCouponException("Coupon has not started yet");
        }

        // Check if expired
        if ($coupon->end_date && $now->isAfter($coupon->end_date)) {
            throw new InvalidCouponException("Coupon has expired");
        }

        // Check usage limit
        if ($coupon->max_uses && $coupon->times_used >= $coupon->max_uses) {
            throw new InvalidCouponException("Coupon usage limit exceeded");
        }

        return true;
    }

    /**
     * Apply a coupon and return the discount amount
     *
     * @param Coupon $coupon
     * @param float $amount
     * @return float The discount amount
     * @throws InvalidCouponException
     */
    public function applyCoupon(Coupon $coupon, float $amount): float
    {
        // Validate coupon
        $this->validateCoupon($coupon);

        // Check minimum purchase amount
        if ($coupon->min_purchase_amount && $amount < $coupon->min_purchase_amount) {
            throw new InvalidCouponException(
                "Minimum purchase amount of {$coupon->min_purchase_amount} not met"
            );
        }

        // Calculate discount based on type
        if ($coupon->discount_type === 'percentage') {
            $discount = $this->calculatePercentageDiscount($amount, $coupon->discount_value);

            // Apply max discount cap if exists
            if ($coupon->max_discount_amount && $discount > $coupon->max_discount_amount) {
                $discount = $coupon->max_discount_amount;
            }

            return round($discount, 2);
        }

        // Fixed amount discount
        $discount = min($coupon->discount_value, $amount);
        return round($discount, 2);
    }

    /**
     * Apply multiple fixed discounts to an amount
     *
     * @param float $amount
     * @param array $discounts Array of discount amounts
     * @return float Final amount after all discounts
     */
    public function applyMultipleDiscounts(float $amount, array $discounts): float
    {
        $finalAmount = $amount;

        foreach ($discounts as $discount) {
            $finalAmount = $this->applyFixedDiscount($finalAmount, $discount);
        }

        return round($finalAmount, 2);
    }

    /**
     * Check if discounts can be stacked together
     *
     * @param array $discounts Array of Discount models
     * @return bool
     * @throws InvalidCouponException
     */
    public function checkDiscountStackability(array $discounts): bool
    {
        foreach ($discounts as $discount) {
            if (isset($discount->can_stack) && !$discount->can_stack) {
                throw new InvalidCouponException("Discount '{$discount->name}' cannot be combined with other discounts");
            }
        }

        return true;
    }

    /**
     * Apply discount from Discount model
     *
     * @param float $amount
     * @param int|null $discountId
     * @return float The discount amount
     */
    public function applyDiscountModel(float $amount, ?int $discountId = null): float
    {
        if (!$discountId) {
            return 0.00;
        }

        $discount = Discount::find($discountId);

        if (!$discount || !$discount->active) {
            return 0.00;
        }

        if (!$this->isDiscountModelValid($discount)) {
            return 0.00;
        }

        if ($discount->type === 'percentage') {
            return $this->calculatePercentageDiscount($amount, $discount->value);
        }

        // Fixed amount
        return min($discount->value, $amount);
    }

    /**
     * Apply coupon by code
     *
     * @param string $code
     * @param float $amount
     * @return float The discount amount
     * @throws InvalidCouponException
     */
    public function applyCouponByCode(string $code, float $amount): float
    {
        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            throw new InvalidCouponException('Invalid coupon code');
        }

        $discountAmount = $this->applyCoupon($coupon, $amount);

        // Increment usage count
        $coupon->increment('times_used');

        return $discountAmount;
    }

    /**
     * Check if discount model is valid (date checks)
     *
     * @param Discount $discount
     * @return bool
     */
    protected function isDiscountModelValid(Discount $discount): bool
    {
        $now = Carbon::now();

        if ($discount->start_date && $now->isBefore($discount->start_date)) {
            return false;
        }

        if ($discount->end_date && $now->isAfter($discount->end_date)) {
            return false;
        }

        return true;
    }

    /**
     * Get all active coupons
     *
     * @return \Illuminate\Support\Collection
     */
    public function getActiveCoupons()
    {
        return Coupon::where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', Carbon::now());
            })
            ->get();
    }

    /**
     * Get all active discounts
     *
     * @return \Illuminate\Support\Collection
     */
    public function getActiveDiscounts()
    {
        return Discount::where('active', true)
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', Carbon::now());
            })
            ->get();
    }
}
