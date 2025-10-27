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
        $discount = Discount::factory()->percentage(10)->create([
            'start_date' => now()->subDay(),
            'end_date' => now()->addDay(),
        ]);

        $coupon = Coupon::factory()->create([
            'discount_id' => $discount->id,
            'code' => 'SAVE10',
            'active' => true,
        ]);

        $isValid = $this->service->validateCoupon($coupon);

        expect($isValid)->toBeTrue();
    });

    test('rejects inactive coupon', function () {
        $discount = Discount::factory()->create();
        $coupon = Coupon::factory()->inactive()->create([
            'discount_id' => $discount->id,
            'code' => 'INACTIVE',
        ]);

        expect(fn() => $this->service->validateCoupon($coupon))
            ->toThrow(\App\Exceptions\Discount\InvalidCouponException::class);
    });

    test('rejects expired coupon', function () {
        $discount = Discount::factory()->create();
        $coupon = Coupon::factory()->expired()->create([
            'discount_id' => $discount->id,
            'code' => 'EXPIRED',
        ]);

        expect(fn() => $this->service->validateCoupon($coupon))
            ->toThrow(\App\Exceptions\Discount\InvalidCouponException::class);
    });

    test('rejects coupon that hasnt started yet', function () {
        $discount = Discount::factory()->notStarted()->create();
        $coupon = Coupon::factory()->create([
            'discount_id' => $discount->id,
            'code' => 'FUTURE',
        ]);

        expect(fn() => $this->service->validateCoupon($coupon))
            ->toThrow(\App\Exceptions\Discount\InvalidCouponException::class);
    });

    test('rejects coupon that exceeded usage limit', function () {
        $discount = Discount::factory()->create();
        $coupon = Coupon::factory()->maxedOut()->create([
            'discount_id' => $discount->id,
            'code' => 'LIMITED',
        ]);

        expect(fn() => $this->service->validateCoupon($coupon))
            ->toThrow(\App\Exceptions\Discount\InvalidCouponException::class);
    });
});

describe('Coupon Application', function () {
    test('applies percentage coupon to amount', function () {
        $discount = Discount::factory()->percentage(20)->create();
        $coupon = Coupon::factory()->create([
            'discount_id' => $discount->id,
            'code' => 'SAVE20',
        ]);

        $amount = 100.00;
        $discountAmount = $this->service->applyCoupon($coupon, $amount);

        expect($discountAmount)->toBe(20.00);
    });

    test('applies fixed amount coupon', function () {
        $discount = Discount::factory()->fixed(10)->create();
        $coupon = Coupon::factory()->create([
            'discount_id' => $discount->id,
            'code' => 'SAVE10',
        ]);

        $amount = 100.00;
        $discountAmount = $this->service->applyCoupon($coupon, $amount);

        expect($discountAmount)->toBe(10.00);
    });

    test('respects minimum purchase amount', function () {
        $discount = Discount::factory()->fixed(50)->create([
            'min_amount' => 200.00,
        ]);
        $coupon = Coupon::factory()->create([
            'discount_id' => $discount->id,
            'code' => 'BIG50',
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

    // Note: Discount stacking feature not implemented in current schema
    // Future enhancement: Add can_stack column to discounts table
});
