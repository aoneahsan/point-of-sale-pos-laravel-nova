<?php

namespace App\Services;

use App\Models\Discount;
use App\Models\Coupon;
use Carbon\Carbon;

class DiscountService
{
    public function applyDiscount(float $amount, ?int $discountId = null): float
    {
        if (!$discountId) {
            return 0;
        }

        $discount = Discount::find($discountId);

        if (!$discount || !$discount->active) {
            return 0;
        }

        if (!$this->isDiscountValid($discount)) {
            return 0;
        }

        if ($discount->type === 'percentage') {
            return round(($amount * $discount->value) / 100, 2);
        }

        return min($discount->value, $amount);
    }

    public function applyCoupon(string $code, float $amount): float
    {
        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon || !$coupon->active) {
            throw new \Exception('Invalid coupon code');
        }

        if (!$this->isCouponValid($coupon)) {
            throw new \Exception('Coupon is not valid');
        }

        if ($amount < $coupon->minimum_purchase) {
            throw new \Exception('Minimum purchase amount not met');
        }

        $coupon->increment('used_count');

        if ($coupon->type === 'percentage') {
            $discount = round(($amount * $coupon->value) / 100, 2);
            return min($discount, $coupon->maximum_discount ?? PHP_FLOAT_MAX);
        }

        return min($coupon->value, $amount);
    }

    protected function isDiscountValid(Discount $discount): bool
    {
        $now = Carbon::now();

        if ($discount->start_date && $now->lt($discount->start_date)) {
            return false;
        }

        if ($discount->end_date && $now->gt($discount->end_date)) {
            return false;
        }

        return true;
    }

    protected function isCouponValid(Coupon $coupon): bool
    {
        $now = Carbon::now();

        if ($coupon->start_date && $now->lt($coupon->start_date)) {
            return false;
        }

        if ($coupon->end_date && $now->gt($coupon->end_date)) {
            return false;
        }

        if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
            return false;
        }

        return true;
    }
}