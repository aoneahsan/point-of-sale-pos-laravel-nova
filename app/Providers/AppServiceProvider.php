<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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

        // Configure rate limiters
        $this->configureRateLimiting();
    }

    /**
     * Configure the application's rate limiters.
     */
    protected function configureRateLimiting(): void
    {
        // General API rate limit: 60 requests per minute per user
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip())
                ->response(function () {
                    return response()->json([
                        'error' => [
                            'code' => 'RATE_LIMIT_EXCEEDED',
                            'message' => 'Too many requests. Please try again later.',
                            'status' => 429,
                        ],
                    ], 429);
                });
        });

        // Login rate limit: 5 attempts per minute per IP
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip())
                ->response(function () {
                    return response()->json([
                        'error' => [
                            'code' => 'TOO_MANY_LOGIN_ATTEMPTS',
                            'message' => 'Too many login attempts. Please try again in 1 minute.',
                            'status' => 429,
                        ],
                    ], 429);
                });
        });

        // Reports rate limit: 10 requests per minute (more intensive operations)
        RateLimiter::for('reports', function (Request $request) {
            return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip())
                ->response(function () {
                    return response()->json([
                        'error' => [
                            'code' => 'RATE_LIMIT_EXCEEDED',
                            'message' => 'Too many report requests. Please try again later.',
                            'status' => 429,
                        ],
                    ], 429);
                });
        });
    }
}
