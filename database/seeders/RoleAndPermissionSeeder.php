<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions (hyphenated format for compatibility)
        $permissions = [
            // Store Management
            'view-stores', 'create-stores', 'edit-stores', 'delete-stores', 'manage-stores',

            // User Management
            'view-users', 'create-users', 'edit-users', 'delete-users', 'manage-users',

            // Product Management
            'view-products', 'create-products', 'edit-products', 'delete-products', 'manage-products',
            'view-categories', 'create-categories', 'edit-categories', 'delete-categories',
            'view-brands', 'create-brands', 'edit-brands', 'delete-brands',

            // Inventory Management
            'view-inventory', 'adjust-inventory', 'transfer-inventory', 'manage-inventory',
            'view-stock-movements', 'view-stock-adjustments',
            'view-suppliers', 'create-suppliers', 'edit-suppliers', 'delete-suppliers',
            'view-purchase-orders', 'create-purchase-orders', 'edit-purchase-orders', 'delete-purchase-orders',

            // Sales
            'view-sales', 'create-sales', 'edit-sales', 'delete-sales', 'void-sales', 'process-sales',
            'view-sale-items', 'apply-discounts', 'apply-coupons',

            // Returns
            'view-returns', 'create-returns', 'approve-returns', 'process-refunds',

            // Customer Management
            'view-customers', 'create-customers', 'edit-customers', 'delete-customers', 'manage-customers',
            'view-customer-groups', 'create-customer-groups', 'edit-customer-groups', 'delete-customer-groups',
            'manage-loyalty-points', 'manage-store-credit',

            // Cash Management
            'view-cash-drawer', 'open-cash-drawer', 'close-cash-drawer',
            'view-cash-transactions', 'create-cash-transactions',
            'manage-petty-cash',

            // Discounts & Promotions
            'view-discounts', 'create-discounts', 'edit-discounts', 'delete-discounts',
            'view-coupons', 'create-coupons', 'edit-coupons', 'delete-coupons',

            // Reports
            'view-reports', 'view-sales-reports', 'view-inventory-reports',
            'view-customer-reports', 'view-financial-reports', 'export-reports',

            // Settings
            'view-settings', 'edit-settings',
            'view-payment-methods', 'create-payment-methods', 'edit-payment-methods', 'delete-payment-methods',
            'view-tax-rates', 'create-tax-rates', 'edit-tax-rates', 'delete-tax-rates',

            // Receipts
            'view-receipts', 'print-receipts', 'email-receipts',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions

        // 1. Super Admin - Full access
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdmin->syncPermissions(Permission::all());

        // 2. Store Manager - Manage everything in their store
        $storeManager = Role::firstOrCreate(['name' => 'Store Manager']);
        $storeManager->syncPermissions([
            'view-users', 'create-users', 'edit-users',
            'view-products', 'create-products', 'edit-products', 'delete-products', 'manage-products',
            'view-categories', 'create-categories', 'edit-categories', 'delete-categories',
            'view-brands', 'create-brands', 'edit-brands', 'delete-brands',
            'view-inventory', 'adjust-inventory', 'view-stock-movements', 'view-stock-adjustments', 'manage-inventory',
            'view-suppliers', 'create-suppliers', 'edit-suppliers',
            'view-purchase-orders', 'create-purchase-orders', 'edit-purchase-orders',
            'view-sales', 'create-sales', 'void-sales', 'process-sales',
            'view-returns', 'create-returns', 'approve-returns', 'process-refunds',
            'view-customers', 'create-customers', 'edit-customers', 'manage-customers',
            'view-customer-groups', 'create-customer-groups', 'edit-customer-groups',
            'manage-loyalty-points', 'manage-store-credit',
            'view-cash-drawer', 'open-cash-drawer', 'close-cash-drawer',
            'view-cash-transactions', 'create-cash-transactions', 'manage-petty-cash',
            'view-discounts', 'create-discounts', 'edit-discounts',
            'view-coupons', 'create-coupons', 'edit-coupons',
            'view-reports', 'view-sales-reports', 'view-inventory-reports',
            'view-customer-reports', 'view-financial-reports', 'export-reports',
            'view-payment-methods', 'view-tax-rates',
            'view-receipts', 'print-receipts', 'email-receipts',
        ]);

        // 3. Cashier - POS operations
        $cashier = Role::firstOrCreate(['name' => 'Cashier']);
        $cashier->syncPermissions([
            'view-products', 'view-inventory',
            'view-sales', 'create-sales', 'view-sale-items', 'process-sales',
            'apply-discounts', 'apply-coupons',
            'view-returns', 'create-returns',
            'view-customers', 'create-customers', 'edit-customers', 'manage-customers',
            'manage-loyalty-points', 'manage-store-credit',
            'view-cash-drawer', 'open-cash-drawer', 'close-cash-drawer',
            'view-cash-transactions', 'create-cash-transactions',
            'view-receipts', 'print-receipts', 'email-receipts',
        ]);

        // 4. Inventory Manager - Stock and procurement
        $inventoryManager = Role::firstOrCreate(['name' => 'Inventory Manager']);
        $inventoryManager->syncPermissions([
            'view-products', 'create-products', 'edit-products', 'manage-products',
            'view-categories', 'create-categories', 'edit-categories',
            'view-brands', 'create-brands', 'edit-brands',
            'view-inventory', 'adjust-inventory', 'transfer-inventory', 'manage-inventory',
            'view-stock-movements', 'view-stock-adjustments',
            'view-suppliers', 'create-suppliers', 'edit-suppliers',
            'view-purchase-orders', 'create-purchase-orders', 'edit-purchase-orders',
            'view-reports', 'view-inventory-reports', 'export-reports',
        ]);

        // 5. Accountant - Financial operations and reports
        $accountant = Role::firstOrCreate(['name' => 'Accountant']);
        $accountant->syncPermissions([
            'view-sales', 'view-returns',
            'view-customers',
            'view-cash-drawer', 'view-cash-transactions',
            'view-discounts', 'view-coupons',
            'view-reports', 'view-sales-reports', 'view-customer-reports',
            'view-financial-reports', 'export-reports',
            'view-payment-methods', 'view-tax-rates',
            'view-receipts',
        ]);
    }
}
