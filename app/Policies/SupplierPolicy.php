<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Authorization policy for Supplier model.
 *
 * Controls access to supplier management operations.
 */
class SupplierPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any suppliers.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'store-manager', 'inventory-manager']);
    }

    /**
     * Determine if the user can view the supplier.
     */
    public function view(User $user, Supplier $supplier): bool
    {
        // Super admin can view all
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Store users can view all suppliers (multi-store shared suppliers)
        return true;
    }

    /**
     * Determine if the user can create suppliers.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'store-manager', 'inventory-manager']);
    }

    /**
     * Determine if the user can update the supplier.
     */
    public function update(User $user, Supplier $supplier): bool
    {
        // Super admin can update all
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Store managers and inventory managers can update suppliers
        return $user->hasAnyRole(['store-manager', 'inventory-manager']);
    }

    /**
     * Determine if the user can delete the supplier.
     */
    public function delete(User $user, Supplier $supplier): bool
    {
        // Only super admin and store managers can delete suppliers
        return $user->hasAnyRole(['super-admin', 'store-manager']);
    }

    /**
     * Determine if the user can restore the supplier.
     */
    public function restore(User $user, Supplier $supplier): bool
    {
        return $this->delete($user, $supplier);
    }

    /**
     * Determine if the user can permanently delete the supplier.
     */
    public function forceDelete(User $user, Supplier $supplier): bool
    {
        return $user->hasRole('super-admin');
    }
}
