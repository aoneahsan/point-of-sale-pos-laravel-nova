<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use App\Models\Product;
use App\Models\TaxRate;
use App\Models\PaymentMethod;
use App\Models\Setting;

class CacheService
{
    const CACHE_TTL = 3600; // 1 hour

    public function getProducts(int $storeId)
    {
        return Cache::remember("products.store.{$storeId}", self::CACHE_TTL, function () use ($storeId) {
            return Product::with(['variants' => function ($query) use ($storeId) {
                $query->where('store_id', $storeId);
            }])->active()->get();
        });
    }

    public function getProduct(int $productId)
    {
        return Cache::remember("product.{$productId}", self::CACHE_TTL, function () use ($productId) {
            return Product::with(['category', 'brand', 'taxRate', 'variants'])->find($productId);
        });
    }

    public function getTaxRates()
    {
        return Cache::remember('tax_rates', self::CACHE_TTL, function () {
            return TaxRate::active()->get();
        });
    }

    public function getDefaultTaxRate()
    {
        return Cache::remember('tax_rate.default', self::CACHE_TTL, function () {
            return TaxRate::where('is_default', true)->where('active', true)->first();
        });
    }

    public function getPaymentMethods()
    {
        return Cache::remember('payment_methods', self::CACHE_TTL, function () {
            return PaymentMethod::active()->orderBy('sort_order')->get();
        });
    }

    public function getSetting(string $key, $default = null)
    {
        return Cache::remember("setting.{$key}", self::CACHE_TTL, function () use ($key, $default) {
            $setting = Setting::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    public function clearProductCache(int $storeId = null)
    {
        if ($storeId) {
            Cache::forget("products.store.{$storeId}");
        } else {
            Cache::flush();
        }
    }

    public function clearTaxCache()
    {
        Cache::forget('tax_rates');
        Cache::forget('tax_rate.default');
    }

    public function clearPaymentMethodsCache()
    {
        Cache::forget('payment_methods');
    }

    public function clearAllCache()
    {
        Cache::flush();
    }
}
