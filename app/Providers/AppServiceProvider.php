<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Sale;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Observers\SaleObserver;
use App\Observers\ProductObserver;
use App\Observers\CategoryObserver;
use App\Observers\BrandObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register observers
        Sale::observe(SaleObserver::class);
        Product::observe(ProductObserver::class);
        Category::observe(CategoryObserver::class);
        Brand::observe(BrandObserver::class);
    }
}
