<?php

declare(strict_types=1);

use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Discount;
use App\Models\Store;
use App\Services\DiscountService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->service = app(DiscountService::class);
    $this->store = Store::factory()->create();
    $this->customer = Customer::factory()->create(['store_id' => $this->store->id]);
});

describe('Percentage Discount Calculation', function () {
    test('calculates percentage discount correctly', function () {
        $amount = 100.00;
        $discountPercent = 10.00;

        $discountAmount = $this->service->calculatePercentageDiscount($amount, $discountPercent);

        expect($discountAmount)->toBe(10.00);
    });

    test('calculates 50 percent discount', function () {
        $amount = 200.00;
        $discountPercent = 50.00;

        $discountAmount = $this->service->calculatePercentageDiscount($amount, $discountPercent);

        expect($discountAmount)->toBe(100.00);
    });

    test('returns zero for zero percent discount', function () {
        $amount = 100.00;
        $discountPercent = 0.00;

        $discountAmount = $this->service->calculatePercentageDiscount($amount, $discountPercent);

        expect($discountAmount)->toBe(0.00);
    });

    test('does not exceed original amount for 100 percent discount', function () {
        $amount = 100.00;
        $discountPercent = 100.00;

        $discountAmount = $this->service->calculatePercentageDiscount($amount, $discountPercent);

        expect($discountAmount)->toBe(100.00);
    });
});

describe('Fixed Amount Discount', function () {
    test('applies fixed discount correctly', function () {
        $amount = 100.00;
        $discount = 15.00;

        $finalAmount = $this->service->applyFixedDiscount($amount, $discount);

        expect($finalAmount)->toBe(85.00);
    });

    test('does not result in negative amount', function () {
        $amount = 50.00;
        $discount = 100.00; // Discount larger than amount

        $finalAmount = $this->service->applyFixedDiscount($amount, $discount);

        expect($finalAmount)->toBe(0.00); // Should not be negative
    });
});

describe('Coupon Validation', function () {
    test('validates active coupon', function () {
        $coupon = Coupon::factory()->create([
            'code' => 'SAVE10',
            'discount_type' => 'percentage',
            'discount_value' => 10.00,
            'is_active' => true,
            'start_date' => now()->subDay(),
            'end_date' => now()->addDay(),
        ]);

        $isValid = $this->service->validateCoupon($coupon);

        expect($isValid)->toBeTrue();
    });

    test('rejects inactive coupon', function () {
        $coupon = Coupon::factory()->create([
            'code' => 'INACTIVE',
            'is_active' => false,
        ]);

        expect(fn() => $this->service->validateCoupon($coupon))
            ->toThrow(\App\Exceptions\Discount\InvalidCouponException::class);
    });

    test('rejects expired coupon', function () {
        $coupon = Coupon::factory()->create([
            'code' => 'EXPIRED',
            'is_active' => true,
            'start_date' => now()->subDays(10),
            'end_date' => now()->subDay(), // Ended yesterday
        ]);

        expect(fn() => $this->service->validateCoupon($coupon))
            ->toThrow(\App\Exceptions\Discount\InvalidCouponException::class);
    });

    test('rejects coupon that hasnt started yet', function () {
        $coupon = Coupon::factory()->create([
            'code' => 'FUTURE',
            'is_active' => true,
            'start_date' => now()->addDay(), // Starts tomorrow
            'end_date' => now()->addDays(10),
        ]);

        expect(fn() => $this->service->validateCoupon($coupon))
            ->toThrow(\App\Exceptions\Discount\InvalidCouponException::class);
    });

    test('rejects coupon that exceeded usage limit', function () {
        $coupon = Coupon::factory()->create([
            'code' => 'LIMITED',
            'is_active' => true,
            'max_uses' => 5,
            'times_used' => 5, // Already used max times
        ]);

        expect(fn() => $this->service->validateCoupon($coupon))
            ->toThrow(\App\Exceptions\Discount\InvalidCouponException::class);
    });
});

describe('Coupon Application', function () {
    test('applies percentage coupon to amount', function () {
        $coupon = Coupon::factory()->create([
            'code' => 'SAVE20',
            'discount_type' => 'percentage',
            'discount_value' => 20.00,
            'is_active' => true,
        ]);

        $amount = 100.00;
        $discountAmount = $this->service->applyCoupon($coupon, $amount);

        expect($discountAmount)->toBe(20.00);
    });

    test('applies fixed amount coupon', function () {
        $coupon = Coupon::factory()->create([
            'code' => 'SAVE10',
            'discount_type' => 'fixed',
            'discount_value' => 10.00,
            'is_active' => true,
        ]);

        $amount = 100.00;
        $discountAmount = $this->service->applyCoupon($coupon, $amount);

        expect($discountAmount)->toBe(10.00);
    });

    test('respects minimum purchase amount', function () {
        $coupon = Coupon::factory()->create([
            'code' => 'BIG50',
            'discount_type' => 'fixed',
            'discount_value' => 50.00,
            'is_active' => true,
            'min_purchase_amount' => 200.00,
        ]);

        $amount = 100.00; // Less than minimum

        expect(fn() => $this->service->applyCoupon($coupon, $amount))
            ->toThrow(\App\Exceptions\Discount\InvalidCouponException::class);
    });
});

describe('Discount Combination', function () {
    test('can stack multiple discounts if allowed', function () {
        $discount1 = 10.00; // $10 off
        $discount2 = 5.00;  // $5 off

        $amount = 100.00;
        $finalAmount = $this->service->applyMultipleDiscounts($amount, [$discount1, $discount2]);

        expect($finalAmount)->toBe(85.00); // 100 - 10 - 5
    });

    test('prevents stacking when not allowed', function () {
        $discount1 = Discount::factory()->create([
            'name' => 'First Discount',
            'can_stack' => false,
        ]);

        $discount2 = Discount::factory()->create([
            'name' => 'Second Discount',
        ]);

        expect(fn() => $this->service->checkDiscountStackability([$discount1, $discount2]))
            ->toThrow(\App\Exceptions\Discount\InvalidCouponException::class);
    });
});
