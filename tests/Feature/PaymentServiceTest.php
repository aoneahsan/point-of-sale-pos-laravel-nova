<?php

declare(strict_types=1);

use App\Models\PaymentMethod;
use App\Models\Sale;
use App\Models\Store;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->service = app(PaymentService::class);
    $this->store = Store::factory()->create();
    $this->user = User::factory()->create(['store_id' => $this->store->id]);
    $this->sale = Sale::factory()->create([
        'store_id' => $this->store->id,
        'user_id' => $this->user->id,
        'total' => 100.00,
    ]);
    $this->cashPayment = PaymentMethod::factory()->create([
        'name' => 'Cash',
        'code' => 'cash',
        'active' => true,
    ]);
    $this->cardPayment = PaymentMethod::factory()->create([
        'name' => 'Credit Card',
        'code' => 'credit_card',
        'active' => true,
    ]);
});

describe('Single Payment Processing', function () {
    test('can process cash payment', function () {
        $payment = $this->service->processPayment(
            sale: $this->sale,
            paymentMethodId: $this->cashPayment->id,
            amount: 100.00,
            reference: null
        );

        expect($payment->amount)->toEqual(100.00)
            ->and($payment->payment_method_id)->toBe($this->cashPayment->id)
            ->and($payment->sale_id)->toBe($this->sale->id);
    });

    test('can process card payment with reference', function () {
        $payment = $this->service->processPayment(
            sale: $this->sale,
            paymentMethodId: $this->cardPayment->id,
            amount: 100.00,
            reference: 'CARD-12345'
        );

        expect($payment->reference)->toBe('CARD-12345')
            ->and($payment->amount)->toEqual(100.00);
    });

    test('throws exception for inactive payment method', function () {
        $this->cashPayment->update(['active' => false]);

        expect(fn() => $this->service->processPayment(
            sale: $this->sale,
            paymentMethodId: $this->cashPayment->id,
            amount: 100.00
        ))->toThrow(\App\Exceptions\Payment\InvalidPaymentMethodException::class);
    });

    test('throws exception for zero amount', function () {
        expect(fn() => $this->service->processPayment(
            sale: $this->sale,
            paymentMethodId: $this->cashPayment->id,
            amount: 0.00
        ))->toThrow(\App\Exceptions\Payment\PaymentFailedException::class);
    });

    test('throws exception for negative amount', function () {
        expect(fn() => $this->service->processPayment(
            sale: $this->sale,
            paymentMethodId: $this->cashPayment->id,
            amount: -50.00
        ))->toThrow(\App\Exceptions\Payment\PaymentFailedException::class);
    });
});

describe('Split Payment Processing', function () {
    test('can process split payment with multiple methods', function () {
        $payments = $this->service->processSplitPayment(
            sale: $this->sale,
            payments: [
                [
                    'payment_method_id' => $this->cashPayment->id,
                    'amount' => 60.00,
                ],
                [
                    'payment_method_id' => $this->cardPayment->id,
                    'amount' => 40.00,
                    'reference' => 'CARD-67890',
                ],
            ]
        );

        expect($payments)->toHaveCount(2)
            ->and($payments->sum('amount'))->toBe(100.00);
    });

    test('validates total payment matches sale total', function () {
        expect(fn() => $this->service->processSplitPayment(
            sale: $this->sale,
            payments: [
                [
                    'payment_method_id' => $this->cashPayment->id,
                    'amount' => 50.00, // Only 50, but sale is 100
                ],
            ]
        ))->toThrow(\App\Exceptions\Payment\PaymentFailedException::class);
    });

    test('rejects overpayment', function () {
        expect(fn() => $this->service->processSplitPayment(
            sale: $this->sale,
            payments: [
                [
                    'payment_method_id' => $this->cashPayment->id,
                    'amount' => 150.00, // More than sale total
                ],
            ]
        ))->toThrow(\App\Exceptions\Payment\PaymentFailedException::class);
    });
});

describe('Change Calculation', function () {
    test('calculates change for cash payment', function () {
        $change = $this->service->calculateChange(
            amountDue: 100.00,
            amountReceived: 150.00
        );

        expect($change)->toBe(50.00);
    });

    test('returns zero change when exact amount received', function () {
        $change = $this->service->calculateChange(
            amountDue: 100.00,
            amountReceived: 100.00
        );

        expect($change)->toBe(0.00);
    });

    test('throws exception when received amount is less than due', function () {
        expect(fn() => $this->service->calculateChange(
            amountDue: 100.00,
            amountReceived: 80.00
        ))->toThrow(\App\Exceptions\Payment\PaymentFailedException::class);
    });
});

describe('Payment Validation', function () {
    test('validates payment method exists', function () {
        $isValid = $this->service->validatePaymentMethod($this->cashPayment->id);

        expect($isValid)->toBeTrue();
    });

    test('returns false for non-existent payment method', function () {
        $isValid = $this->service->validatePaymentMethod(9999);

        expect($isValid)->toBeFalse();
    });

    test('validates payment method is active', function () {
        $isActive = $this->service->isPaymentMethodActive($this->cashPayment->id);

        expect($isActive)->toBeTrue();
    });

    test('returns false for inactive payment method', function () {
        $this->cashPayment->update(['active' => false]);

        $isActive = $this->service->isPaymentMethodActive($this->cashPayment->id);

        expect($isActive)->toBeFalse();
    });
});

describe('Payment Refund', function () {
    test('can process full refund', function () {
        $originalPayment = $this->service->processPayment(
            sale: $this->sale,
            paymentMethodId: $this->cashPayment->id,
            amount: 100.00
        );

        $refund = $this->service->refundPayment(
            payment: $originalPayment,
            amount: 100.00,
            reason: 'Customer return'
        );

        expect($refund->amount)->toEqual(-100.00) // Negative for refund
            ->and($refund->sale_id)->toBe($this->sale->id);
    });

    test('can process partial refund', function () {
        $originalPayment = $this->service->processPayment(
            sale: $this->sale,
            paymentMethodId: $this->cashPayment->id,
            amount: 100.00
        );

        $refund = $this->service->refundPayment(
            payment: $originalPayment,
            amount: 50.00,
            reason: 'Partial return'
        );

        expect($refund->amount)->toEqual(-50.00);
    });

    test('throws exception when refund exceeds original payment', function () {
        $originalPayment = $this->service->processPayment(
            sale: $this->sale,
            paymentMethodId: $this->cashPayment->id,
            amount: 100.00
        );

        expect(fn() => $this->service->refundPayment(
            payment: $originalPayment,
            amount: 150.00,
            reason: 'Invalid refund'
        ))->toThrow(\App\Exceptions\Payment\PaymentFailedException::class);
    });
});
