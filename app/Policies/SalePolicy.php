<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Sale;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Authorization policy for Sale model.
 *
 * Controls access to sales transactions and related operations.
 */
class SalePolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any sales.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view-sales') ||
               $user->hasAnyRole(['super-admin', 'store-manager', 'cashier', 'accountant']);
    }

    /**
     * Determine if the user can view the sale.
     */
    public function view(User $user, Sale $sale): bool
    {
        // Super admin can view all
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Store managers and accountants can view all sales in their store
        if ($user->hasAnyRole(['store-manager', 'accountant'])) {
            return $user->store_id === $sale->store_id;
        }

        // Cashiers can view their own sales
        if ($user->hasRole('cashier')) {
            return $user->id === $sale->user_id;
        }

        return false;
    }

    /**
     * Determine if the user can create sales.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create-sales') ||
               $user->hasAnyRole(['super-admin', 'store-manager', 'cashier']);
    }

    /**
     * Determine if the user can update the sale.
     */
    public function update(User $user, Sale $sale): bool
    {
        // Sales cannot be updated once completed
        if ($sale->status === 'completed') {
            return false;
        }

        // Super admin can update all
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Store managers can update sales in their store
        if ($user->hasRole('store-manager')) {
            return $user->store_id === $sale->store_id;
        }

        // Cashiers can update their own pending sales
        if ($user->hasRole('cashier') && $sale->status === 'pending') {
            return $user->id === $sale->user_id;
        }

        return false;
    }

    /**
     * Determine if the user can delete the sale.
     */
    public function delete(User $user, Sale $sale): bool
    {
        // Completed sales cannot be deleted, only refunded
        if ($sale->status === 'completed') {
            return false;
        }

        // Super admin can delete pending sales
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Store managers can delete pending sales in their store
        if ($user->hasRole('store-manager') && $sale->status === 'pending') {
            return $user->store_id === $sale->store_id;
        }

        return false;
    }

    /**
     * Determine if the user can refund the sale.
     */
    public function refund(User $user, Sale $sale): bool
    {
        // Only completed sales can be refunded
        if ($sale->status !== 'completed') {
            return false;
        }

        // Super admin can refund any sale
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Store managers can approve refunds in their store
        if ($user->hasRole('store-manager')) {
            return $user->store_id === $sale->store_id;
        }

        return false;
    }

    /**
     * Determine if the user can view sale reports.
     */
    public function viewReports(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'store-manager', 'accountant']);
    }

    /**
     * Determine if the user can export sales data.
     */
    public function export(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'store-manager', 'accountant']);
    }
}
