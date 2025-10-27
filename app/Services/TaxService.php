<?php

namespace App\Services;

use App\Models\TaxRate;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class TaxService
{
    /**
     * Calculate tax amount for a given amount and tax rate percentage
     *
     * @param float $amount The base amount to calculate tax on
     * @param float $taxRate The tax rate as a percentage (e.g., 10 for 10%)
     * @return float The calculated tax amount
     */
    public function calculateTax(float $amount, float $taxRate): float
    {
        if ($amount < 0) {
            throw new InvalidArgumentException("Amount cannot be negative");
        }

        if ($taxRate < 0) {
            throw new InvalidArgumentException("Tax rate cannot be negative");
        }

        $tax = ($amount * $taxRate) / 100;
        return round($tax, 2);
    }

    /**
     * Calculate total amount including tax
     *
     * @param float $subtotal The subtotal before tax
     * @param float $taxRate The tax rate as a percentage
     * @return float The total including tax
     */
    public function calculateTotalWithTax(float $subtotal, float $taxRate): float
    {
        $taxAmount = $this->calculateTax($subtotal, $taxRate);
        return round($subtotal + $taxAmount, 2);
    }

    /**
     * Extract the tax amount from a total that already includes tax
     *
     * @param float $totalWithTax The total amount including tax
     * @param float $taxRate The tax rate as a percentage
     * @return float The tax amount
     */
    public function extractTaxFromTotal(float $totalWithTax, float $taxRate): float
    {
        if ($taxRate == 0) {
            return 0.00;
        }

        // Formula: tax = total - (total / (1 + rate/100))
        // Or: tax = total * (rate / (100 + rate))
        $taxMultiplier = $taxRate / (100 + $taxRate);
        $taxAmount = $totalWithTax * $taxMultiplier;

        return round($taxAmount, 2);
    }

    /**
     * Calculate total with multiple tax rates applied
     *
     * @param float $subtotal The subtotal before taxes
     * @param array $taxRates Array of tax rates as percentages
     * @return float The total including all taxes
     */
    public function calculateTotalWithMultipleTaxes(float $subtotal, array $taxRates): float
    {
        $total = $subtotal;

        foreach ($taxRates as $rate) {
            $taxAmount = $this->calculateTax($subtotal, $rate);
            $total += $taxAmount;
        }

        return round($total, 2);
    }

    /**
     * Get all active tax rates from database
     *
     * @return Collection
     */
    public function getActiveTaxRates(): Collection
    {
        return TaxRate::where('active', true)->get();
    }

    /**
     * Get default tax rate if configured
     *
     * @return TaxRate|null
     */
    public function getDefaultTaxRate(): ?TaxRate
    {
        return TaxRate::where('is_default', true)
            ->where('active', true)
            ->first();
    }

    /**
     * Validate a tax rate value
     *
     * @param float $rate The tax rate to validate
     * @return bool
     * @throws InvalidArgumentException
     */
    public function validateTaxRate(float $rate): bool
    {
        if ($rate < 0) {
            throw new InvalidArgumentException("Tax rate cannot be negative");
        }

        if ($rate > 100) {
            throw new InvalidArgumentException("Tax rate cannot exceed 100%");
        }

        return true;
    }

    /**
     * Calculate tax using a TaxRate model
     *
     * @param float $amount The amount to calculate tax on
     * @param int|null $taxRateId The tax rate ID from database
     * @return float
     */
    public function calculateTaxFromModel(float $amount, ?int $taxRateId = null): float
    {
        if (!$taxRateId) {
            $taxRate = $this->getDefaultTaxRate();
        } else {
            $taxRate = TaxRate::find($taxRateId);
        }

        if (!$taxRate) {
            return 0;
        }

        return $this->calculateTax($amount, $taxRate->rate);
    }
}
