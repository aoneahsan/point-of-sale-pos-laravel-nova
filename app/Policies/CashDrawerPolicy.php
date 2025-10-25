<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\CashDrawer;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Authorization policy for CashDrawer model.
 *
 * Controls access to cash drawer operations and reconciliation.
 */
class CashDrawerPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any cash drawers.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'store-manager', 'cashier', 'accountant']);
    }

    /**
     * Determine if the user can view the cash drawer.
     */
    public function view(User $user, CashDrawer $cashDrawer): bool
    {
        // Super admin can view all
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Store managers and accountants can view all drawers in their store
        if ($user->hasAnyRole(['store-manager', 'accountant'])) {
            return $user->store_id === $cashDrawer->store_id;
        }

        // Cashiers can view their own drawers
        if ($user->hasRole('cashier')) {
            return $user->id === $cashDrawer->user_id;
        }

        return false;
    }

    /**
     * Determine if the user can create cash drawers (open drawer).
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'store-manager', 'cashier']);
    }

    /**
     * Determine if the user can update the cash drawer.
     */
    public function update(User $user, CashDrawer $cashDrawer): bool
    {
        // Closed drawers cannot be updated
        if ($cashDrawer->status === 'closed') {
            return false;
        }

        // Super admin can update all
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Store managers can update drawers in their store
        if ($user->hasRole('store-manager')) {
            return $user->store_id === $cashDrawer->store_id;
        }

        // Cashiers can update their own open drawers
        if ($user->hasRole('cashier') && $cashDrawer->status === 'open') {
            return $user->id === $cashDrawer->user_id;
        }

        return false;
    }

    /**
     * Determine if the user can close the cash drawer.
     */
    public function close(User $user, CashDrawer $cashDrawer): bool
    {
        // Only open drawers can be closed
        if ($cashDrawer->status !== 'open') {
            return false;
        }

        // Super admin can close any drawer
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Store managers can close drawers in their store
        if ($user->hasRole('store-manager')) {
            return $user->store_id === $cashDrawer->store_id;
        }

        // Cashiers can close their own drawers
        if ($user->hasRole('cashier')) {
            return $user->id === $cashDrawer->user_id;
        }

        return false;
    }

    /**
     * Determine if the user can reconcile the cash drawer.
     */
    public function reconcile(User $user, CashDrawer $cashDrawer): bool
    {
        // Only closed drawers can be reconciled
        if ($cashDrawer->status !== 'closed') {
            return false;
        }

        return $user->hasAnyRole(['super-admin', 'store-manager', 'accountant']) &&
               ($user->hasRole('super-admin') || $user->store_id === $cashDrawer->store_id);
    }

    /**
     * Determine if the user can delete the cash drawer.
     */
    public function delete(User $user, CashDrawer $cashDrawer): bool
    {
        // Only super admin can delete cash drawers
        return $user->hasRole('super-admin');
    }
}
