<?php

namespace App\Services;

use App\Models\TaxRate;

class TaxService
{
    public function calculateTax(float $amount, ?int $taxRateId = null): float
    {
        if (!$taxRateId) {
            $taxRate = TaxRate::where('is_default', true)->first();
        } else {
            $taxRate = TaxRate::find($taxRateId);
        }

        if (!$taxRate) {
            return 0;
        }

        return round(($amount * $taxRate->rate) / 100, 2);
    }

    public function getDefaultTaxRate(): ?TaxRate
    {
        return TaxRate::where('is_default', true)->where('active', true)->first();
    }
}