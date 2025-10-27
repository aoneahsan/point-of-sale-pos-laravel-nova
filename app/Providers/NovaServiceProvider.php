<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Laravel\Fortify\Features;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        parent::boot();

        // Load custom theme to completely rebrand the interface
        Nova::style('custom-theme', resource_path('css/nova-theme.css'));

        // Set custom footer content
        Nova::footer(function ($request) {
            return '<div class="text-center text-sm text-gray-500">
                POS System &copy; ' . date('Y') . ' - All Rights Reserved
            </div>';
        });

        // Set page title
        Nova::mainMenu(function ($request) {
            return [
                // This will be the main menu items
                // Nova automatically adds resources here
            ];
        });

        // Customize user menu
        Nova::userMenu(function ($request, $menu) {
            return $menu;
        });

        // Set the application name that appears in page titles
        Nova::withBreadcrumbs();

        // Hide "Nova" from browser tab titles
        Nova::initialPath('/dashboards/main-dashboard');
    }

    /**
     * Register the configurations for Laravel Fortify.
     */
    protected function fortify(): void
    {
        Nova::fortify()
            ->features([
                Features::updatePasswords(),
                // Features::emailVerification(),
                // Features::twoFactorAuthentication(['confirm' => true, 'confirmPassword' => true]),
            ])
            ->register();
    }

    /**
     * Register the Nova routes.
     */
    protected function routes(): void
    {
        Nova::routes()
            ->withAuthenticationRoutes(default: true)
            ->withPasswordResetRoutes()
            ->withoutEmailVerificationRoutes()
            ->register();
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     */
    protected function gate(): void
    {
        Gate::define('viewNova', function (User $user) {
            // Allow all authenticated and active users to access Nova
            // Additional permissions are controlled by Nova policies
            return $user->active === true;
        });
    }

    /**
     * Get the dashboards that should be listed in the Nova sidebar.
     *
     * @return array<int, \Laravel\Nova\Dashboard>
     */
    protected function dashboards(): array
    {
        return [
            new \App\Nova\Dashboards\MainDashboard,
            new \App\Nova\Dashboards\InventoryDashboard,
            new \App\Nova\Dashboards\ReportsDashboard,
        ];
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array<int, \Laravel\Nova\Tool>
     */
    public function tools(): array
    {
        return [];
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        parent::register();

        //
    }
}
