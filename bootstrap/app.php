<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        App\Providers\RateLimitServiceProvider::class,
        App\Providers\EventServiceProvider::class,
    ])
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'store.access' => \App\Http\Middleware\EnsureUserBelongsToStore::class,
            'cash.drawer' => \App\Http\Middleware\CheckCashDrawer::class,
        ]);

        // Configure default API rate limiting
        $middleware->throttleApi();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Report custom exceptions with additional context
        $exceptions->report(function (\App\Exceptions\POSException $e) {
            \Illuminate\Support\Facades\Log::error('POS Exception occurred', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
        });

        // Custom rendering for API exceptions
        $exceptions->render(function (\App\Exceptions\POSException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'error' => class_basename($e),
                    'code' => $e->getCode() ?: 400,
                ], $e->getCode() ?: 400);
            }
        });

        // Handle validation exceptions for API
        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Validation failed.',
                    'errors' => $e->errors(),
                ], 422);
            }
        });

        // Handle authentication exceptions for API
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Unauthenticated.',
                ], 401);
            }
        });

        // Handle authorization exceptions for API
        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Unauthorized. You do not have permission to perform this action.',
                ], 403);
            }
        });

        // Handle model not found exceptions for API
        $exceptions->render(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Resource not found.',
                ], 404);
            }
        });

        // Handle general exceptions for API
        $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                $statusCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;

                // Don't expose internal errors in production
                $message = app()->environment('production') && $statusCode === 500
                    ? 'An unexpected error occurred. Please try again later.'
                    : $e->getMessage();

                return response()->json([
                    'message' => $message,
                    'error' => app()->environment('production') ? 'ServerError' : class_basename($e),
                ], $statusCode);
            }
        });
    })->create();
