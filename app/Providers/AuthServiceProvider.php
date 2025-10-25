<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Brand;
use App\Models\CashDrawer;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Discount;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Sale;
use App\Models\StockAdjustment;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\User;
use App\Policies\BrandPolicy;
use App\Policies\CashDrawerPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\CouponPolicy;
use App\Policies\CustomerPolicy;
use App\Policies\DiscountPolicy;
use App\Policies\ProductPolicy;
use App\Policies\PurchaseOrderPolicy;
use App\Policies\SalePolicy;
use App\Policies\StockAdjustmentPolicy;
use App\Policies\StorePolicy;
use App\Policies\SupplierPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

/**
 * Authorization Service Provider.
 *
 * Registers all authorization policies and gates for the POS system.
 * Integrates with Spatie Permission for role-based access control.
 */
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // Core Models
        User::class => UserPolicy::class,
        Store::class => StorePolicy::class,

        // Product & Inventory
        Product::class => ProductPolicy::class,
        Category::class => CategoryPolicy::class,
        Brand::class => BrandPolicy::class,
        Supplier::class => SupplierPolicy::class,

        // Sales & Customers
        Sale::class => SalePolicy::class,
        Customer::class => CustomerPolicy::class,

        // Inventory Management
        PurchaseOrder::class => PurchaseOrderPolicy::class,
        StockAdjustment::class => StockAdjustmentPolicy::class,

        // Cash Management
        CashDrawer::class => CashDrawerPolicy::class,

        // Discounts & Promotions
        Discount::class => DiscountPolicy::class,
        Coupon::class => CouponPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Register custom gates
        $this->registerGates();
    }

    /**
     * Register custom authorization gates.
     */
    protected function registerGates(): void
    {
        // Super Admin gate - bypasses all authorization checks
        Gate::before(function (User $user, string $ability) {
            if ($user->hasRole('super-admin')) {
                return true;
            }
        });

        // View any reports gate
        Gate::define('view-reports', function (User $user) {
            return $user->hasAnyRole(['super-admin', 'store-manager', 'accountant']);
        });

        // Export data gate
        Gate::define('export-data', function (User $user) {
            return $user->hasAnyRole(['super-admin', 'store-manager', 'accountant']);
        });

        // Manage store settings gate
        Gate::define('manage-settings', function (User $user) {
            return $user->hasAnyRole(['super-admin', 'store-manager']);
        });

        // Process refunds gate
        Gate::define('process-refunds', function (User $user) {
            return $user->hasAnyRole(['super-admin', 'store-manager']);
        });

        // Manage users gate
        Gate::define('manage-users', function (User $user) {
            return $user->hasAnyRole(['super-admin', 'store-manager']);
        });

        // View analytics gate
        Gate::define('view-analytics', function (User $user) {
            return $user->hasAnyRole(['super-admin', 'store-manager', 'accountant']);
        });

        // Access POS interface gate
        Gate::define('access-pos', function (User $user) {
            return $user->hasAnyRole(['super-admin', 'store-manager', 'cashier']);
        });

        // Access Nova admin panel gate
        Gate::define('access-nova', function (User $user) {
            // All authenticated users can access Nova, but resource policies control what they see
            return true;
        });
    }
}
