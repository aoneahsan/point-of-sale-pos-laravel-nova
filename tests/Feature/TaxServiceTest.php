<?php

declare(strict_types=1);

use App\Models\Store;
use App\Models\TaxRate;
use App\Services\TaxService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->service = app(TaxService::class);
    $this->store = Store::factory()->create();
    $this->taxRate = TaxRate::factory()->create([
        'name' => 'Standard VAT',
        'rate' => 10.00, // 10%
        'active' => true,
    ]);
});

describe('Tax Calculation', function () {
    test('calculates tax correctly for given amount', function () {
        $amount = 100.00;
        $taxAmount = $this->service->calculateTax($amount, $this->taxRate->rate);

        expect($taxAmount)->toBe(10.00); // 10% of 100
    });

    test('calculates tax for zero amount', function () {
        $taxAmount = $this->service->calculateTax(0.00, $this->taxRate->rate);

        expect($taxAmount)->toBe(0.00);
    });

    test('calculates tax with decimal rate', function () {
        $amount = 100.00;
        $rate = 7.5; // 7.5%

        $taxAmount = $this->service->calculateTax($amount, $rate);

        expect($taxAmount)->toBe(7.50);
    });

    test('rounds tax amount to 2 decimal places', function () {
        $amount = 100.00;
        $rate = 7.33; // Results in 7.33

        $taxAmount = $this->service->calculateTax($amount, $rate);

        expect($taxAmount)->toBe(7.33);
    });
});

describe('Total with Tax', function () {
    test('calculates total including tax', function () {
        $subtotal = 100.00;
        $taxRate = 10.00;

        $total = $this->service->calculateTotalWithTax($subtotal, $taxRate);

        expect($total)->toBe(110.00); // 100 + 10% tax
    });

    test('handles zero tax rate', function () {
        $subtotal = 100.00;
        $taxRate = 0.00;

        $total = $this->service->calculateTotalWithTax($subtotal, $taxRate);

        expect($total)->toBe(100.00); // No tax added
    });
});

describe('Tax from Total', function () {
    test('extracts tax amount from total that includes tax', function () {
        $totalWithTax = 110.00;
        $taxRate = 10.00;

        // Total includes 10% tax, so base = 110 / 1.1 = 100
        // Tax = 110 - 100 = 10
        $taxAmount = $this->service->extractTaxFromTotal($totalWithTax, $taxRate);

        expect($taxAmount)->toBe(10.00);
    });

    test('returns zero when extracting tax with zero rate', function () {
        $totalWithTax = 100.00;
        $taxRate = 0.00;

        $taxAmount = $this->service->extractTaxFromTotal($totalWithTax, $taxRate);

        expect($taxAmount)->toBe(0.00);
    });
});

describe('Multiple Tax Rates', function () {
    test('calculates total with multiple tax rates', function () {
        $subtotal = 100.00;
        $taxRates = [10.00, 5.00]; // 10% + 5% = 15%

        $total = $this->service->calculateTotalWithMultipleTaxes($subtotal, $taxRates);

        expect($total)->toBe(115.00);
    });

    test('handles empty tax rates array', function () {
        $subtotal = 100.00;
        $taxRates = [];

        $total = $this->service->calculateTotalWithMultipleTaxes($subtotal, $taxRates);

        expect($total)->toBe(100.00);
    });
});

describe('Tax Rate Retrieval', function () {
    test('retrieves active tax rates', function () {
        $activeTaxRates = $this->service->getActiveTaxRates();

        expect($activeTaxRates)->toHaveCount(1)
            ->and($activeTaxRates->first()->id)->toBe($this->taxRate->id);
    });

    test('excludes inactive tax rates', function () {
        $this->taxRate->update(['active' => false]);

        $activeTaxRates = $this->service->getActiveTaxRates();

        expect($activeTaxRates)->toHaveCount(0);
    });
});

describe('Tax Validation', function () {
    test('validates tax rate is positive', function () {
        expect(fn() => $this->service->validateTaxRate(-5.00))
            ->toThrow(InvalidArgumentException::class);
    });

    test('validates tax rate is not exceeding 100 percent', function () {
        expect(fn() => $this->service->validateTaxRate(150.00))
            ->toThrow(InvalidArgumentException::class);
    });

    test('accepts valid tax rate', function () {
        $result = $this->service->validateTaxRate(15.00);

        expect($result)->toBeTrue();
    });
});
