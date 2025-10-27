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
        if (!$coupon->active) {
            throw new InvalidCouponException("Coupon is not active");
        }

        // Check if expired
        if ($coupon->expires_at && now()->isAfter($coupon->expires_at)) {
            throw new InvalidCouponException("Coupon has expired");
        }

        // Check usage limit
        if ($coupon->max_uses && $coupon->uses >= $coupon->max_uses) {
            throw new InvalidCouponException("Coupon usage limit exceeded");
        }

        // Validate the related discount
        if (!$coupon->discount || !$coupon->discount->active) {
            throw new InvalidCouponException("Coupon discount is not active");
        }

        $now = Carbon::now();
        $discount = $coupon->discount;

        // Check discount start date
        if ($discount->start_date && $now->isBefore($discount->start_date)) {
            throw new InvalidCouponException("Discount has not started yet");
        }

        // Check discount end date
        if ($discount->end_date && $now->isAfter($discount->end_date)) {
            throw new InvalidCouponException("Discount has expired");
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
        // Validate coupon (this also validates the related discount)
        $this->validateCoupon($coupon);

        $discount = $coupon->discount;

        // Check minimum purchase amount
        if ($discount->min_amount && $amount < $discount->min_amount) {
            throw new InvalidCouponException(
                "Minimum purchase amount of {$discount->min_amount} not met"
            );
        }

        // Calculate discount based on type
        if ($discount->type === 'percentage') {
            $discountAmount = $this->calculatePercentageDiscount($amount, $discount->value);
            return round($discountAmount, 2);
        }

        // Fixed amount discount
        $discountAmount = min($discount->value, $amount);
        return round($discountAmount, 2);
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
        $coupon = Coupon::with('discount')->where('code', $code)->first();

        if (!$coupon) {
            throw new InvalidCouponException('Invalid coupon code');
        }

        $discountAmount = $this->applyCoupon($coupon, $amount);

        // Increment usage count
        $coupon->increment('uses');

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
