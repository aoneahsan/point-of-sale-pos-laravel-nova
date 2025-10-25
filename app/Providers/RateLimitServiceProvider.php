<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Rate Limit Service Provider
 *
 * Configures rate limiting for different API endpoints.
 * Implements tiered rate limiting based on endpoint sensitivity.
 */
final class RateLimitServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();
    }

    /**
     * Configure the rate limiters for the application.
     *
     * Implements different rate limits for various endpoint types:
     * - login: 5 requests per minute (stricter for security)
     * - api: 60 requests per minute (default for authenticated API)
     * - reports: 30 requests per minute (resource-intensive)
     */
    protected function configureRateLimiting(): void
    {
        // Login endpoint - strict rate limiting (prevent brute force)
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)
                ->by($request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'message' => 'Too many login attempts. Please try again in 1 minute.',
                        'retry_after' => $headers['Retry-After'] ?? 60,
                    ], 429, $headers);
                });
        });

        // Default API rate limiting (60 req/min per user)
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)
                ->by($request->user()?->id ?: $request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'message' => 'Too many requests. Please slow down.',
                        'retry_after' => $headers['Retry-After'] ?? 60,
                        'limit' => 60,
                    ], 429, $headers);
                });
        });

        // Report generation - lower limit (resource-intensive)
        RateLimiter::for('reports', function (Request $request) {
            return Limit::perMinute(30)
                ->by($request->user()?->id ?: $request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'message' => 'Report generation rate limit exceeded.',
                        'retry_after' => $headers['Retry-After'] ?? 60,
                        'limit' => 30,
                    ], 429, $headers);
                });
        });

        // POS operations - higher limit (cashier needs speed)
        RateLimiter::for('pos', function (Request $request) {
            return Limit::perMinute(120)
                ->by($request->user()?->id ?: $request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'message' => 'POS rate limit exceeded. Please wait.',
                        'retry_after' => $headers['Retry-After'] ?? 60,
                        'limit' => 120,
                    ], 429, $headers);
                });
        });

        // Burst protection - prevent sudden spikes
        RateLimiter::for('burst', function (Request $request) {
            return [
                Limit::perMinute(100)->by($request->user()?->id ?: $request->ip()),
                Limit::perHour(1000)->by($request->user()?->id ?: $request->ip()),
            ];
        });
    }
}
